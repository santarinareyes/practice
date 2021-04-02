"use_strict";

// Hide/Show
let hide = false;
$("#hide").click(function () {
  if (!hide) {
    $(".example").hide();
    $("#hide").html("show");
    hide = true;
  } else {
    $(".example").show();
    $("#hide").html("hide");
    hide = false;
  }
});

// Change img to JS/PHP
let showPHP = false;
$("#change_image").click(function () {
  if (!showPHP) {
    $("#image_jquery").attr(
      "src",
      "https://cdn.imgbin.com/12/2/24/imgbin-php-java-initiation-iy1tsmB5Am9syCfs69ixFA5R4.jpg"
    );
    $("#change_image").text("Change to JS");
    showPHP = true;
  } else {
    $("#image_jquery").attr(
      "src",
      "https://static.memrise.com/img/400sqf/from/uploads/course_photos/3146044000171223183557.png"
    );
    $("#change_image").text("Change to PHP");
    showPHP = false;
  }
});
