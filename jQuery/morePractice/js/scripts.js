let products = {
  white: {
    plain: {
      unitPrice: 5.12,
      photo: "v-white.jpg",
    },
    printed: {
      unitPrice: 8.95,
      photo: "v-white-personalized.jpg",
    },
  },

  colored: {
    plain: {
      unitPrice: 6.04,
      photo: "v-color.jpg",
    },
    printed: {
      unitPrice: 9.47,
      photo: "v-color-personalized.png",
    },
  },
};

// Search params
let searchParams = {
  quantity: "",
  color: "",
  quality: "",
  style: "",
};

// Additional pricing rules:
// 1. The prices above are for Basic quality (q150).
// The high quality shirt (190g/m2) has a 12% increase in the unit price.

// 2. Apply the following discounts for higher quantities:
// 1: above 1.000 units - 20% discount
// 2: above 500 units - 12% discount
// 3: above 100 units - 5% discount

$(function () {
  function updateParams() {
    searchParams.quantity = $("#quantity").val();
    searchParams.color = $("#color .option-button.selected").attr("id");
    searchParams.quality = $("#quality .option-button.selected").attr("id");
    searchParams.style = $("#style option:checked").val();
    updateOrderDetails();
  }

  function updateOrderDetails() {
    $(".refresh-loader").show();
    $("#result-quantity").html(parseInt(searchParams.quantity));
    $("#result-color").html($("#" + searchParams.color).text());
    $("#result-quality").html($("#" + searchParams.quality).text());
    $("#result-style").html(
      $("#style option[value='" + searchParams.style + "']").text()
    );
    $("#total-price").text(calculateTotal());
    $("#photo-product").attr(
      "src",
      "img/" + products[searchParams.color][searchParams.style].photo
    );

    // Testing purposes only
    window.setTimeout(function () {
      console.log("test");
      $(".refresh-loader").hide();
    }, 100);
  }

  function calculateTotal() {
    let unitPrice = products[searchParams.color][searchParams.style].unitPrice;

    if (searchParams.quality == "q190") {
      unitPrice *= 1.12;
    }

    let total = unitPrice * searchParams.quantity;

    if (searchParams.quantity >= 1000) {
      total *= 0.8;
    } else if (searchParams.quantity >= 500) {
      total *= 0.88;
    } else if (searchParams.quantity >= 100) {
      total *= 0.95;
    }

    // https://www.w3schools.com/jsref/jsref_tolocalestring_number.asp
    return total.toLocaleString("en-US", {
      style: "currency",
      currency: "USD",
    });
  }

  updateParams();

  $("#quantity").change(function () {
    searchParams.quantity = $("#quantity").val();
    updateOrderDetails();
  });

  $("#style").change(function () {
    searchParams.style = $("#style option:checked").val();
    updateOrderDetails();
  });

  $(".option-button").click(function () {
    let clickedOption = $(this).parent().attr("id");
    let childSelector = "#" + clickedOption + " .option-button";

    $(childSelector).removeClass("selected");
    $(this).addClass("selected");

    let selectedChild = "#" + clickedOption + " .option-button.selected";
    searchParams[clickedOption] = $(selectedChild).attr("id");
    searchParams[clickedOption] = $(selectedChild).attr("id");
    updateOrderDetails();
  });
});
