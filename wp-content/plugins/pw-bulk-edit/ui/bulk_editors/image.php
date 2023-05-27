<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?>
<span id="pwbe-dialog-content-image" class="pwbe-dialog-content" data-function="pwbeBulkEditorImageHandler">
    <fieldset id="pwbe-bulkedit-image-mode">
        <input type="radio" value="select" name="pwbe-bulkedit-image-mode" id="pwbe-bulkedit-image-mode-select" /> <label for="pwbe-bulkedit-image-mode-select"><?php _e( 'Select an image', 'pw-bulk-edit' ); ?></label><br />
        <input type="radio" value="clear" name="pwbe-bulkedit-image-mode" id="pwbe-bulkedit-image-mode-clear" /> <label for="pwbe-bulkedit-image-mode-clear"><?php _e( 'Clear the image', 'pw-bulk-edit' ); ?></label><br />
    </fieldset>
    <div id="pwbe-bulkedit-details-image-container">
        <div>
            <div class="pwbe-bulkedit-details pwbe-bulkedit-details-select">
                <label for="pwbe-bulkedit-select-amount"><?php printf( __( 'Set %s to the following image:', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label><br />
                <div id="pwbe-dialog-select-image-container" data-image-ids=""></div>
                <button class="button" id="pwbe-dialog-bulk-edit-select-image"><?php _e( 'Select an image', 'pw-bulk-edit' ); ?></button>
            </div>

            <div class="pwbe-bulkedit-details pwbe-bulkedit-details-clear">
                <label><?php printf( __( 'Clear the %s value.', 'pw-bulk-edit' ), '<span class="pwbe-bulkedit-field-name"></span>' ); ?></label>
            </div>
        </div>
    </div>
</span>
<style>
    #pwbe-dialog-content-image {
        white-space: nowrap;
    }

    #pwbe-bulkedit-image-mode, #pwbe-bulkedit-details-image-container {
        display: inline-block;
        vertical-align: top;
    }

    #pwbe-bulkedit-details-image-container {
        padding-left: 30px;
        min-width: 400px;
    }

    .pwbe-bulkedit-details {
        display: none;
    }
</style>
<script>
    jQuery(function() {
        jQuery('#pwbe-bulkedit-image-mode').find('input[type=radio]').change(function() {
            var mode = jQuery(this).val();
            var dialog = jQuery('#pwbe-dialog-content-image');
            var details = dialog.find('.pwbe-bulkedit-details-' + mode);
            dialog.find('.pwbe-bulkedit-details').hide();

            details.show().find('input:first').focus();
        });

        pwbeGetImageHtml(jQuery('#pwbe-dialog-select-image-container'), '');
    });


    function pwbeBulkEditorImageHandler(action, oldValue) {
        var dialog = jQuery('#pwbe-dialog-content-image');
        var fieldName = dialog.attr('data-field-name');
        var mode = dialog.find('input[name=pwbe-bulkedit-image-mode]:checked').first();
        var selectedImageContainer = jQuery('#pwbe-dialog-select-image-container');
        var newValue = '';

        switch (action) {
            case 'init':
                dialog.find('.pwbe-bulkedit-field-name').text(fieldName);
                selectedImageContainer.text('');
                selectedImageContainer.attr('data-image-ids', '');
            break;

            case 'apply':
                if (!mode.val()) {
                    return oldValue;
                }

                switch (mode.val()) {
                    case 'select':
                        newValue = selectedImageContainer.attr('data-image-ids');
                    break;

                    case 'clear':
                        newValue = '';
                    break;
                }

                return newValue;
            break;

            case 'reset':
                mode.prop('checked', false);
                dialog.find('.pwbe-bulkedit-details').hide();
            break;
        }
    }

    jQuery('#pwbe-dialog-bulk-edit-select-image').click(function(e) {
        var selectedImageContainer = jQuery('#pwbe-dialog-select-image-container');
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
            selectedImageContainer.attr('data-image-ids', ids);
            pwbeGetImageHtml(selectedImageContainer, ids);
        });

        image_frame.on('open',function() {
            // On open, get the id from the hidden input
            // and select the appropiate images in the media manager
            var selection =  image_frame.state().get('selection');
            var ids = selectedImageContainer.attr('data-image-ids').split(',');
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
</script>