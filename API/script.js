"use_strict";

let response = document.querySelector("#api-response"),
  xhr = new XMLHttpRequest(),
  url = "https://api.chucknorris.io/jokes/random";

xhr.onreadystatechange = function () {
  console.log(xhr.readyState);
  if (xhr.readyState === 4 && xhr.status === 200) {
    // console.log(xhr.response);
    // console.log(xhr.responseText);
    let obj = JSON.parse(xhr.responseText);
    response.innerHTML = obj.url;
  }
};

xhr.open("GET", url);
xhr.send();
console.log(xhr);
