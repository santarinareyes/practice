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
    $("#change_image").text("Change to ");
    showPHP = false;
  }
});

// Write something
$("#write_something_code").hide();
$("#write_something").hide();
let writeSomething = false;

$("#btn_write_something").click(function () {
  if (!writeSomething) {
    writeSomething = true;
    $("#btn_write_something").text("Click to stop writing something");
    $("#write_something").show();

    let activateWrite = window.setInterval(function () {
      if (writeSomething) {
        console.log("test");
        if ($("#write_something").val().length < 1) {
          $("#write_something_code").hide();

          let closeWriteTimer = window.setInterval(function () {
            if ($("#write_something").val().length < 1) {
              console.log("test2");
              writeSomething = false;
              $("#btn_write_something").text(
                "Click to start writing something"
              );
              $("#write_something").hide();
              window.clearInterval(activateWrite);
              window.clearInterval(closeWriteTimer);
            } else {
              window.clearInterval(closeWriteTimer);
            }
          }, 15000);
        } else {
          $("#write_something_code").show();
          $("#write_something_text").text($("#write_something").val());
        }
      } else {
        window.clearInterval(activateWrite);
      }
    }, 100);
  } else {
    writeSomething = false;
    $("#write_something_code").hide();
    $("#write_something").hide();
    $("#btn_write_something").text("Click to start writing something");
  }
});

// Options
$("#options").change(function () {
  let selectedText = $("#options option:selected").text();
  $("#selected_option p").text(selectedText);
});

// Radio
$("input[name='location']").change(function () {
  $("#selected_radio p").text($("input[name='location']:checked").val());
});

// Checkbox
$("input[name='interest']").change(function () {
  let selectedCheckboxes = $("input[name='interest']:checked");
  $.each(selectedCheckboxes, function (index, value) {
    console.log($(value).parent().text());
    $("#selected_checkbox ul").html(
      "<li>" + $(value).parent().text() + "</li>"
    );
  });
});

// Add/Remove/Toggle class buttons
$("#add_class").click(function () {
  $("#example-paragraph").addClass("styling");
});

$("#remove_class").click(function () {
  $("#example-paragraph").removeClass("styling");
});

$("#toggle_class").click(function () {
  $("#example-paragraph").toggleClass("styling");
});
