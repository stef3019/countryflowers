
function showLoader() {
  var jsonUrl = jQuery('#json_url').val();

// Check if JSON URL is provided
if (jsonUrl === '') {
  jQuery('#json-image-importer-messages').html('<div class="error notice"><p>Please enter a valid JSON file URL.</p></div>');
  return;
}

// Show loading animation
jQuery('#json-image-importer-loader').show();

// Disable the button
jQuery('#json-image-importer-button').prop('disabled', true);

// Clear previous messages
jQuery('#json-image-importer-messages').html('');

    // Submit the form asynchronously
    jQuery.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'json_image_importer_process_json',
        json_url: jsonUrl,
        nonce: jQuery('#json_image_importer_nonce').val()
      },
      success: function(response) {
        console.log('success'+response);
        // Hide loading animation
        jQuery('#json-image-importer-loader').hide();

        // Enable the button
        jQuery('#json-image-importer-button').prop('disabled', false);

        // Show success or error message
        jQuery('#json-image-importer-messages').html(response);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log('error'+errorThrown);
        alert('Failed to save JSON file: ' + errorThrown);
      }
    });

}