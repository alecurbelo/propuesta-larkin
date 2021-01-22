var $firstButton = $(".first"),
  $secondButton = $(".second"),
  $thirdButton = $ (".third"),
  $input = $("input"),
  $name = $(".name"),
  $more = $(".more"),
  $yourname = $(".yourname"),
  $reset = $(".reset"),
  $ctr = $(".form-container");

$firstButton.on("click", function(e){
  $(this).text("Saving...").delay(900).queue(function(){
    $ctr.addClass("first slider-two-active").removeClass("slider-one-active");
    $("html, body").animate({ scrollTop: $("#form-appointment").offset().top }, 1000);
  });
  e.preventDefault();
});

$secondButton.on("click", function(e){
  $(this).text("Saving...").delay(900).queue(function(){
    $ctr.addClass("center slider-three-active").removeClass("first slider-two-active");
    $("html, body").animate({ scrollTop: $("#form-appointment").offset().top }, 1000);
  });
  e.preventDefault();
});

$thirdButton.on("click", function(e){
    $(this).text("Saving...").delay(900).queue(function(){
      $ctr.addClass("full slider-fourth-active").removeClass("center slider-three-active");
      $("html, body").animate({ scrollTop: $("#form-appointment").offset().top }, 1000);
    });
    e.preventDefault();
  });

// copy
//balapaCop("Step by Step Form", "#999");