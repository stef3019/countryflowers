function saveJsonFile() {
    var jsonUrl = jQuery('#json_url_2').val();
  
    jQuery.ajax({
      url: jsonImageImporter.ajaxUrl,
      type: 'POST',
      data: {
        action: 'save_json_file',
        json_url: jsonUrl,
        nonce: jsonImageImporter.nonce
      },
      success: function(response) {
        if (response.success) {
          alert('JSON file saved successfully.');
        } else {
          alert('Cannot save JSON file: ' + response.data);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
        alert('Failed to save JSON file: ' + errorThrown);
      }
    });
  }