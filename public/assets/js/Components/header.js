function myFunction() {
  var x = document.getElementById("myLinks");
  x.classList.toggle("active");
}

function changeLanguage() {
  var select = document.getElementById("lang-select");
  var lang = select.value;
  var base_url = "/keep-my-pet/";
  
  console.log('Changing language to:', lang);
  
  // Send to backend and save
  fetch(base_url + 'app/API/changeLanguage.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'lang=' + encodeURIComponent(lang)
  })
  .then(response => {
    console.log('Response status:', response.status);
    return response.json();
  })
  .then(data => {
    console.log('Response data:', data);
    if (data.success) {
      console.log('Language changed, reloading...');
      // Reload page to apply language changes
      setTimeout(() => {
        window.location.href = window.location.href;
      }, 500);
    } else {
      console.error('Error:', data.error);
    }
  })
  .catch(error => {
    console.error('Fetch error:', error);
  });
}


