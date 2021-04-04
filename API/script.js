"use_strict";

// document.querySelector("button").onclick = function () {
//   let response = document.querySelector("#api-response"),
//     xhr = new XMLHttpRequest(),
//     url = "https://api.chucknorris.io/jokes/random";

//   xhr.onreadystatechange = function () {
//     console.log(xhr.readyState);
//     if (xhr.readyState === 4 && xhr.status === 200) {
//       // console.log(xhr.response);
//       // console.log(xhr.responseText);
//       let obj = JSON.parse(xhr.responseText);
//       console.log(obj);
//       response.innerHTML = obj.value;
//     }
//   };

//   xhr.open("GET", url);
//   xhr.send();
//   console.log(xhr);
// };

// document.querySelector("button").onclick = function () {
//   let response = document.querySelector("#api-response"),
//     xhr = new XMLHttpRequest(),
//     url = "https://api.chucknorris.io/jokes/random";

//   xhr.onreadystatechange = function () {
//     if (xhr.readyState === 4) {
//       if (xhr.status === 200) {
//         let obj = JSON.parse(xhr.responseText);
//         response.innerHTML = obj.value;
//       } else {
//         response.innerHTML = "ERROR";
//       }
//     }
//   };

//   xhr.open("GET", url);
//   xhr.send();

//   xhr.onprogress = function (e) {
//     const test = e.timeStamp;
//     console.log(realTime);
//     console.log(test);
//   };

//   xhr.onerror = function (e) {
//     console.log(e);
//   };

// xhr.addEventListener("progress", function (e) {
//   console.log(e);
// });
// };

// const btn = document.querySelector("button"),
//   input = document.querySelector("input"),
//   response = document.querySelector("#api-response"),
//   url = "https://randomuser.me/api/";

// btn.onclick = function () {
//   const xhr = new XMLHttpRequest(),
//     amountToGet = "?results=" + input.value;
//   xhr.onload = function () {
//     if (this.readyState === 4) {
//       if (this.status === 200) {
//         let data = JSON.parse(xhr.responseText).results;
//         getValues(data);
//       }
//     }
//   };
//   xhr.open("GET", url + amountToGet);
//   xhr.send();
// };

// function getValues(data) {
//   response.innerHTML = "<ul>";
//   for (i = 0; i < data.length; i++) {
//     response.innerHTML += "<li>" + data[i].email + "</li>";
//   }
//   response.innerHTML += "</ul>";
// }

// const btn = document.querySelector("button"),
//   input = document.querySelector("input"),
//   response = document.querySelector("#api-response"),
//   url = "https://randomuser.me/api/";

// btn.onclick = function () {
//   fetch(url)
//     .then(function (response) {
//       if (response.status === 200) {
//         return response.json();
//       } else {
//         console.log("Error");
//       }
//     })
//     .then(function (responseJson) {
//       console.log(responseJson);
//       console.log("***");
//       console.log(JSON.stringify(responseJson));
//     });
// };

// const btn = document.querySelector("button"),
//   input = document.querySelector("input"),
//   response = document.querySelector("#api-response"),
//   url = "https://randomuser.me/api/";

// btn.onclick = function () {
//   let amountToGet = "?results=" + input.value;
//   fetch(url + amountToGet)
//     .then(function (response) {
//       return response.json();
//       // if (response.status === 200) {
//       //   return response.json();
//       // } else {
//       //   console.log("Error");
//       // }
//     })
//     .then(function (responseJson) {
//       // console.log(responseJson.length); // undefined
//       console.log(responseJson.results.length);
//       response.innerHTML = "<ul>";
//       for (let i = 0; i < responseJson.results.length; i++) {
//         response.innerHTML +=
//           "<li>" + responseJson.results[i].name.first + "</li>";
//       }
//       response.innerHTML += "</ul>";
//     })
//     .catch(function (err) {
//       console.log(err);
//     });
// };

// const btn = document.querySelector("button"),
//   input = document.querySelector("input"),
//   response = document.querySelector("#api-response"),
//   url = "http://localhost/JavaScript Practice/API/v1/products/";

// btn.onclick = function () {
//   let amountToGet = input.value;
//   let params = new Request(url + amountToGet, {
//     method: "GET",
//     headers: new Headers().append("Content-Type", "application/json"),
//   });
//   fetch(params)
//     .then(function (response) {
//       return response.json();
//     })
//     .then(function (responseJson) {
//       for (let i = 0; i < responseJson.data.products.length; i++) {
//         response.innerHTML +=
//           JSON.stringify(responseJson.data.products[i]) + "<br/><br/>";
//       }
//     })
//     .catch(function (err) {
//       console.log(err);
//     });
// };

// const btn = document.querySelector("button"),
//   input = document.querySelector("input"),
//   response = document.querySelector("#api-response"),
//   url = "http://localhost/JavaScript Practice/API/v1/products/";

// btn.onclick = function () {
//   let amountToGet = input.value;
//   let params = new Request(url + amountToGet, {
//     method: "GET",
//   });
//   fetch(params)
//     .then((res) => res.json())
//     .then(
//       (results) => (response.innerHTML = JSON.stringify(results.data.products))
//     )
//     .catch((error) => (response.innerHTML = error));
// };

const btn = document.querySelector("button"),
  input = document.querySelector("input"),
  response = document.querySelector("#api-response"),
  url = "http://localhost/JavaScript Practice/API/v1/products/";

let myData = {};

fetch(url)
  .then(function (res) {
    return res.json();
  })
  .then(function (data) {
    myData = data.data.products;
    showResults(data.data.products);
  });

function showResults(data) {
  let select = document.createElement("select");
  data.forEach(function (item) {
    console.log(item);
  });
}
