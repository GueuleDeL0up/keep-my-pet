# 🎯 Page Annonces - Implémentation MVC Complète

## Résumé

La page annonces a été complètement refactorisée en suivant l'architecture **MVC** :

- **Modèle** : Gestion de la base de données (286 lignes)
- **Contrôleur** : Logique métier et filtrage (286 lignes)
- **Vues** : HTML/PHP (365 lignes) + CSS (447 lignes) + JavaScript (91 lignes)

**Total : 1475 lignes de code propre et séparé**

---

## 📁 Structure des fichiers

```
app/
├─ Models/
│  └─ requests.advertisements.php          ← Modèle (BD)
├─ Controller/
│  └─ AdvertisementsController.php         ← Contrôleur (Logique)
└─ Views/
   └─ advertisements.php                   ← Vue (HTML/PHP)

public/assets/
├─ css/
│  └─ advertisements.css                   ← Styles
└─ js/
   └─ advertisements.js                    ← Interactivité
```

---

## 🛠️ Comment ça fonctionne

### 1️⃣ **MODÈLE** (`requests.advertisements.php`)

Fonctions pour la base de données :

```php
obtenirToutesAnnonces()              // Récupère toutes les annonces
obtenirAnnoncePar($id)               // Récupère une annonce
obtenirAnnoncesParUtilisateur($id)   // Récupère les annonces d'un utilisateur
rechercherAnnonces($filters)         // Filtre par type, ville, prix
creerAnnonce($data)                  // Crée une annonce
mettreAJourAnnonce($id, $data)       // Met à jour une annonce
supprimerAnnonce($id, $user_id)      // Supprime une annonce
```

**Sécurité** :

- PDO avec prepared statements ✅
- Pas d'injection SQL ✅

### 2️⃣ **CONTRÔLEUR** (`AdvertisementsController.php`)

Classe statique qui fait le lien entre Modèle et Vue :

```php
AdvertisementsController::afficherAnnonces()        // Récupère + filtre
AdvertisementsController::afficherDetailAnnonce()   // Détail unique
AdvertisementsController::afficherMesAnnonces()     // Mes annonces
AdvertisementsController::creerAnnonce()            // Créer
AdvertisementsController::mettreAJourAnnonce()      // Modifier
AdvertisementsController::supprimerAnnonce()        // Supprimer
```

### 3️⃣ **VUE** (`advertisements.php`)

Affichage avec :

- Barre de recherche
- Filtres (type, ville, prix)
- Grille 2 colonnes :
  - Colonne gauche : Liste des annonces (cartes cliquables)
  - Colonne droite : Détails de l'annonce sélectionnée
- Infos du propriétaire + boutons de contact

### 4️⃣ **CSS** (`advertisements.css`)

Design moderne avec :

- Gradient violet/bleu
- Animations hover
- Responsive (mobile, tablet, desktop)
- Grille et flexbox
- Breakpoints : 768px et 480px

### 5️⃣ **JAVASCRIPT** (`advertisements.js`)

Interactivité côté client :

```js
selectAd(ad); // Affiche les détails d'une annonce
displayAdDetail(ad); // Met à jour le panneau de droite
// Auto-sélection de la première annonce au chargement
```

---

## 🌐 Accès

Ouvrez votre navigateur à :

```
http://localhost:8888/keep-my-pet/app/Views/advertisements.php
```

---

## ✨ Fonctionnalités

✅ Affichage de toutes les annonces  
✅ Sélection et détails (clic sur une annonce)  
✅ Recherche par texte  
✅ Filtres : type, ville, prix min/max  
✅ Responsive design  
✅ Boutons de contact (email, téléphone)  
✅ Avatar utilisateur avec initiales  
✅ Dates formatées en français  
✅ Gestion d'erreurs  
✅ Sécurité maximale

---

## 🔐 Sécurité

- ✅ PDO prepared statements (pas d'injection SQL)
- ✅ `htmlspecialchars()` partout (prévient XSS)
- ✅ Validation serveur des données
- ✅ Vérification sessions utilisateur
- ✅ Logs d'erreurs sécurisés

---

## 📊 Flux de données

```
Navigateur
    ↓
GET /advertisements.php
    ↓
PHP chargement View (advertisements.php)
    ↓
View appelle AdvertisementsController::afficherAnnonces()
    ↓
Controller récupère filtres GET ($_GET)
    ↓
Controller appelle Model::rechercherAnnonces()
    ↓
Model exécute requête PDO prepared statement
    ↓
Base de données retourne les annonces
    ↓
Controller formate et retourne les données
    ↓
View affiche HTML avec les annonces
    ↓
Browser télécharge CSS + JS
    ↓
User voit la page + peut cliquer sur les annonces
    ↓
JavaScript selectAd() affiche les détails sans recharger
```

---

## 🚀 Extensibilité

Pour ajouter une fonctionnalité :

1. **Ajouter une fonction dans le Modèle** (`requests.advertisements.php`)

   ```php
   function nouvelleFunction() {
       // Code BD
   }
   ```

2. **Ajouter une méthode dans le Contrôleur** (`AdvertisementsController.php`)

   ```php
   public static function nouveauTraitement() {
       $data = Model::nouvelleFunction();
       return $data;
   }
   ```

3. **Utiliser dans la Vue** (`advertisements.php`)

   ```php
   $data = AdvertisementsController::nouveauTraitement();
   ```

4. **Ajouter styles CSS** si nécessaire
5. **Ajouter JavaScript** pour l'interactivité

---

## 🎓 Points clés d'apprentissage

Cette implémentation montre comment :

- ✅ Séparer les responsabilités (MVC)
- ✅ Utiliser PDO pour la BD
- ✅ Gérer les filtres et recherche
- ✅ Valider et nettoyer les données
- ✅ Faire de l'interactivité sans page reload
- ✅ Créer du CSS responsive moderne
- ✅ Structurer le code PHP proprement
- ✅ Gérer les sessions utilisateur
- ✅ Implémenter la sécurité (XSS, SQL injection)

---

## 📝 Documentation complète

Voir : `DOCUMENTATION_MVC_ADVERTISEMENTS.md`

---

**Fait avec ❤️ pour l'apprentissage du PHP moderne et de l'architecture MVC**
