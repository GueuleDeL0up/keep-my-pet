// Toggle options menu
function toggleOptionsMenu() {
  const optionsMenu = document.getElementById("optionsMenu");
  optionsMenu.classList.toggle("active");
}

// Close options menu when clicking outside
document.addEventListener("click", function (event) {
  const optionsMenu = document.getElementById("optionsMenu");
  const optionsBtn = document.querySelector(".options-btn");

  if (
    optionsMenu &&
    !optionsMenu.contains(event.target) &&
    !optionsBtn.contains(event.target)
  ) {
    optionsMenu.classList.remove("active");
  }
});

// Report user function
function reportUser() {
  alert(
    "Merci de nous avoir signalé cet utilisateur. Notre équipe examinera ce signalement."
  );
  // Close menu after action
  document.getElementById("optionsMenu").classList.remove("active");

  // fetch('/keep-my-pet/api/report-user', {
  //   method: 'POST',
  //   headers: {
  //     'Content-Type': 'application/json'
  //   },
  //   body: JSON.stringify({ reason: 'report' })
  // })
}

// Delete account function (admin only)
function deleteAccount() {
  const confirmed = confirm(
    "Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible."
  );

  if (confirmed) {
    document.getElementById("optionsMenu").classList.remove("active");

    // fetch('/keep-my-pet/api/admin/delete-account', {
    //   method: 'POST',
    //   headers: {
    //     'Content-Type': 'application/json'
    //   },
    //   body: JSON.stringify({ userId: userId })
    // })
    // .then(response => response.json())
    // .then(data => {
    //   alert('Le compte a été supprimé avec succès.');
    //   // Redirect to home or admin panel
    //   window.location.href = '/keep-my-pet/';
    // })
    // .catch(error => {
    //   alert('Erreur lors de la suppression du compte.');
    //   console.error('Error:', error);
    // })

    alert("Le compte a été supprimé avec succès.");
  }
}
