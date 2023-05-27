
var pwbeUndoHistory = [];
var pwbeRedoHistory = [];

jQuery(function($) {
    $.contextMenu({
        selector: '.pwbe-header',
        callback: function(key, options) {
            var header = $(this).closest('.pwbe-header');
            var fieldName = header.text();

            switch (key) {
                case 'edit':
                    var contentName = header.attr('data-type');

                    var dialog = pwbeShowDialog('bulkedit', contentName, $(this), {
                        'data-field': header.attr('data-field'),
                        'data-field-name': fieldName
                    });

                    dialog.find('.pwbe-bulkedit-field-name').text(fieldName);
                    $('#pwbe-bulkedit-dialog').focus();

                break;

                case 'sort_text_asc':
                case 'sort_text_desc':
                case 'sort_number_asc':
                case 'sort_number_desc':

                    $('#pwbe-order-by').val(header.attr('data-field'));
                    if (key.slice(-4) == 'desc') {
                        $('#pwbe-order-by-desc').val('DESC');
                    } else {
                        $('#pwbe-order-by-desc').val('');
                    }

                    $('#pwbe-filters-form').submit();
                break;

                case 'hide':
                    var viewName = $('#pwbe-view').val();

                    if (viewName.startsWith('pwbeview_')) {
                        viewName = prompt(pwbe.i18n.view_name_prompt, '');
                    }

                    if (viewName) {
                        // Hide the column.
                        $('.pwbe-results-table-header-td').filter(function() { return $(this).attr('data-field') == header.attr('data-field'); }).addClass('pwbe-hidden').addClass('pwbe-hidden-column');
                        $('.pwbe-results-table-cell-td').filter(function() { return $(this).attr('data-field') == header.attr('data-field'); }).addClass('pwbe-hidden');

                        pwbeSaveCurrentView(viewName);
                        $('#pwbe-view').val(viewName);
                        $('#pwbe-view-edit, #pwbe-view-delete').removeClass('pwbe-hidden');
                    }

                break;

                default:
                    alert('Unhandled menu: ' + key);
                break;
            }
        },
        trigger: 'left',
        items: {
            "edit": {name: pwbe.i18n.editAllCheckedProducts, icon: "fa-pencil-square-o", visible: function(key, opt) { return ($(this).attr('data-readonly') != 'true'); } },
            separator1: { "type": "cm_separator", visible: function(key, opt) { return ($(this).attr('data-readonly') != 'true' && $(this).attr('data-sortable') == 'true'); } },
            "sort_text_asc": {name: pwbe.i18n.sortAscending, icon: "fa-sort-amount-asc", visible: function(key, opt) { return ($(this).attr('data-sortable') == 'true'); } },
            "sort_text_desc": {name: pwbe.i18n.sortDescending, icon: "fa-sort-amount-desc", visible: function(key, opt) { return ($(this).attr('data-sortable') == 'true'); } },
            separator2: { "type": "cm_separator", visible: function(key, opt) { return ($(this).attr('data-readonly') != 'true' || $(this).attr('data-sortable') == 'true'); } },
            "hide": {name: pwbe.i18n.hideColumn, icon: "fa-eye-slash" }
        }
    });
});

var lastChecked = null;

function pwbeSelectRow(row) {
    var checkbox = row.find('.pwbe-product-checkbox');
    if (checkbox.prop('checked')) {
        row.addClass('pwbe-product-tr-selected');
    } else {
        row.removeClass('pwbe-product-tr-selected');
    }
}

function pwbeBindCheckallCheckbox() {
    jQuery('.pwbe-checkall').off('change.pwbe').on('change.pwbe', function () {
        jQuery('.pwbe-product-checkbox').each(function(index) {
            var checkbox = jQuery(this);
            checkbox.prop('checked', !checkbox.prop('checked'));
            pwbeSelectRow(checkbox.closest('.pwbe-product-tr'));
        });
    });
}

