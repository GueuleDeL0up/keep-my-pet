/**
 * KeepMyPet Advertisements Page JavaScript
 * Handles advertisement selection and display logic
 */

console.log('advertisements.js LOADED!');

// Store current selected advertisement
let currentAd = null;

/**
 * Select and display advertisement details
 * @param {Object} ad Advertisement data
 */
function selectAd(ad) {
  console.log('selectAd called');
  
  // Store the selected advertisement
  currentAd = ad;

  // Update active card state
  const cards = document.querySelectorAll('.ad-card');
  cards.forEach(card => card.classList.remove('active'));
  event.currentTarget.classList.add('active');

  // Update detail display
  displayAdDetail(ad);

  // Show detail section, hide empty state
  document.getElementById('ad-detail').style.display = 'block';
  document.getElementById('ad-empty').style.display = 'none';
}

/**
 * Display advertisement details in the right panel
 * @param {Object} ad Advertisement data
 */
function displayAdDetail(ad) {
  console.log('displayAdDetail called with user_id:', ad.user_id);
  
  // Format dates
  const startDate = new Date(ad.start_date);
  const endDate = new Date(ad.end_date);
  const dateFormatter = new Intl.DateTimeFormat('fr-FR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });

  // Update header
  document.getElementById('detail-title').textContent = ad.title;
  document.getElementById('detail-type').textContent =
    ad.type === 'gardiennage' ? 'Gardiennage à domicile' : 'Promenade';

  // Update description
  document.getElementById('detail-description').textContent = ad.description;

  // Update details grid
  document.getElementById('detail-city').textContent = ad.city;
  document.getElementById('detail-price').textContent = `${Number(ad.price).toFixed(2)}€`;
  document.getElementById('detail-start').textContent =
    `${dateFormatter.format(startDate)} à ${ad.start_hour}`;
  document.getElementById('detail-end').textContent =
    `${dateFormatter.format(endDate)} à ${ad.end_hour}`;
  document.getElementById('detail-animal').textContent = ad.animal_name || 'Non spécifié';
  document.getElementById('detail-race').textContent = ad.animal_race || 'Non spécifiée';

  // Update user info - NAME AS CLICKABLE LINK
  const userName = `${ad.first_name || 'Utilisateur'} ${ad.last_name || ''}`.trim();
  const userNameLink = document.getElementById('user-name-link');
  if (userNameLink) {
    userNameLink.textContent = userName;
    if (ad.user_id) {
      userNameLink.href = `/keep-my-pet/app/Views/user_profile.php?id=${ad.user_id}`;
      console.log('Set link to:', userNameLink.href);
    }
  }

  // Update phone
  const userPhoneEl = document.getElementById('user-phone');
  if (userPhoneEl) {
    userPhoneEl.textContent = ad.phone_number || 'Non fourni';
  }

  // Update avatar with initials
  const initials = (ad.first_name?.[0] || '?') + (ad.last_name?.[0] || '');
  const userAvatarEl = document.getElementById('user-avatar');
  if (userAvatarEl) {
    userAvatarEl.textContent = initials.toUpperCase();
  }

  // Update contact buttons
  if (ad.email) {
    const btnMessage = document.getElementById('btn-message');
    if (btnMessage) {
      btnMessage.href = `mailto:${encodeURIComponent(ad.email)}`;
    }
  }

  if (ad.phone_number) {
    const btnPhone = document.getElementById('btn-phone');
    if (btnPhone) {
      btnPhone.href = `tel:${ad.phone_number}`;
    }
  }
}

/**
 * Auto-select first advertisement if available
 */
document.addEventListener('DOMContentLoaded', function() {
  const firstCard = document.querySelector('.ad-card');
  if (firstCard) {
    // Simulate click on first card
    firstCard.click();
  }
});
