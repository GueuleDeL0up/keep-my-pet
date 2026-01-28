document.addEventListener("DOMContentLoaded", function () {
  const followBtn = document.getElementById("followBtn");
  const starBtn = document.getElementById("starBtn");

  async function postJSON(url, data) {
    const form = new FormData();
    for (const k in data) form.append(k, data[k]);
    const resp = await fetch(url, { method: "POST", body: form });
    return resp.json();
  }

  if (followBtn) {
    followBtn.addEventListener("click", async function () {
      const target = followBtn.dataset.targetUserId;
      try {
        const res = await postJSON("/keep-my-pet/app/Api/follow.php", {
          target_user_id: target,
        });
        if (res.success) {
          if (res.following) {
            followBtn.classList.add("following");
            followBtn.textContent = "Suivi";
          } else {
            followBtn.classList.remove("following");
            followBtn.textContent = "Suivre";
          }
        } else {
          alert(res.message || "Erreur");
        }
      } catch (e) {
        alert("Erreur de communication");
      }
    });
  }

  if (starBtn) {
    starBtn.addEventListener("click", async function () {
      const target = starBtn.dataset.targetUserId;
      try {
        const res = await postJSON("/keep-my-pet/app/Api/favorite.php", {
          target_user_id: target,
        });
        if (res.success) {
          if (res.favorited) {
            starBtn.classList.add("starred");
            starBtn.textContent = "★";
          } else {
            starBtn.classList.remove("starred");
            starBtn.textContent = "☆";
          }
        } else {
          alert(res.message || "Erreur");
        }
      } catch (e) {
        alert("Erreur de communication");
      }
    });
  }

  // Edit profile button
  const editBtn = document.getElementById("editProfileBtn");
  if (editBtn) {
    editBtn.addEventListener("click", function () {
      window.location.href = "/keep-my-pet/app/Views/profile_edit.php";
    });
  }
  // Followers modal handling
  const showFollowersLink = document.getElementById("showFollowersLink");
  const followersModal = document.getElementById("followersModal");
  const closeFollowersModal = document.getElementById("closeFollowersModal");
  const followersList = document.getElementById("followersList");
  const prevBtn = document.getElementById("prevFollowers");
  const nextBtn = document.getElementById("nextFollowers");
  const pageInfo = document.getElementById("followersPageInfo");

  let followersPage = 1;
  const perPage = 10;
  const profileId = "<?php echo $profile_user_id; ?>";

  async function loadFollowers(page) {
    followersList.textContent = "Chargement…";
    try {
      const res = await fetch(
        `/keep-my-pet/app/Api/followers.php?user_id=${profileId}&page=${page}&per_page=${perPage}`
      );
      const data = await res.json();
      if (!data.success) {
        showToast(data.message || "Erreur");
        followersList.textContent = "Erreur";
        return;
      }
      const items = data.followers;
      if (items.length === 0) {
        followersList.innerHTML = "<p>Aucun follower</p>";
      } else {
        followersList.innerHTML = items
          .map(
            (u) =>
              `<div class="f-item"><strong>${escapeHtml(
                u.first_name
              )} ${escapeHtml(
                u.last_name
              )}</strong> <span class="muted">${escapeHtml(
                u.email
              )}</span></div>`
          )
          .join("");
      }
      pageInfo.textContent = `Page ${data.page} / ${Math.max(
        1,
        Math.ceil(data.total / data.per_page)
      )}`;
      prevBtn.disabled = data.page <= 1;
      nextBtn.disabled = data.page * data.per_page >= data.total;
    } catch (e) {
      showToast("Erreur de communication");
      followersList.textContent = "Erreur";
    }
  }

  function openFollowersModal() {
    followersModal.setAttribute("aria-hidden", "false");
    followersModal.classList.add("open");
    loadFollowers(followersPage);
  }
  function closeModal() {
    followersModal.setAttribute("aria-hidden", "true");
    followersModal.classList.remove("open");
  }

  if (showFollowersLink) {
    showFollowersLink.addEventListener("click", function (e) {
      e.preventDefault();
      openFollowersModal();
    });
  }
  if (closeFollowersModal)
    closeFollowersModal.addEventListener("click", closeModal);
  if (prevBtn)
    prevBtn.addEventListener("click", function () {
      if (followersPage > 1) {
        followersPage--;
        loadFollowers(followersPage);
      }
    });
  if (nextBtn)
    nextBtn.addEventListener("click", function () {
      followersPage++;
      loadFollowers(followersPage);
    });

  // Toast helper
  function showToast(msg) {
    const t = document.getElementById("toast");
    t.textContent = msg;
    t.classList.add("show");
    setTimeout(() => t.classList.remove("show"), 3000);
  }

  function escapeHtml(s) {
    return s
      ? s.replace(/[&<>"']/g, function (c) {
          return {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;",
          }[c];
        })
      : "";
  }
});
