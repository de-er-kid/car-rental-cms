jQuery(document).ready(function ($) {

  // Delay slider initialization to prevent layout shift
  $(window).on('load', function () {
    // Car images slider
    document.querySelectorAll(".car-images-slider").forEach((slider) => {
      tns({
        container: slider,
        items: 1,
        slideBy: 1, 
        autoplay: true,
        autoplayButtonOutput: false,
        loop: true,
        nav: false,
        controls: true,
        controlsText: ["<", ">"],
      });
    });

    // Brands slider
    const slider = tns({
      container: "#brands-slide .taxonomy-loop",
      slideBy: 1,
      autoplay: true,
      autoplayTimeout: 3000,
      autoplayButtonOutput: false,
      loop: true,
      gutter: 10,
      controls: false,
      nav: false,
      responsive: {
        0: { items: 4 },
        768: { items: 4 },
        1024: { items: 6 },
        1440: { items: 11 },
      },
    });
  });

});
