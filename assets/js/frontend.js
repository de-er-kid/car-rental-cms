jQuery(document).ready(function ($) {
  const slider = tns({
    container: ".taxonomy-loop",
    items: 8,
    slideBy: "page",
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayButtonOutput: false,
    loop: true,
    gutter: 10,
    controls: false,
    nav: false,
    responsive: {
      0: { items: 5 },
      768: { items: 6 },
      1024: { items: 6 },
      1440: { items: 7 },
    },
  });

  // $.ajax({
  //     url: 'your-endpoint-url',
  //     method: 'GET',
  //     success: function(response) {
  //         console.log('AJAX request successful:', response);
  //     },
  //     error: function(error) {
  //         console.error('AJAX request failed:', error);
  //     }
  // });
});
