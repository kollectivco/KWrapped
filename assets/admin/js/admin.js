(function ($) {
  function generateId() {
    if (window.crypto && typeof window.crypto.randomUUID === 'function') {
      return window.crypto.randomUUID();
    }

    return 'slide-' + Date.now() + '-' + Math.random().toString(16).slice(2);
  }

  function replaceIndexToken(value, index) {
    if (!value) return value;
    return value.replace(/kt_wrapped_slides\[(\d+|{{INDEX}})\]/g, 'kt_wrapped_slides[' + index + ']')
      .replace(/kt_wrapped_background_(\d+|{{INDEX}})/g, 'kt_wrapped_background_' + index)
      .replace(/kt_wrapped_background_preview_(\d+|{{INDEX}})/g, 'kt_wrapped_background_preview_' + index)
      .replace(/\[(\d+|{{ITEM_INDEX}})\](?=\[(image_id|track_title|artist_name|link|badge)\])/g, '[' + index + ']')
      .replace(/_(\d+|{{ITEM_INDEX}})(?=$)/g, '_' + index);
  }

  function updateSlideSummary(card) {
    const title = card.find('.kt-wrapped-primary-title').val() || 'Untitled Slide';
    const subtitle = card.find('.kt-wrapped-primary-subtitle').val();
    const body = card.find('.kt-wrapped-primary-body').val();
    const summary = subtitle || body || 'Add a short supporting line.';

    card.find('.kt-wrapped-slide-card__title').text(title);
    card.find('.kt-wrapped-slide-card__summary').text(summary.length > 110 ? summary.slice(0, 110) + '…' : summary);
  }

  function updateCollectionIndices(collection) {
    collection.find('[data-kt-collection-item]').each(function (index) {
      const item = $(this);

      item.find('[name]').each(function () {
        const field = $(this);
        field.attr('name', replaceIndexToken(field.attr('name'), index));
      });

      item.find('[id]').each(function () {
        const field = $(this);
        field.attr('id', replaceIndexToken(field.attr('id'), index));
      });

      item.find('[data-target]').each(function () {
        const field = $(this);
        field.attr('data-target', replaceIndexToken(field.attr('data-target'), index));
      });

      item.find('[data-preview]').each(function () {
        const field = $(this);
        field.attr('data-preview', replaceIndexToken(field.attr('data-preview'), index));
      });
    });
  }

  function updateSlideIndices() {
    $('#kt-wrapped-slides-list .kt-wrapped-slide-card').each(function (index) {
      const card = $(this);
      card.attr('data-slide-index', index);
      card.find('.kt-wrapped-slide-number').text(index + 1);
      card.find('.kt-wrapped-slide-number-badge').text(index + 1);
      card.find('.kt-wrapped-order-index').val(index);

      card.find('[name]').each(function () {
        const field = $(this);
        field.attr('name', replaceIndexToken(field.attr('name'), index));
      });

      card.find('[id]').each(function () {
        const field = $(this);
        field.attr('id', replaceIndexToken(field.attr('id'), index));
      });

      card.find('[for]').each(function () {
        const field = $(this);
        field.attr('for', replaceIndexToken(field.attr('for'), index));
      });

      card.find('[data-target]').each(function () {
        const field = $(this);
        field.attr('data-target', replaceIndexToken(field.attr('data-target'), index));
      });

      card.find('[data-preview]').each(function () {
        const field = $(this);
        field.attr('data-preview', replaceIndexToken(field.attr('data-preview'), index));
      });

      card.find('[data-kt-collection]').each(function () {
        updateCollectionIndices($(this));
      });

      updateSlideSummary(card);
    });
  }

  function applyTypePanels(card, type) {
    card.find('.kt-wrapped-slide-config-panel').removeClass('is-active');
    card.find('.kt-wrapped-slide-config-panel[data-slide-type-panel="' + type + '"]').addClass('is-active');

    const guidance = ktWrappedAdmin.slideDefaults.guidance[type];
    if (guidance) {
      card.find('.kt-wrapped-slide-card__guidance').text(guidance);
    }

    const label = card.find('.kt-wrapped-slide-type option:selected').text();
    card.find('.kt-wrapped-slide-type-badge').text(label);
  }

  function applyContentDefaults(card, type) {
    const defaults = ktWrappedAdmin.slideDefaults.content[type];
    if (!defaults) return;

    const title = card.find('.kt-wrapped-primary-title');
    const subtitle = card.find('.kt-wrapped-primary-subtitle');
    const body = card.find('.kt-wrapped-primary-body');

    if (title.length && !title.val()) title.val(defaults.title || '');
    if (subtitle.length && !subtitle.val()) subtitle.val(defaults.subtitle || '');
    if (body.length && !body.val()) body.val(defaults.body_text || '');

    updateSlideSummary(card);
  }

  function openMediaFrame(button) {
    const control = button.closest('.kt-wrapped-media-control');
    const targetId = control.data('target');
    const previewId = control.data('preview');
    const frame = wp.media({
      title: ktWrappedAdmin.mediaTitle,
      button: { text: ktWrappedAdmin.mediaButton },
      multiple: false
    });

    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();
      $('#' + targetId).val(attachment.id);
      $('#' + previewId).html('<img src="' + attachment.url + '" alt="" />');
    });

    frame.open();
  }

  function collapseOtherCards(activeCard) {
    $('#kt-wrapped-slides-list .kt-wrapped-slide-card').not(activeCard).removeClass('is-expanded').addClass('is-collapsed');
    $('#kt-wrapped-slides-list .kt-wrapped-slide-toggle').not(activeCard.find('.kt-wrapped-slide-toggle')).attr('aria-expanded', 'false');
  }

  function toggleCard(card, shouldExpand) {
    const expand = typeof shouldExpand === 'boolean' ? shouldExpand : card.hasClass('is-collapsed');
    card.toggleClass('is-expanded', expand);
    card.toggleClass('is-collapsed', !expand);
    card.find('.kt-wrapped-slide-toggle').attr('aria-expanded', expand ? 'true' : 'false');

    if (expand) {
      collapseOtherCards(card);
    }
  }

  function duplicateCard(card) {
    const clone = card.clone();
    clone.find('input[name$="[id]"]').val(generateId());

    const internalNameField = clone.find('input[name$="[internal_name]"]');
    if (internalNameField.length) {
      const currentValue = internalNameField.val();
      internalNameField.val(currentValue ? currentValue + '-copy' : 'slide-copy');
    }

    clone.removeClass('is-collapsed').addClass('is-expanded');
    clone.find('.kt-wrapped-slide-toggle').attr('aria-expanded', 'true');
    clone.find('.kt-wrapped-slide-advanced').removeClass('is-open');
    clone.find('.kt-wrapped-slide-advanced__toggle').attr('aria-expanded', 'false');
    clone.insertAfter(card);
    updateSlideIndices();
    toggleCard(clone, true);
  }

  $(function () {
    const list = $('#kt-wrapped-slides-list');

    if (list.length) {
      list.sortable({
        handle: '.kt-wrapped-slide-card__handle',
        update: updateSlideIndices
      });
    }

    $(document).on('click', '.kt-wrapped-select-media', function (e) {
      e.preventDefault();
      openMediaFrame($(this));
    });

    $(document).on('click', '[data-kt-collection-add]', function (e) {
      e.preventDefault();
      const button = $(this);
      const target = $('#' + button.attr('data-kt-collection-target'));
      const card = button.closest('.kt-wrapped-slide-card');
      const slideIndex = card.attr('data-slide-index');
      const template = $('#tmpl-kt-wrapped-music-top-card-' + slideIndex).html();
      if (!target.length || !template) return;

      const itemIndex = target.find('[data-kt-collection-item]').length;
      const html = template.replaceAll('{{ITEM_INDEX}}', itemIndex);
      target.append($(html));
      updateCollectionIndices(target);
    });

    $(document).on('click', '[data-kt-collection-remove]', function (e) {
      e.preventDefault();
      const collection = $(this).closest('[data-kt-collection]');
      if (collection.find('[data-kt-collection-item]').length <= 1) {
        return;
      }

      $(this).closest('[data-kt-collection-item]').remove();
      updateCollectionIndices(collection);
    });

    $('#kt-wrapped-add-slide').on('click', function () {
      const template = $('#tmpl-kt-wrapped-slide-card').html();
      const index = $('#kt-wrapped-slides-list .kt-wrapped-slide-card').length;
      const html = template.replaceAll('{{INDEX}}', index);
      const card = $(html);
      $('#kt-wrapped-slides-list').append(card);
      updateSlideIndices();
      toggleCard(card, true);
    });

    $(document).on('click', '.kt-wrapped-remove-slide', function () {
      const cards = $('#kt-wrapped-slides-list .kt-wrapped-slide-card');
      if (cards.length <= 1) {
        return;
      }

      const current = $(this).closest('.kt-wrapped-slide-card');
      const fallback = current.prev('.kt-wrapped-slide-card').length ? current.prev('.kt-wrapped-slide-card') : current.next('.kt-wrapped-slide-card');
      current.remove();
      updateSlideIndices();
      if (fallback.length) toggleCard(fallback, true);
    });

    $(document).on('click', '.kt-wrapped-duplicate-slide', function () {
      duplicateCard($(this).closest('.kt-wrapped-slide-card'));
    });

    $(document).on('change', '.kt-wrapped-slide-type', function () {
      const field = $(this);
      const card = field.closest('.kt-wrapped-slide-card');
      const type = field.val();
      applyTypePanels(card, type);
      applyContentDefaults(card, type);
    });

    $(document).on('input', '.kt-wrapped-primary-title, .kt-wrapped-primary-subtitle, .kt-wrapped-primary-body', function () {
      updateSlideSummary($(this).closest('.kt-wrapped-slide-card'));
    });

    $(document).on('click', '.kt-wrapped-slide-toggle', function (e) {
      e.preventDefault();
      toggleCard($(this).closest('.kt-wrapped-slide-card'));
    });

    $(document).on('click', '.kt-wrapped-slide-advanced__toggle', function (e) {
      e.preventDefault();
      const advanced = $(this).closest('.kt-wrapped-slide-advanced');
      const nextState = !advanced.hasClass('is-open');
      advanced.toggleClass('is-open', nextState);
      $(this).attr('aria-expanded', nextState ? 'true' : 'false');
    });

    $('#kt-wrapped-slides-list .kt-wrapped-slide-card').each(function (index) {
      const card = $(this);
      applyTypePanels(card, card.find('.kt-wrapped-slide-type').val());
      updateSlideSummary(card);

      if (index === 0) {
        toggleCard(card, true);
      } else {
        toggleCard(card, false);
      }
    });

    updateSlideIndices();
  });
})(jQuery);
