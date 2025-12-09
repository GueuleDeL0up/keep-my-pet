<?php require_once '/app/Views/header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>KeepMyPet - Sign Up</title>
<link rel="stylesheet" href="assets/css/main.css">
</head>
<body id="inscription">

<!-- Formes de fond -->
<div class="shape circle" style="top: 120px; left: 150px;"></div>
<div class="shape circle" style="top: 300px; left: 100px;"></div>
<div class="shape circle" style="bottom: 120px; left: 200px;"></div>
<div class="shape triangle" style="top: 250px; left: 350px;"></div>
<div class="shape triangle" style="bottom: 200px; left: 100px;"></div>
<div class="shape circle" style="top: 80px; right: 350px;"></div>
<div class="shape triangle" style="top: 200px; right: 220px;"></div>
<div class="shape circle" style="bottom: 140px; right: 300px;"></div>

<div class="container">

    <!-- Logo gauche -->
    <div class="left">
        <img src="assets/KeepMyPet_Logo.png" alt="Logo KeepMyPet">
    </div>

    <!-- Formulaire -->
    <div class="right">

        <label>MAIL</label>
        <input type="text" class="input">

        <div class="row">
            <div style="flex:1">
                <label>NOM</label>
                <input type="text" class="input">
            </div>

            <div style="flex:1">
                <label>PRÉNOM</label>
                <input type="text" class="input">
            </div>
        </div>

        <label>MOT DE PASSE</label>
        <input type="password" class="input">

        <label>CONFIRMER LE MOT DE PASSE</label>
        <input type="password" class="input">

        <button class="btn">S’inscrire</button>

        <div class="links">
            <a href="Connexion.html">Se connecter</a>
        </div>
    </div>
</div>

</body>
</html><?php require_once '/app/Views/footer.php'; ?>