function pwbeSearchResults() {

    jQuery('.pwbe-product-checkbox').change(function() {
        var allChecked = (jQuery('.pwbe-product-checkbox:checked').length == jQuery('.pwbe-product-checkbox').length);
        jQuery('.pwbe-checkall').prop('checked', allChecked);
    });

    jQuery('.pwbe-product-checkbox').click(function(e) {
        pwbeSelectRow(jQuery(this).closest('.pwbe-product-tr'));

        if (!lastChecked) {
            lastChecked = this;
            return;
        }

        if (e.shiftKey) {
            var start = jQuery('.pwbe-product-checkbox').index(this);
            var end = jQuery('.pwbe-product-checkbox').index(lastChecked);

            jQuery('.pwbe-product-checkbox').slice(Math.min(start,end), Math.max(start,end)+ 1).each(function(index) {
                var checkbox = jQuery(this);
                checkbox.prop('checked', lastChecked.checked);
                pwbeSelectRow(checkbox.closest('.pwbe-product-tr'));
            });
        }

        lastChecked = this;
    });

    pwbeBindCheckallCheckbox();

    jQuery('#pwbe-bulkedit-dialog').keydown(function(e) {
        if (e.keyCode == 27) {
            jQuery('#pwbe-bulkedit-dialog-button-cancel').trigger('click');
            e.preventDefault();
            return false;
        }
    });

    jQuery('#pwbe-bulkedit-dialog-button-apply').click(function(e) {
        jQuery(this).text('Processing...').prop('disabled', true);
        pwbeBulkEditDialogApply();
        jQuery(this).text('Apply').prop('disabled', false);
        e.preventDefault();
        return false;
    });

    jQuery('#pwbe-bulkedit-dialog-button-cancel').click(function(e) {
        pwbeBulkEditDialogClose();
        e.preventDefault();
        return false;
    });

    jQuery('.pwbe-header').hover(
        function() { jQuery(this).addClass('pwbe-thead-mouseover').prepend('<i class="fa fa-magic fa-fw pwbe-thead-mouseover-icon" aria-hidden="true"></i>'); },
        function() { jQuery(this).removeClass('pwbe-thead-mouseover').find('.pwbe-thead-mouseover-icon').remove(); }
    );

    jQuery('.pwbe-field-variation').hover(
        function() { jQuery(this).addClass('pwbe-tbody-mouseover').prepend('<i class="fa fa-child fa-fw pwbe-tbody-mouseover-icon" aria-hidden="true"></i>'); },
        function() { jQuery(this).removeClass('pwbe-tbody-mouseover').find('.pwbe-tbody-mouseover-icon').remove(); }
    );

    jQuery('.pwbe-field').not('.pwbe-field-readonly').children('.pwbe-field-label').hover(
        function() { jQuery(this).addClass('pwbe-tbody-mouseover').prepend('<span class="fa fa-pencil-square-o fa-fw pwbe-tbody-mouseover-icon" aria-hidden="true"></span>'); },
        function() { jQuery(this).removeClass('pwbe-tbody-mouseover').parent().find('.pwbe-tbody-mouseover-icon').remove(); }
    );

    jQuery('.pwbe-field-label').click(function() {
        if (jQuery(this).closest('.pwbe-field').hasClass('pwbe-field-readonly')) { return false; }

        // Close out any open editors.
        var valid = true;
        jQuery('.pwbe-temporary-editor-container').each(function() {
            if (!pwbeAcceptFieldEdit(jQuery(this))) {
                valid = false;
            }
        });
        if (!valid) {
            return false;
        }

        var text = jQuery(this);
        text.css('display', 'none');

        var cell = text.closest('.pwbe-td');
        var hiddenInput = text.siblings('.pwbe-field-value').first();
        var editorContainer = jQuery('<div class="pwbe-temporary-editor-container"></div>');
        var input;
        var inputType = hiddenInput.attr('data-input-type');
        var fieldName = hiddenInput.attr('data-field');
        var productType = hiddenInput.attr('data-product-type');


        editorContainer.insertAfter(hiddenInput);

        if (pwbe.editingRowBorder == 'yes') {
            jQuery(editorContainer).closest('.pwbe-tr').addClass('pwbe-tr-editing');
        }

        cell.removeClass('pwbe-results-table-td');

        switch (inputType) {
            case 'currency':
                input = jQuery('<input type="text" autocomplete="off" />');
                input.val(hiddenInput.val());
            break;

            case 'checkbox':
                input = jQuery('<input type="checkbox" value="yes" />');
                input.prop('checked', (hiddenInput.val() == 'yes'));
            break;

            case 'textarea':
                input = jQuery('<textarea rows="8" cols="45">' + hiddenInput.val() + '</textarea>');
            break;

            case 'select':
                input = jQuery('<select style="width: 300px;"></select>');
                jQuery('.pwbe-dropdown-template-' + fieldName + ' option, .pwbe-dropdown-template-' + fieldName + ' option').clone().appendTo(input);
                input.attr('data-select2', jQuery('.pwbe-dropdown-template-' + fieldName).attr('data-select2'));
                input.val(hiddenInput.val());
            break;

            case 'multiselect':
                input = jQuery('<select style="width: 300px;" multiple="multiple"></select>');
                jQuery('.pwbe-dropdown-template-' + fieldName + ' option, .pwbe-dropdown-template-' + fieldName + ' option').clone().appendTo(input);
                input.val(hiddenInput.val().split(','));
            break;

            case 'image':
                input = jQuery('<input type="hidden" id="pwbe-image-input" value="' + hiddenInput.val() + '">');
                var mediaButton = jQuery('<button class="button">' + pwbe.i18n.select_image + '</button>');
                mediaButton.click(function(e) {
                    var image_frame;
                    if (image_frame) {
                        image_frame.open();
                    }

                    // Define image_frame as wp.media object
                    image_frame = wp.media({
                        title: pwbe.i18n.select_image,
                        multiple : false,
                        library : {
                            type : 'image',
                        }
                    });

                    image_frame.on('close',function() {
                        // On close, get selections and save to the hidden input
                        // plus other AJAX stuff to refresh the image preview
                        var selection =  image_frame.state().get('selection');
                        var gallery_ids = new Array();
                        var my_index = 0;
                        selection.each(function(attachment) {
                            gallery_ids[my_index] = attachment['id'];
                            my_index++;
                        });
                        var ids = gallery_ids.join(",");
                        input.val(ids);
                        pwbeAcceptFieldEdit(editorContainer);
                    });

                    image_frame.on('open',function() {
                        // On open, get the id from the hidden input
                        // and select the appropiate images in the media manager
                        var selection =  image_frame.state().get('selection');
                        var ids = hiddenInput.val().split(',');
                        ids.forEach(function(id) {
                            var attachment = wp.media.attachment(id);
                            attachment.fetch();
                            selection.add( attachment ? [ attachment ] : [] );
                        });
                    });

                    image_frame.open();

                    e.preventDefault();
                    return false;
                });

                var removeButton = jQuery('<button class="button pwbe-field-edit-button">' + pwbe.i18n.remove_image + '</button>');
                removeButton.click(function(e) {
                    input.val('');
                    pwbeAcceptFieldEdit(editorContainer);
                    e.preventDefault();
                    return false;
                });

                mediaButton.appendTo(editorContainer);
                removeButton.appendTo(editorContainer);
            break;

            default:
                input = jQuery('<input type="' + inputType + '" autocomplete="off" />');
                if (inputType == 'text') {
                    input.css('width', '500px');
                }
                input.val(hiddenInput.val());
            break;
        }

        input.appendTo(editorContainer);
        input.css('position', 'relative').css('z-index', '1');
        input.addClass('pwbe-temporary-editor');
        input.attr('data-old', hiddenInput.val());
        input.attr('data-field', hiddenInput.attr('data-field'));


        // Hide unavailable options (selects).
        if (input.prop('type') == 'select-one' || input.prop('type') == 'select-multiple') {
            if (productType == 'variation') {
                input.find('.pwbe-dropdown-visibility-parent').remove();
            } else {
                input.find('.pwbe-dropdown-visibility-variation').remove();
            }
        }

        var acceptButton = jQuery('<div class="button button-secondary pwbe-field-edit-button pwbe-field-edit-button-accept" title="' + pwbe.i18n.acceptChanges + '"><i class="fa fa-check fa-fw pwbe-field-edit-button-accept-icon"></i></div>');
        acceptButton.insertAfter(input);
        acceptButton.click(function(e) {
            pwbeAcceptFieldEdit(editorContainer);
            e.preventDefault();
            return false;
        });

        var cancelButton = jQuery('<div class="button button-secondary pwbe-field-edit-button pwbe-field-edit-button-cancel" title="' + pwbe.i18n.cancelChanges + '"><i class="fa fa-times fa-fw pwbe-field-edit-button-cancel-icon"></i></div>');
        cancelButton.insertAfter(acceptButton);
        cancelButton.click(function(e) {
            cell.addClass('pwbe-results-table-td');
            var hiddenInput = editorContainer.siblings('.pwbe-field-value');
            var input = editorContainer.find('.pwbe-temporary-editor');
            pwbeFieldUpdate(hiddenInput, input.attr('data-old'), true);
            pwbeCloseTemporaryEditor();
            e.preventDefault();
            return false;
        });

        if (hiddenInput.val() != hiddenInput.attr('data-original-value')) {
            var resetButton = jQuery('<div class="button button-secondary pwbe-field-edit-button pwbe-field-edit-button-reset" title="' + pwbe.i18n.revertToOriginal + '"><i class="fa fa-refresh fa-fw"></i></div>');
            resetButton.insertAfter(cancelButton);
            resetButton.click(function(e) {
                cell.addClass('pwbe-results-table-td');
                var hiddenInput = editorContainer.siblings('.pwbe-field-value');
                pwbeFieldUpdate(hiddenInput, hiddenInput.attr('data-original-value'));
                pwbeCloseTemporaryEditor();
                e.preventDefault();
                return false;
            });
        }

        if (inputType == 'image') {
            acceptButton.hide();
        }

        input.keydown(function(e) {
            var input = jQuery(this);
            var handled = false;

            switch (e.keyCode) {
                case 9: // Tab
                    var hiddenInput = editorContainer.siblings('.pwbe-field-value');
                    if (pwbeAcceptFieldEdit(editorContainer)) {
                        if (e.shiftKey) {
                            pwbeFieldMove(hiddenInput, 'left');
                        } else {
                            pwbeFieldMove(hiddenInput, 'right');
                        }
                    }
                    handled = true;
                break;

                case 13: // Enter
                    if (!input.is('textarea')) {
                        var hiddenInput = editorContainer.siblings('.pwbe-field-value');
                        if (pwbeAcceptFieldEdit(editorContainer)) {
                            if (e.shiftKey) {
                                pwbeFieldMove(hiddenInput, 'up');
                            } else {
                                pwbeFieldMove(hiddenInput, 'down');
                            }
                        }
                        handled = true;
                    }
                break;

                case 27: // Escape
                    input.siblings('.pwbe-field-edit-button-cancel').click();
                    handled = true;
                break;
            }

            if (handled) {
                e.preventDefault();
                return false;
            }
        });

        if (inputType == 'select' && input.attr('data-select2')) {
            var displayName = jQuery('#pwbe-header-results .pwbe-results-table-header-td').filter(function() { return jQuery(this).attr('data-field') == fieldName; } ).find('.pwbe-header').text();
            input.pwbeselect2({ placeholder: pwbe.i18n.select + ' ' + displayName + '...', allowClear: true });
        }

        if (inputType == 'multiselect') {
            var displayName = jQuery('#pwbe-header-results .pwbe-results-table-header-td').filter(function() { return jQuery(this).attr('data-field') == fieldName; } ).find('.pwbe-header').text();
            if (pwbe.select2copypaste) {
                input.pwbeselect2({ placeholder: pwbe.i18n.select + ' ' + displayName + '...', tokenSeparators: [','], tags: true, multiple: true });
            } else {
                input.pwbeselect2({ placeholder: pwbe.i18n.select + ' ' + displayName + '...' });
            }

            jQuery('.pwbeselect2-search__field').keydown(function(e) {
                var handled = false;

                switch (e.keyCode) {
                    case 9:
                        input.pwbeselect2('close');

                        var selectBox = jQuery(this).closest('.pwbeselect2');
                        var hiddenInput = jQuery(this).closest('.pwbe-temporary-editor-container').siblings('.pwbe-field-value');

                        if (pwbeAcceptFieldEdit(editorContainer)) {
                            if (e.shiftKey) {
                                pwbeFieldMove(hiddenInput, 'left');
                            } else {
                                pwbeFieldMove(hiddenInput, 'right');
                            }
                            handled = true;
                        }
                    break;
                }

                if (handled) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        input.focus();
    });

    jQuery('.pwbe-field-variation').click(function() {
        var postId = jQuery(this).attr('data-post-id');
        var parent = jQuery("#pwbe-product-" + postId).closest('.pwbe-tr');

        parent.get(0).scrollIntoView(true);

        // Account for the fixed header.
        var scrolledY = window.scrollY;
        var header = jQuery('#wpadminbar');
        if (scrolledY && header) {
            window.scroll(0, scrolledY - (header.height() * 2));
        }

        parent.pwbe_highlight();
    });

    jQuery('#pwbe-product-save-button').click(function(e) {
        var valid = true;
        jQuery('.pwbe-temporary-editor-container').each(function() {
            if (!pwbeAcceptFieldEdit(jQuery(this))) {
                valid = false;
            }
        });
        if (valid) {
            pwbeSaveFields();
        }

        e.preventDefault();
        return false;
    });

    jQuery('#pwbe-product-fix-attributes-button').click(function(e) {
        pwbeFixAttributes();
        e.preventDefault();
        return false;
    });

    jQuery('#pwbe-product-undo-button').click(function(e) {
        jQuery('.pwbe-field-edit-button-cancel').click();

        pwbeUndo();

        e.preventDefault();
        return false;
    });

    jQuery('#pwbe-product-redo-button').click(function(e) {
        jQuery('.pwbe-field-edit-button-cancel').click();

        pwbeRedo();

        e.preventDefault();
        return false;
    });

    jQuery('#pwbe-product-discard-button').click(function(e) {
        jQuery('.pwbe-field-edit-button-cancel').click();

        if (confirm(pwbe.i18n.discardAllChanges)) {
            var fields = jQuery('.pwbe-field-changed');
            for (var i = 0, len = fields.length; i < len; i++) {
                var field = jQuery(fields[i]);

                var hiddenInput = field.find('.pwbe-field-value').first();
                var originalValue = hiddenInput.attr('data-original-value');

                pwbeFieldUpdate(hiddenInput, originalValue, true);
            }

            pwbeUndoHistory = [];
            pwbeRedoHistory = [];

            pwbeProductButtonsEnableDisable();
            jQuery('#pwbe-message').html('');
        }

        e.preventDefault();
        return false;
    });

    jQuery('#pwbe-view').change(function() {
        jQuery('body').css('cursor', 'wait');

        var viewName = jQuery(this).val();

        jQuery.post(ajaxurl, {
            'action': 'pwbe_set_current_view',
            'name': viewName
        }, function(data) {
            jQuery('body').css('cursor', 'default');
            jQuery('#pwbe-filters-form').submit();
        });
    });

    jQuery('#pwbe-view-edit').click(function() {
        pwbeShowDialog('edit-view', 'edit-view');
    });

    jQuery('#pwbe-view-copy').click(function() {
        var viewName = prompt(pwbe.i18n.view_name_prompt, '');
        if (viewName) {

            var viewExists = false;
            jQuery('#pwbe-view option').each(function() {
                if (this.value == viewName) {
                    viewExists = true;
                    return;
                }
            });

            if ( viewExists && !confirm(pwbe.i18n.overwrite_view_prompt)) {
                return;
            }

            pwbeSaveCurrentView(viewName);
            jQuery('#pwbe-view').val(viewName);
            jQuery('#pwbe-view-edit, #pwbe-view-delete').removeClass('pwbe-hidden');
        }
    });

    jQuery('#pwbe-edit-view-dialog-button-cancel').click(function(e) {
        pwbeEditViewDialogClose();
    });

    jQuery('#pwbe-view-delete').click(function() {

        var viewName = jQuery('#pwbe-view').val();

        if (viewName.startsWith('pwbeview_')) {
            return;
        }

        if (!confirm(pwbe.i18n.confirmDeleteView + ' "' + viewName + '"')) {
            return;
        }

        jQuery('body').css('cursor', 'wait');

        jQuery.post(ajaxurl, {
            'action': 'pwbe_delete_view',
            'name': viewName
        }, function(data) {
            jQuery('body').css('cursor', 'default');
            jQuery('#pwbe-filters-form').submit();
        });
    });

    var resultsHeader = jQuery('#pwbe-header-results');
    if (resultsHeader.length > 0) {
        var adminBarHeight = jQuery('#wpadminbar').height();
        var header = resultsHeader.find('.pwbe-tr').clone();
        var fixedHeader = jQuery('#pwbe-header-fixed').css('top', adminBarHeight + 'px').append(header);
        var offsetTop = resultsHeader.offset().top - adminBarHeight;
        var offsetLeft = resultsHeader.offset().left;

        pwbeResizeFixedHeaderColumns();
        pwbeBindCheckallCheckbox();

        jQuery(window).bind('scroll', function() {
            fixedHeader.css('left', offsetLeft - jQuery(window).scrollLeft());

            var offset = jQuery(this).scrollTop();

            if (offset >= offsetTop && fixedHeader.is(':hidden')) {
                fixedHeader.show();
            } else if (offset < offsetTop) {
                fixedHeader.hide();
            }
        });
    }
}

function pwbeResizeFixedHeaderColumns() {
    jQuery('#pwbe-header-fixed').width(jQuery('#pwbe-header-results').width());
    jQuery('#pwbe-header-fixed .pwbe-results-table-header-td').each(function(index) {
        jQuery(this).width(jQuery('#pwbe-header-results .pwbe-results-table-header-td').eq(index).width());
    });
}

function pwbeSaveFields() {
    var valid = true;
    var fields = jQuery('.pwbe-filter-field');
    for(var i = 0, len = fields.length; i < len; i++) {
        var field = jQuery(fields[i]);
        if ( field.is(':visible') && !field.val() ) {
            field.closest('.pwbe-filter-row').find('.pwbe-filter-required').css('display', 'block');
            valid = false;
        } else {
            field.closest('.pwbe-filter-row').find('.pwbe-filter-required').css('display', 'none');
        }
    }

    if (!valid) {
        return false;
    }

    jQuery('body').css('cursor', 'wait');
    jQuery('#pwbe-results-error').html('').addClass('pwbe-hidden');
    jQuery('.pwbe-processing-message').text(pwbe.i18n.saving + '...');
    jQuery('.pwbe-processing').css('display', 'block');
    jQuery('#pwbe-results-container').css('display', 'none');

    var changedFields = jQuery('.pwbe-field-changed');

    var autoCreateVariations = 0;
    if (jQuery('#pwbe-auto-create-variations').is(':checked')) {
        autoCreateVariations = 1;
    }

    // Start the saving loop
    pwbeSaveField(0, changedFields, autoCreateVariations);

    pwbeUndoHistory = [];
    pwbeRedoHistory = [];
}

function pwbeSaveField(index, values, autoCreateVariations) {
    if (index < values.length) {
        var fields = [];

        for (var x = 0; x < parseInt(pwbe.saveBatchSize) && x < values.length; x++) {
            var input = jQuery(values[index + x]).find('input:first');
            var displayName = jQuery('#pwbe-header-results .pwbe-results-table-header-td').filter(function() { return jQuery(this).attr('data-field') == input.attr('data-field'); } ).find('.pwbe-header').text();
            fields.push({
                'post_id': input.attr('data-post-id'),
                'parent_post_id': input.attr('data-parent-post-id'),
                'product_type': input.attr('data-product-type'),
                'field': input.attr('data-field'),
                'value': input.val(),
                'old_value': input.attr('data-original-value'),
                'display_name': displayName
            });
        }

        var current_record = (index + fields.length);
        if (current_record > values.length) { current_record = values.length; }
        jQuery('.pwbe-processing-message').text(pwbe.i18n.saving + ' ' + current_record + ' of ' + values.length);

        jQuery.post(ajaxurl, { 'action': 'pwbe_save_products', 'fields': fields, 'security': pwbe.nonces.save_products, 'auto_create_variations': autoCreateVariations }, function(response) {
            if (response.includes('success')) {
                // Save the next product.
                pwbeSaveField(index + parseInt(pwbe.saveBatchSize), values, autoCreateVariations);
            } else {
                jQuery('#pwbe-results-error').html('RESPONSE: ' + response).removeClass('pwbe-hidden');
                jQuery('.pwbe-processing-message').text('');
                jQuery('.pwbe-processing').css('display', 'none');
                jQuery('#pwbe-results-container').css('display', 'block');
                jQuery('body').css('cursor', 'default');
            }
        }).fail(function() {
            jQuery.get(ajaxurl, { 'action': 'pwbe_get_save_products_error' }, function(response) {

                jQuery('#pwbe-results-error').html('SERVER ERROR ' + response).removeClass('pwbe-hidden');
                jQuery('.pwbe-processing-message').text('');
                jQuery('.pwbe-processing').css('display', 'none');
                jQuery('#pwbe-results-container').css('display', 'block');
                jQuery('body').css('cursor', 'default');
            }).fail(function(xhr, textStatus, errorThrown) {;
                alert(ajaxurl + ' ' + errorThrown);
            });
        });

    } else {
        jQuery('.pwbe-field-changed').removeClass('pwbe-field-changed');
        jQuery('#pwbe-filters-form').submit();
        jQuery('.pwbe-saving-status').css('display', 'none');
        jQuery('body').css('cursor', 'default');
    }
}

function pwbeFixAttributes() {
    var valid = true;
    jQuery('.pwbe-temporary-editor-container').each(function() {
        if (!pwbeAcceptFieldEdit(jQuery(this))) {
            valid = false;
        }
    });

    if (!valid) {
        return false;
    }

    jQuery('body').css('cursor', 'wait');
    jQuery('#pwbe-results-error').html('').addClass('pwbe-hidden');
    jQuery('.pwbe-processing-message').text('Fixing attributes...');
    jQuery('.pwbe-processing').css('display', 'block');
    jQuery('#pwbe-results-container').css('display', 'none');

    var checkedProducts = jQuery('.pwbe-product-checkbox:checked');

    // Start the saving loop
    pwbeFixAttribute(0, checkedProducts);
}

function pwbeFixAttribute(index, values) {
    if (index < values.length) {

        jQuery('.pwbe-processing-message').text('Fixing ' + (index + 1) + ' of ' + values.length);

        var postId = jQuery(values).eq(index).val();

        jQuery.post(ajaxurl, { 'action': 'pwbe_fix_attributes', 'post_id': postId }, function(response) {
            if (response == 'success') {
                pwbeFixAttribute(index + 1, values);
            } else {
                jQuery('#pwbe-results-error').html(response).removeClass('pwbe-hidden');
                jQuery('.pwbe-processing-message').text('');
                jQuery('.pwbe-processing').css('display', 'none');
                jQuery('#pwbe-results-container').css('display', 'block');
                jQuery('body').css('cursor', 'default');
                return false;
            }
        }).fail(function(xhr, textStatus, errorThrown) {
            jQuery('#pwbe-results-error').html(errorThrown).removeClass('pwbe-hidden');
            jQuery('.pwbe-processing-message').text('');
            jQuery('.pwbe-processing').css('display', 'none');
            jQuery('#pwbe-results-container').css('display', 'block');
            jQuery('body').css('cursor', 'default');
            return false;
        });

    } else {
        jQuery('.pwbe-field-changed').removeClass('pwbe-field-changed');
        jQuery('#pwbe-filters-form').submit();
        jQuery('.pwbe-saving-status').css('display', 'none');
        jQuery('body').css('cursor', 'default');
    }
}

function pwbeSaveCurrentView(viewName) {
    jQuery('body').css('cursor', 'wait');

    var hiddenColumns = [];
    jQuery('#pwbe-header-results .pwbe-hidden-column').each(function() {
        hiddenColumns.push(jQuery(this).children('.pwbe-header').attr('data-field'));
    });

    jQuery.post(ajaxurl, {
        'action': 'pwbe_save_view',
        'name': viewName,
        'view_data': JSON.stringify( hiddenColumns )
    }, function(data) {
        if (jQuery('#pwbe-view option').filter(function() { return this.value == viewName; }).length == 0) {
            jQuery('#pwbe-view').append(jQuery("<option></option>").attr("value", viewName).text(viewName));
        }

        jQuery('#pwbe-view').val(viewName);
        jQuery('#pwbe-view-save').addClass('pwbe-hidden');
        jQuery('#pwbe-view-delete').removeClass('pwbe-hidden');

        if (jQuery('#pwbe-header-results .pwbe-hidden-column').length > 0) {
            jQuery('#pwbe-view-edit').removeClass('pwbe-hidden');
        }

        jQuery('#pwbe-view-custom').remove();
        jQuery('body').css('cursor', 'default');
    });
}

function pwbeAcceptFieldEdit(editorContainer) {
    var hiddenInput = editorContainer.siblings('.pwbe-field-value');
    var input = editorContainer.find('.pwbe-temporary-editor');
    var newValue = input.val();

    if (input.attr('type') == 'checkbox') {
        newValue = input.prop('checked') ? 'yes' : 'no';
    }

    var validationResult = pwbeFieldValidate(input, newValue);
    if (validationResult == '') {
        pwbeFieldUpdate(hiddenInput, newValue);
        editorContainer.closest('.pwbe-td').addClass('pwbe-results-table-td');
        pwbeCloseTemporaryEditor();
        return true;
    } else {
        alert(validationResult);
        return false;
    }
}

function pwbeFieldMove(input, direction, sameElement) {
    switch (direction) {
        case 'up':
            input.closest('.pwbe-product-tr').prevAll().each(function() {
                return pwbeEditableFieldExists(jQuery(this), '-' + input.attr('data-field'));
            });
        break;

        case 'down':
            input.closest('.pwbe-product-tr').nextAll().each(function() {
                return pwbeEditableFieldExists(jQuery(this), '-' + input.attr('data-field'));
            });
        break;

        case 'left':
            var element = input.closest('.pwbe-results-table-td');

            if (sameElement) {
                element.next().prevAll().each(function() {
                    return pwbeEditableFieldExists(jQuery(this), '');
                });
            } else {
                element.prevAll().each(function() {
                    return pwbeEditableFieldExists(jQuery(this), '');
                });
            }

            if (jQuery('.pwbe-temporary-editor').length == 0 && input.closest('.pwbe-product-tr').prevAll().length > 0) {
                pwbeFieldMove(input.closest('.pwbe-product-tr').prev().find('.pwbe-field').not('.pwbe-field-readonly').find('.pwbe-field-value').last(), direction, true);
            }
        break;

        case 'right':
            var element = input.closest('.pwbe-results-table-td');

            if (sameElement) {
                element.prev().nextAll().each(function() {
                    return pwbeEditableFieldExists(jQuery(this), '');
                });
            } else {
                element.nextAll().each(function() {
                    return pwbeEditableFieldExists(jQuery(this), '');
                });
            }

            if (jQuery('.pwbe-temporary-editor').length == 0 && input.closest('.pwbe-product-tr').nextAll().length > 0) {
                pwbeFieldMove(input.closest('.pwbe-product-tr').next().find('.pwbe-field').not('.pwbe-field-readonly').find('.pwbe-field-value').first(), direction, true);
            }
        break;
    }
}

function pwbeEditableFieldExists(parent, fieldName) {
    var nextInput = parent.find('.pwbe-field' + fieldName).not('.pwbe-field-readonly').find('.pwbe-field-label').first();
    if (nextInput.length != 0) {
        nextInput.click();
        return false;
    } else {
        return true;
    }
}

function pwbeBulkEditDialogApply() {
    var dialog = jQuery('#pwbe-bulkedit-dialog');
    var editor = dialog.find('.pwbe-dialog-content:visible').first();
    var func = editor.attr('data-function');
    var fieldName = editor.attr('data-field');

    var salePriceRegularPriceSwitcharoo = false;
    if (fieldName == '_sale_price' && editor.find('.pwbe-bulkedit-use-regular-price:first').prop('checked')) {
        salePriceRegularPriceSwitcharoo = true;
    }

    var changeCount = 0;
    pwbeUndoHistory.push({'action': 'bulk_begin'});

    var checkboxes = jQuery('.pwbe-product-checkbox');
    for (var i = 0, len = checkboxes.length; i < len; i++) {
        var checkbox = jQuery(checkboxes[i]);
        if (checkbox.prop('checked')) {
            var productDiv = checkbox.closest('.pwbe-tr').find('.pwbe-field-' + fieldName);

            if (!productDiv.hasClass('pwbe-field-readonly')) {
                var fieldInput = productDiv.children('.pwbe-field-value').first();
                var oldValue = fieldInput.val();

                var regularPrice = 0;
                if (fieldName == '_sale_price') {
                    regularPrice = checkbox.closest('.pwbe-tr').find('.pwbe-field-_regular_price').children('.pwbe-field-value').first().val();
                }

                // Sale Price can be adjusted based on Regular Price.
                if (salePriceRegularPriceSwitcharoo) {
                    oldValue = regularPrice;
                }

                var newValue = window[func]('apply', oldValue);
                if (newValue != fieldInput.val()) {

                    if (fieldName == '_sale_price') {
                        var thousandSeparator = jQuery('#pwbe-price-thousand-separator').val();
                        var decimalSeparator = jQuery('#pwbe-price-decimal-separator').val();

                        var decimalNewValue = parseFloat(String(newValue).replace(thousandSeparator, '').replace(decimalSeparator, '.'));
                        var decimalRegularPrice = parseFloat(String(regularPrice).replace(thousandSeparator, '').replace(decimalSeparator, '.'));

                        if (decimalNewValue > decimalRegularPrice) {
                            // Don't allow sale price to be higher than the regular price.
                            newValue = regularPrice;
                        }
                    }

                    if (func == 'pwbeBulkEditorSelectHandler') {
                        var cell = productDiv.closest('.pwbe-td');
                        var hiddenInput = productDiv.find('.pwbe-field-value').first();
                        var productType = hiddenInput.attr('data-product-type');
                        var visibility = window[func]('visibility', '');
                        if (visibility != 'both') {
                            if (visibility == 'variation' && productType != 'variation') {
                                continue;
                            } else if (visibility == 'parent' && productType == 'variation') {
                                continue;
                            }
                        }
                    }
                    pwbeFieldUpdate(fieldInput, newValue);
                    changeCount++;
                }
            }
        }
    }

    if (changeCount > 0) {
        pwbeRedoHistory.length = 0;
        pwbeUndoHistory.push({'action': 'bulk_end'});
    } else {
        pwbeUndoHistory.pop();
    }

    pwbeProductButtonsEnableDisable();

    pwbeBulkEditDialogClose();
}

function pwbeBulkEditDialogClose() {
    var dialog = jQuery('#pwbe-bulkedit-dialog');
    var editor = dialog.find('.pwbe-dialog-content:visible').first();
    var func = editor.attr('data-function');

    dialog.css('display', 'none');
    jQuery('.pwbe-overlay').remove();

    window[func]('reset');
}

function pwbeEditViewDialogClose() {
    var dialog = jQuery('#pwbe-edit-view-dialog');
    var func = dialog.find('.pwbe-dialog-content:visible:first').attr('data-function');

    dialog.css('display', 'none');
    jQuery('.pwbe-overlay').remove();

    window[func]('reset');
}

function pwbeFieldValidate(field, newValue) {
    var oldValue = field.val();
    var inputType = field.attr('data-input-type');
    var fieldName = field.attr('data-field');

    if (fieldName == '_sale_price') {
        var regularPrice = field.closest('.pwbe-tr').find('.pwbe-field-_regular_price').children('.pwbe-field-value').first().val();

        var thousandSeparator = jQuery('#pwbe-price-thousand-separator').val();
        var decimalSeparator = jQuery('#pwbe-price-decimal-separator').val();

        var decimalNewValue = parseFloat(String(newValue).replace(thousandSeparator, '').replace(decimalSeparator, '.'));
        var decimalRegularPrice = parseFloat(String(regularPrice).replace(thousandSeparator, '').replace(decimalSeparator, '.'));

        if (decimalNewValue > decimalRegularPrice) {
            return 'Sale Price cannot be higher than the Regular Price.';
        }
        return '';
    }

    return '';
}

function pwbeFieldUpdate(field, newValue, skipHistory) {

    var thousandSeparator = jQuery('#pwbe-price-thousand-separator').val();
    var decimalSeparator = jQuery('#pwbe-price-decimal-separator').val();

    var oldValue = field.val();
    var inputType = field.attr('data-input-type');
    var fieldName = field.attr('data-field');
    var parent = field.closest('.pwbe-td');
    var fieldLabel = parent.find('.pwbe-field-label');

    if (inputType == 'currency') {
        newValue = String(newValue).replace(thousandSeparator, '');
    }

    field.val(newValue);

    var displayValue = newValue;

    switch (inputType) {
        case 'select':
            if (displayValue === undefined) {
                displayValue = 'n/a';
            } else {
                displayValue = jQuery('.pwbe-dropdown-template-' + fieldName + ' option').filter(function() { return jQuery(this).attr('value') == newValue; }).text();
            }

            if (!displayValue) {
                displayValue = 'n/a';
            }

            fieldLabel.text(displayValue);
        break;

        case 'multiselect':
            if (newValue) {
                displayValue = 'Edit (' + String(newValue).split(',').length + ')';
            } else {
                displayValue = 'Edit (0)';
            }

            fieldLabel.text(displayValue);
        break;

        case 'image':
            if (newValue != oldValue) {
                pwbeGetImageHtml(fieldLabel, newValue);
            }
        break;

        default:
            if (displayValue === undefined || displayValue === '') {
                displayValue = 'n/a';
            }

            fieldLabel.text(displayValue);
        break;
    }

    parent.removeClass('pwbe-field-changed').removeClass('pwbe-field-changed-up').removeClass('pwbe-field-changed-down');

    fieldLabel.css('display', 'block');

    var originalValue = field.attr('data-original-value');
    if (pwbeValueHasChanged(originalValue, newValue, inputType)) {
        parent.addClass('pwbe-field-changed');

        // We'll do special formatting for numbers and currency (higher/lower).
        if (inputType == 'number' || inputType == 'currency') {
            if (!originalValue) { originalValue = 0; }

            var decimalNewValue = parseFloat(String(newValue).replace(thousandSeparator, '').replace(decimalSeparator, '.'));
            var decimalOriginalValue = parseFloat(String(originalValue).replace(thousandSeparator, '').replace(decimalSeparator, '.'));

            if (decimalNewValue > decimalOriginalValue) {
                parent.addClass('pwbe-field-changed-up');
            } else if (decimalNewValue < decimalOriginalValue) {
                parent.addClass('pwbe-field-changed-down');
            }
        }
    }

    if (skipHistory != true) {
        if (pwbeValueHasChanged(oldValue, newValue, inputType)) {
            var displayName = jQuery('#pwbe-header-results .pwbe-results-table-header-td').filter(function() { return jQuery(this).attr('data-field') == fieldName; } ).find('.pwbe-header').text();
            var editingAttributes = (displayName && displayName.endsWith(' attributes'));

            var action = {
                'action': 'field',
                'field': field,
                'editingAttributes': editingAttributes,
                'oldValue': oldValue,
                'newValue': newValue
            };

            pwbeUndoHistory.push(action);
            pwbeRedoHistory.length = 0;
        }
    }

    pwbeProductButtonsEnableDisable();
}

function pwbeCloseTemporaryEditor() {
    var container = jQuery('.pwbe-temporary-editor-container');

    if (pwbe.editingRowBorder == 'yes') {
        jQuery(container).closest('.pwbe-tr').removeClass('pwbe-tr-editing');
    }

    container.find('.pwbe-temporary-editor').each(function() {
        if (jQuery(this).data('pwbeselect2')) {
            jQuery(this).pwbeselect2('destroy');
        }
    });
    container.remove();
}

function pwbeValueHasChanged(oldValue, newValue, inputType) {
    var hasChanged = false;

    // First, compare based on straight up values.
    hasChanged = (newValue != oldValue && (newValue || oldValue));

    // Multiselect boxes may be re-arranged so a string comparison is insufficent. Need to compare arrays.
    if (hasChanged && inputType == 'multiselect') {
        var arrOld = String(oldValue).split(',');
        var arrNew = String(newValue).split(',');
        hasChanged = (jQuery(arrOld).not(arrNew).length !== 0 || jQuery(arrNew).not(arrOld).length !== 0);
    }

    return hasChanged;
}

function pwbeUndo(repeat) {
    if (pwbeUndoHistory.length > 0) {
        var item = pwbeUndoHistory.pop();
        pwbeRedoHistory.push(item);

        switch (item.action) {
            case 'field':
                pwbeFieldUpdate(item.field, item.oldValue, true);
            break;

            case 'bulk_begin':
                repeat = false;
            break;

            case 'bulk_end':
                repeat = true;
            break;
        }
    }

    if (repeat) {
        pwbeUndo(true);
    } else {
        pwbeProductButtonsEnableDisable();
    }
}

function pwbeRedo(repeat) {
    if (pwbeRedoHistory.length > 0) {
        var item = pwbeRedoHistory.pop();
        pwbeUndoHistory.push(item);

        switch (item.action) {
            case 'field':
                pwbeFieldUpdate(item.field, item.newValue, true);
            break;

            case 'bulk_begin':
                repeat = true;
            break;

            case 'bulk_end':
                repeat = false;
            break;
        }
    }

    if (repeat) {
        pwbeRedo(true);
    } else {
        pwbeProductButtonsEnableDisable();
    }
}

function pwbeProductButtonsEnableDisable() {
    var isDirty = (pwbeUndoHistory.length > 0 || pwbeRedoHistory.length > 0);
    jQuery('.pwbe-results-buttons').find('button').prop('disabled', !isDirty);

    jQuery('#pwbe-product-undo-button').prop('disabled', (pwbeUndoHistory.length == 0));
    jQuery('#pwbe-product-redo-button').prop('disabled', (pwbeRedoHistory.length == 0));

    // Auto-Create Variations
    var container = jQuery('#pwbe-auto-create-variations-container');
    container.addClass('pwbe-hidden');
    for (var i = 0, len = pwbeUndoHistory.length; i < len; i++) {
        var item = pwbeUndoHistory[i];
        if (item.editingAttributes) {
            container.removeClass('pwbe-hidden');
            break;
        }
    }
}

function pwbePregQuote(str) {
    // Source: http://kevin.vanzonneveld.net
    return (str+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\|\:])/g, "\\$1");
}

var pwbeImageCache = {};

function pwbeGetImageHtml(field, imageId) {
    if ( imageId in pwbeImageCache ) {
        field.html(pwbeImageCache[imageId]);
    } else {
        field.html('<i class="fa fa-cog fa-spin fa-3x"></i>');
        jQuery.post(ajaxurl, {'action': 'pwbe_get_image_html', 'image_id': imageId}, function( result ) {
            pwbeImageCache[imageId] = result.html;
            field.html(result.html);
        }).fail(function(xhr, textStatus, errorThrown) {
            field.text('Unknown error');
        });
    }
}