(function () {
  const viewer = document.querySelector('.kt-wrapped-viewer');
  if (!viewer) return;

  const slides = Array.from(viewer.querySelectorAll('.kt-wrapped-slide'));
  const segments = Array.from(viewer.querySelectorAll('.kt-wrapped-progress-segment'));
  const toast = viewer.querySelector('[data-kt-toast]');
  const position = viewer.querySelector('[data-kt-position]');
  const hint = viewer.querySelector('[data-kt-hint]');
  const exportCanvas = viewer.querySelector('[data-kt-export-canvas]');
  const shareSheet = viewer.querySelector('[data-kt-share-sheet]');
  const shareSheetTitle = viewer.querySelector('[data-kt-share-sheet-title]');
  const shareSheetBody = viewer.querySelector('[data-kt-share-sheet-body]');
  const shareSheetActions = viewer.querySelector('[data-kt-share-sheet-actions]');
  if (!slides.length) return;

  let index = 0;
  let autoplayTimer = null;
  let touchStartX = 0;
  let isPaused = false;
  let hintTimer = null;
  let isBusy = false;
  let finaleTimer = null;

  function showToast(message) {
    if (!toast) return;
    toast.hidden = false;
    toast.textContent = message;
    clearTimeout(showToast.timer);
    showToast.timer = setTimeout(() => {
      toast.hidden = true;
    }, 2600);
  }

  function openShareSheet(title, body, actionsMarkup) {
    if (!shareSheet) return;
    shareSheet.hidden = false;
    shareSheetTitle.textContent = title;
    shareSheetBody.textContent = body;
    shareSheetActions.innerHTML = actionsMarkup || '<button type="button" class="kt-wrapped-ghost" data-kt-share-sheet-close>Close</button>';
  }

  function closeShareSheet() {
    if (!shareSheet) return;
    shareSheet.hidden = true;
  }

  function setBusyState(busy, message) {
    isBusy = busy;
    viewer.classList.toggle('is-busy', busy);
    viewer.querySelectorAll('[data-kt-action], [data-kt-button]').forEach((button) => {
      button.disabled = busy;
      button.setAttribute('aria-busy', busy ? 'true' : 'false');
    });

    if (busy && message) {
      openShareSheet('Preparing your story card…', message, '<button type="button" class="kt-wrapped-ghost" disabled>Working…</button>');
    }
  }

  function getCurrentSlide() {
    return slides[index];
  }

  function getCurrentDuration() {
    const current = getCurrentSlide();
    return current ? parseInt(current.dataset.slideDuration || '6500', 10) : 6500;
  }

  function syncControls() {
    viewer.classList.toggle('is-first-slide', index === 0);
    viewer.classList.toggle('is-last-slide', index === slides.length - 1);

    if (position) {
      position.textContent = (index + 1) + ' / ' + slides.length;
    }
  }

  function syncProgress() {
    viewer.style.setProperty('--kt-progress-duration', getCurrentDuration() + 'ms');

    segments.forEach((segment, segmentIndex) => {
      segment.classList.toggle('is-active', segmentIndex === index);
      segment.classList.toggle('is-complete', segmentIndex < index);
      segment.classList.remove('is-restarting');

      if (segmentIndex === index) {
        void segment.offsetWidth;
        segment.classList.add('is-restarting');
      }
    });
  }

  function preloadNearby(currentIndex) {
    [currentIndex - 1, currentIndex + 1].forEach((candidateIndex) => {
      const candidate = slides[candidateIndex];
      if (!candidate) return;

      const background = candidate.querySelector('.kt-wrapped-slide__bg');
      if (!background) return;

      const style = window.getComputedStyle(background);
      const image = style.backgroundImage;
      const match = image && image.match(/url\(["']?(.*?)["']?\)/);
      if (!match || !match[1]) return;

      const img = new Image();
      img.src = match[1];
    });
  }

  function startAutoplay() {
    clearTimeout(autoplayTimer);
    if (isPaused || isBusy || slides.length <= 1) return;

    autoplayTimer = setTimeout(() => {
      if (index < slides.length - 1) {
        next();
      }
    }, getCurrentDuration());
  }

  function setPaused(paused) {
    isPaused = paused;
    viewer.classList.toggle('is-paused', paused);

    if (paused) {
      clearTimeout(autoplayTimer);
      return;
    }

    startAutoplay();
  }

  function hideHintSoon() {
    if (!hint) return;

    clearTimeout(hintTimer);
    hintTimer = setTimeout(() => {
      hint.classList.add('is-hidden');
    }, 2200);
  }

  function render(nextIndex) {
    const previousIndex = index;
    index = Math.max(0, Math.min(nextIndex, slides.length - 1));
    viewer.dataset.direction = index >= previousIndex ? 'forward' : 'backward';

    slides.forEach((slide, slideIndex) => {
      slide.classList.toggle('is-active', slideIndex === index);
      slide.classList.toggle('is-before', slideIndex < index);
      slide.classList.toggle('is-after', slideIndex > index);
    });

    syncControls();
    syncProgress();
    preloadNearby(index);
    applySlideMood();
    startAutoplay();
  }

  function applySlideMood() {
    clearTimeout(finaleTimer);
    viewer.classList.remove('is-finale-settled');

    const current = getCurrentSlide();
    if (!current) return;

    const type = current.dataset.slideType || '';
    viewer.dataset.activeType = type;

    if (type === 'final_share') {
      finaleTimer = setTimeout(() => {
        viewer.classList.add('is-finale-settled');
      }, 950);
    }
  }

  function next() {
    if (index < slides.length - 1) {
      hideHintSoon();
      render(index + 1);
    }
  }

  function prev() {
    if (index > 0) {
      hideHintSoon();
      render(index - 1);
    }
  }

  function waitForImages(scope) {
    const imageNodes = Array.from(scope.querySelectorAll('img'));
    const backgroundNodes = Array.from(scope.querySelectorAll('.kt-wrapped-slide__bg'));
    const imagePromises = imageNodes.map((node) => {
      if (node.complete) return Promise.resolve();
      return new Promise((resolve) => {
        node.addEventListener('load', resolve, { once: true });
        node.addEventListener('error', resolve, { once: true });
      });
    });

    const backgroundPromises = backgroundNodes.map((node) => {
      const style = window.getComputedStyle(node);
      const match = style.backgroundImage && style.backgroundImage.match(/url\(["']?(.*?)["']?\)/);
      if (!match || !match[1]) return Promise.resolve();

      return new Promise((resolve) => {
        const img = new Image();
        img.onload = resolve;
        img.onerror = resolve;
        img.crossOrigin = 'anonymous';
        img.src = match[1];
      });
    });

    return Promise.all(imagePromises.concat(backgroundPromises));
  }

  async function exportSlide(slide) {
    if (!exportCanvas) {
      throw new Error('Export canvas is unavailable.');
    }

    const clone = slide.cloneNode(true);
    clone.classList.add('is-exporting', 'is-active');
    clone.classList.remove('is-before', 'is-after');
    clone.querySelectorAll('[data-kt-action], [data-kt-button]').forEach((node) => node.remove());

    exportCanvas.innerHTML = '';
    exportCanvas.appendChild(clone);

    await Promise.all([
      document.fonts && document.fonts.ready ? document.fonts.ready.catch(() => undefined) : Promise.resolve(),
      waitForImages(clone)
    ]);

    const serialized = new XMLSerializer().serializeToString(exportCanvas);
    const svg = `
      <svg xmlns="http://www.w3.org/2000/svg" width="1080" height="1920">
        <foreignObject width="100%" height="100%">${serialized}</foreignObject>
      </svg>
    `;
    const blob = new Blob([svg], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(blob);

    try {
      const image = await new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = url;
      });

      const canvas = document.createElement('canvas');
      canvas.width = 1080;
      canvas.height = 1920;
      const ctx = canvas.getContext('2d');
      if (!ctx) {
        throw new Error('Canvas is not available.');
      }

      ctx.fillStyle = '#090b13';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(image, 0, 0);

      const blobPng = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png', 1));
      if (!blobPng) {
        throw new Error('Image export failed.');
      }

      return blobPng;
    } finally {
      URL.revokeObjectURL(url);
      exportCanvas.innerHTML = '';
    }
  }

  function downloadBlob(blob) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'kontentainment-wrapped-slide-' + (index + 1) + '.png';
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
  }

  async function saveCurrentSlide(existingBlob) {
    const slide = getCurrentSlide();
    if (!slide || slide.dataset.exportEnabled !== '1') {
      showToast('Saving is not enabled for this slide.');
      return null;
    }

    try {
      setBusyState(true, 'We are rendering a 1080 x 1920 story card for your camera roll.');
      const blob = existingBlob || await exportSlide(slide);
      downloadBlob(blob);
      openShareSheet(
        'Saved to your device',
        'Your story card is ready. Open Instagram Stories and upload it from your recent photos.',
        '<button type="button" class="kt-wrapped-cta" data-kt-share-sheet-close>Done</button>'
      );
      showToast(window.ktWrappedViewer?.downloadMessage || 'Image downloaded.');
      return blob;
    } catch (error) {
      openShareSheet(
        'We could not export this slide',
        'Try again on a modern mobile browser, or save from desktop and move it to your phone.',
        '<button type="button" class="kt-wrapped-ghost" data-kt-share-sheet-close>Close</button>'
      );
      showToast('Unable to save this slide on this device.');
      return null;
    } finally {
      setBusyState(false);
    }
  }

  async function shareCurrentSlide() {
    const slide = getCurrentSlide();
    if (!slide || slide.dataset.shareEnabled !== '1') {
      showToast('Sharing is not enabled for this slide.');
      return;
    }

    try {
      setBusyState(true, 'Preparing a share-ready image file for your phone.');
      const blob = await exportSlide(slide);
      const file = new File([blob], 'kontentainment-wrapped.png', { type: 'image/png' });
      const shareText = viewer.dataset.shareText || '';

      if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
        await navigator.share({
          text: shareText,
          files: [file]
        });
        openShareSheet(
          'Shared successfully',
          'Your story card was handed off to the native share sheet.',
          '<button type="button" class="kt-wrapped-cta" data-kt-share-sheet-close>Done</button>'
        );
        return;
      }

      await saveCurrentSlide(blob);
      openShareSheet(
        'Saved instead of shared',
        'Native file sharing is not available here, so we saved the image for manual upload to Instagram Stories.',
        '<button type="button" class="kt-wrapped-cta" data-kt-share-sheet-close>Got it</button>'
      );
      showToast(window.ktWrappedViewer?.shareMessage || 'Downloaded instead of sharing.');
    } catch (error) {
      const saved = await saveCurrentSlide();
      if (!saved) {
        showToast('Unable to share this slide on this device.');
      }
    } finally {
      setBusyState(false);
    }
  }

  viewer.addEventListener('click', (event) => {
    const navZone = event.target.closest('[data-kt-nav]');
    const actionButton = event.target.closest('[data-kt-action]');
    const navButton = event.target.closest('[data-kt-button]');

    if (navZone) {
      hideHintSoon();
      navZone.dataset.ktNav === 'next' ? next() : prev();
      return;
    }

    if (navButton) {
      hideHintSoon();
      const action = navButton.dataset.ktButton;
      if (action === 'next') next();
      if (action === 'prev') prev();
      return;
    }

    if (actionButton) {
      hideHintSoon();
      const action = actionButton.dataset.ktAction;
      if (action === 'save') saveCurrentSlide();
      if (action === 'share') shareCurrentSlide();
      if (action === 'replay') {
        viewer.classList.remove('is-finale-settled');
        render(0);
      }
    }
  });

  document.addEventListener('click', (event) => {
    if (event.target.closest('[data-kt-share-sheet-close]')) {
      closeShareSheet();
      return;
    }

    if (shareSheet && !shareSheet.hidden && event.target === shareSheet) {
      closeShareSheet();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (shareSheet && !shareSheet.hidden && event.key === 'Escape') {
      closeShareSheet();
      return;
    }

    if (event.key === 'ArrowRight') next();
    if (event.key === 'ArrowLeft') prev();
  });

  viewer.addEventListener('touchstart', (event) => {
    touchStartX = event.changedTouches[0].clientX;
    setPaused(true);
  });

  viewer.addEventListener('touchend', (event) => {
    const delta = event.changedTouches[0].clientX - touchStartX;
    if (Math.abs(delta) > 45) {
      if (delta < 0) next();
      if (delta > 0) prev();
    }
    setPaused(false);
  });

  viewer.addEventListener('pointerdown', () => {
    setPaused(true);
  });

  viewer.addEventListener('pointerup', () => {
    setPaused(false);
  });

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      setPaused(true);
      return;
    }

    setPaused(false);
  });

  window.ktWrappedStory = {
    saveCurrentSlide,
    shareCurrentSlide
  };

  hideHintSoon();
  render(0);
})();
