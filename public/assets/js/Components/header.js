function myFunction() {
  var x = document.getElementById("myLinks");
  x.classList.toggle("active");
}

function changeLanguage() {
  var select = document.getElementById("lang-select");
  var flag = document.getElementById("flag");
  var lang = select.value;
  var base_url = "/keep-my-pet/";
  if (lang === "fr") {
    flag.src = base_url + "/public/assets/images/flags/fr.png";
  } else if (lang === "en") {
    flag.src = base_url + "/public/assets/images/flags/en.png";
  } else if (lang === "es") {
    flag.src = base_url + "/public/assets/images/flags/es.png";
  }
}
