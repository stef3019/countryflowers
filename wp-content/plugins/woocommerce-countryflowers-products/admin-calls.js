  jQuery(document).ready(function($) {
    jQuery('#import_all').click(function() {
        var data = {
            'action': 'check_products_file'    // We pass php values differently!
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function(response) {
            alert('Got this from the server: ' + response);
           // alert('ran');
        });
    });

    jQuery("#checkAll").click(function(){
        jQuery('table input:checkbox').not(this).prop('checked', this.checked);
    });

    jQuery('#parent_category').on('change', function() {
        var parent = jQuery('#parent_category').val();
         jQuery('#sub_category').children('option').hide();
         jQuery('#sub_category').children("option[data-parent=" + parent + "]").show();
    });

   
});