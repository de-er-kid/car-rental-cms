jQuery(document).ready(function ($) {
  var mediaUploader;

  // Function to handle file upload for both brand logo and category image
  function handleMediaUpload(
    inputSelector,
    previewSelector,
    uploadButtonSelector,
    removeButtonSelector
  ) {
    var $input = $(inputSelector);
    var $preview = $(previewSelector);
    var $uploadButton = $(uploadButtonSelector);
    var $removeButton = $(removeButtonSelector);

    // Trigger media uploader
    $uploadButton.on("click", function (e) {
      e.preventDefault();

      // If the media frame already exists, reopen it
      if (mediaUploader) {
        mediaUploader.open();
        return;
      }

      // Create the media frame
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

        // Show preview
        $preview.html(
          '<img src="' + attachment.url + '" style="max-width: 300px;" />'
        );

        // Show remove button
        $removeButton.show();
      });

      // Open the media library frame
      mediaUploader.open();
    });

    // Remove the selected image
    $removeButton.on("click", function (e) {
      e.preventDefault();

      // Clear input
      $input.val("");

      // Clear preview
      $preview.html("");

      // Hide remove button
      $removeButton.hide();
    });
  }

  // Initialize file upload functionality for both Brand Logo and Category Image
  handleMediaUpload(
    "#brands_logo",
    "#brands_logo_preview",
    "#upload_brands_logo_button",
    "#remove_brands_logo_button"
  );
  handleMediaUpload(
    "#category_image",
    "#category_image_preview",
    "#upload_category_image_button",
    "#remove_category_image_button"
  );

  // let faqIndex = $('.faq-group').length;

  // $('#add_faq').on('click', function() {

  //     const template = `

  //         <div class="faq-group">

  //             <p>

  //                 <label>Question</label>

  //                 <input type="text" class="widefat" name="car_faq[question][]" value="" />

  //             </p>

  //             <p>

  //                 <label>Answer</label>

  //                 <textarea name="car_faq[answer][]"></textarea>

  //             </p>

  //             <button type="button" class="remove-faq button button-primary button-large">Remove</button>

  //         </div>

  //     `;

  //     $('#car_faqs_container').append(template);

  //     faqIndex++;

  // });

  // $(document).on('click', '.remove-faq', function() {

  //     $(this).closest('.faq-group').remove();

  // });

  $(".deals-color-picker").wpColorPicker();
});