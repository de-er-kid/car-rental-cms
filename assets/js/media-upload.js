jQuery(document).ready(function ($) {
  var mediaUploader;

  // Function to handle file upload for both brand logo and category image
  function handleMediaUpload(inputSelector, previewSelector, uploadButtonSelector, removeButtonSelector) {
      var $input = $(inputSelector);
      var $preview = $(previewSelector);
      var $uploadButton = $(uploadButtonSelector);
      var $removeButton = $(removeButtonSelector);

      // Trigger media uploader only when needed
      $uploadButton.on("click", function (e) {
          e.preventDefault();

          // If the media frame already exists, reopen it
          if (mediaUploader) {
              mediaUploader.open();
              return;
          }

          // Create the media frame on demand
          mediaUploader = wp.media({
              title: "Select or Upload Image",
              button: {
                  text: "Use this image",
              },
              multiple: false,
          });

          // When an image is selected, run a callback
          mediaUploader.on("select", function () {
              var attachment = mediaUploader
                  .state()
                  .get("selection")
                  .first()
                  .toJSON();

              // Set the input value to attachment ID
              $input.val(attachment.id);

              // Create an image element and set src for preview
              var $img = $('<img />', {
                  src: attachment.url,
                  class: 'preview-image',
                  style: 'max-width: 300px;'
              });

              // Append image to preview container
              $preview.html($img);

              // Show remove button
              $removeButton.show();
          });

          // Open the media library frame
          mediaUploader.open();
      });

      // Remove the selected image
      $removeButton.on("click", function (e) {
          e.preventDefault();

          // Clear input and preview
          $input.val("");
          $preview.empty();

          // Hide remove button
          $removeButton.hide();
      });
  }

  // Initialize file upload functionality for both Brand Logo and Category Image
  handleMediaUpload("#brands_logo", "#brands_logo_preview", "#upload_brands_logo_button", "#remove_brands_logo_button");
  handleMediaUpload("#category_image", "#category_image_preview", "#upload_category_image_button", "#remove_category_image_button");

  // Color picker initialization (no changes needed here)
  $(".deals-color-picker").wpColorPicker();
});
