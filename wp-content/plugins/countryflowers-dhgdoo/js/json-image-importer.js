jQuery(document).ready(function($) {
  var mediaUploader;

  // Handle click event on Import Images button
  $('#json-image-importer-form').on('submit', function(e) {
    e.preventDefault();

    var jsonUrl = $('#json_url').val();

    // Clear previous messages
    $('#json-image-importer-messages').html('');

    // Check if JSON URL is provided
    if (jsonUrl === '') {
      $('#json-image-importer-messages').html('<div class="error notice"><p>Please enter a valid JSON file URL.</p></div>');
      return;
    }

    // Show loading animation
    $('#json-image-importer-loader').show();

    // Disable the button
    $('#json-image-importer-button').prop('disabled', true);

    // Submit the form asynchronously
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'json_image_importer_process_json',
        json_url: jsonUrl,
        nonce: $('#json_image_importer_nonce').val()
      },
      success: function(response) {
        // Hide loading animation
        $('#json-image-importer-loader').hide();

        // Enable the button
        $('#json-image-importer-button').prop('disabled', false);

        // Show success or error message
        $('#json-image-importer-messages').html(response);
      }
    });
  });
});