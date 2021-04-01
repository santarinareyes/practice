// Mobile Menu
const hamburgerBtn = document.querySelector(".hamburguer-icon"),
  hamburgerClose = document.querySelector("#sliding-header-menu-close-button"),
  hamburgerSlider = document.querySelector("#sliding-header-menu-outer");

hamburgerBtn.onclick = function () {
  hamburgerSlider.style.right = "0px";
};

hamburgerClose.onclick = function () {
  hamburgerSlider.style.right = "-320px";
};

// About us Tab
let aboutUs = {
  Mission:
    "Duis ac leo nisi. Mauris nec ex id lorem commodo rutrum rutrum a est. Cras facilisis sit amet lectus non posuere. Nullam non magna non enim blandit elementum.",
  Vision:
    "Praesent ut lacinia neque, faucibus suscipit quam. Duis sed nunc rutrum, tempor mi at, euismod nibh.",
  Values:
    "<ul><li>Nunc iaculis</li><li>Donec dictum fringilla</li><li>Duis convallis tortor ultrices</li><li>Curabitur in est lectus</li><li>Maecenas condimentum elit</li></ul>",
};

let unselectedColor = "#646872";
let selectedColor = "#2A2D34";

const aboutUsTabs = document.querySelectorAll(".single-tab");

for (let i = 0; i < aboutUsTabs.length; i++) {
  aboutUsTabs[i].onclick = function () {
    let clickedTabValue = this.innerHTML;
    document.querySelector("#box-text").innerHTML = aboutUs[clickedTabValue];

    for (let i = 0; i < aboutUsTabs.length; i++) {
      aboutUsTabs[i].style.backgroundColor = unselectedColor;
      aboutUsTabs[i].style.fontWeight = "normal";
    }

    this.style.backgroundColor = selectedColor;
    this.style.fontWeight = "bold";
  };
}

// Service slider
let ourServices = [
  {
    title: "Web design",
    text:
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent finibus tincidunt sem non sodales. Nunc et quam in magna vehicula sollicitudin. Aliquam erat volutpat. Maecenas dolor mi, aliquet ac quam aliquet, condimentum dictum nisi.",
  },

  {
    title: "Branding",
    text:
      "Praesent finibus tincidunt sem non sodales. Nunc et quam in magna vehicula sollicitudin. Aliquam erat volutpat. Maecenas dolor mi, aliquet ac quam aliquet, condimentum dictum nisi.",
  },

  {
    title: "Digital Marketing",
    text:
      "Nunc et quam in magna vehicula sollicitudin. Aliquam erat volutpat. Maecenas dolor mi, aliquet ac quam aliquet, condimentum dictum nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent finibus.",
  },
  {
    title: "Wait What",
    text:
      "Yaas Nunc et quam in magna vehicula sollicitudin. Aliquam erat volutpat. Maecenas dolor mi, aliquet ac quam aliquet, condimentum dictum nisi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent finibus.",
  },
];

const servicesNext = document.querySelector("#service-next"),
  servicesPrev = document.querySelector("#service-previous"),
  serviceTitle = document.querySelector("#service-title"),
  serviceText = document.querySelector("#service-text");

let currentService = 0;

servicesNext.onclick = function () {
  if (currentService < ourServices.length - 1) {
    currentService++;
  } else {
    currentService = 0;
  }
  serviceTitle.innerHTML = ourServices[currentService].title;
  serviceText.innerHTML = ourServices[currentService].text;
};

servicesPrev.onclick = function () {
  if (currentService > 0) {
    currentService--;
  } else {
    currentService = ourServices.length - 1;
  }
  serviceTitle.innerHTML = ourServices[currentService].title;
  serviceText.innerHTML = ourServices[currentService].text;
};

// Footer
// let getCurrentDate = new Date();
// let currentYear = getCurrentDate.getFullYear();
document.querySelector(
  "#current_year"
).innerHTML = new Date().getFullYear().toString();
