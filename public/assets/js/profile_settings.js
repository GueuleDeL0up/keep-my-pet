(function () {
  const sidebar = document.querySelector(".settings-sidebar");
  if (!sidebar) return;

  const navItems = sidebar.querySelectorAll("nav ul li");
  const sections = {
    compte: document.querySelector(".settings-main"),
  };

  function showTab(tab) {
    // hide all specific sections first
    ["compte", "securite", "historique", "preferences"].forEach((id) => {
      const el = document.getElementById(id);
      if (!el) return;
      if (id === tab) {
        el.classList.remove("hidden");
      } else {
        el.classList.add("hidden");
      }
    });
    // mark active
    navItems.forEach((li) =>
      li.classList.toggle("active", li.dataset.tab === tab)
    );
    // show main Compte view by default when tab is 'compte'
    if (tab === "compte") {
      document.getElementById("securite")?.classList.add("hidden");
    }
  }

  navItems.forEach((li) => {
    li.addEventListener("click", () => showTab(li.dataset.tab));
  });

  // default
  showTab("compte");

  // Save behaviour: validate and let the form submit to server
  const form = document.getElementById("accountForm");
  const toast = document.getElementById("toast");
  form?.addEventListener("submit", (ev) => {
    const email = form.querySelector('input[name="email"]').value.trim();
    if (!email || !email.includes("@")) {
      ev.preventDefault();
      showToast("Veuillez entrer une adresse e-mail valide");
      return;
    }
    // allow submit; optionally disable button to prevent double submit
    const btn = document.getElementById("saveBtn");
    if (btn) {
      btn.disabled = true;
      btn.textContent = "Enregistrement...";
    }
  });

  // Password form validation
  const pwdForm = document.getElementById("passwordForm");
  pwdForm?.addEventListener("submit", (ev) => {
    const current = pwdForm
      .querySelector('input[name="current_password"]')
      .value.trim();
    const newPwd = pwdForm.querySelector('input[name="new_password"]').value;
    const confirm = pwdForm.querySelector(
      'input[name="new_password_confirm"]'
    ).value;

    if (!current) {
      ev.preventDefault();
      showToast("Veuillez entrer votre mot de passe actuel");
      return;
    }
    if (newPwd.length < 6) {
      ev.preventDefault();
      showToast("Le mot de passe doit comporter au moins 6 caractères");
      return;
    }
    if (newPwd !== confirm) {
      ev.preventDefault();
      showToast("La confirmation du mot de passe ne correspond pas");
      return;
    }

    const btn = document.getElementById("passwordSaveBtn");
    if (btn) {
      btn.disabled = true;
      btn.textContent = "Mise à jour...";
    }
  });

  // Show toast when redirected back with saved flag
  if (typeof PROFILE_SAVED !== "undefined" && PROFILE_SAVED) {
    showToast("Modifications enregistrées");
  }

  // Theme and Font Management
  const themeSelect = document.getElementById("theme-select");
  const fontSelect = document.getElementById("font-select");
  
  // Load saved preferences
  function loadPreferences() {
    const savedTheme = localStorage.getItem("theme") || "light";
    const savedFont = localStorage.getItem("font") || "default";
    
    if (themeSelect) themeSelect.value = savedTheme;
    if (fontSelect) fontSelect.value = savedFont;
    
    applyFont(savedFont);
  }
  
  // Apply font to document
  function applyFont(font) {
    document.documentElement.setAttribute("data-font", font);
  }
  
  // Save all preferences
  window.savePreferences = function() {
    const selectedTheme = themeSelect.value;
    const selectedFont = fontSelect.value;
    
    localStorage.setItem("theme", selectedTheme);
    localStorage.setItem("font", selectedFont);
    
    if (window.applyTheme) {
      window.applyTheme(selectedTheme);
    }
    applyFont(selectedFont);
    
    showToast("Préférences enregistrées");
  }
  
  // Listen for font changes
  fontSelect?.addEventListener("change", (e) => {
    applyFont(e.target.value);
  });
  
  // Listen for theme changes
  themeSelect?.addEventListener("change", (e) => {
    if (window.applyTheme) {
      window.applyTheme(e.target.value);
    }
  });
  
  // Load preferences on page load
  loadPreferences();

  function showToast(msg) {
    toast.textContent = msg;
    toast.classList.add("show");
    toast.setAttribute("aria-hidden", "false");
    setTimeout(() => {
      toast.classList.remove("show");
      toast.setAttribute("aria-hidden", "true");
    }, 2600);
  }
})();
