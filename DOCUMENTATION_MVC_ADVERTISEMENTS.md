# Architecture MVC - Page Annonces

## Vue d'ensemble

La page Annonces (`advertisements.php`) suit le modèle MVC (Modèle-Vue-Contrôleur) pour une séparation nette des responsabilités.

## Structure

### 1. **Modèle** (`app/Models/requests.advertisements.php`)

Le modèle gère toutes les interactions avec la base de données.

#### Fonctions principales :

- **`obtenirToutesAnnonces()`** - Récupère toutes les annonces avec les informations utilisateur et animal
- **`obtenirAnnoncePar($id)`** - Récupère une annonce spécifique par ID
- **`obtenirAnnoncesParUtilisateur($user_id)`** - Récupère les annonces d'un utilisateur
- **`creerAnnonce($data)`** - Crée une nouvelle annonce
- **`mettreAJourAnnonce($id, $data)`** - Met à jour une annonce
- **`supprimerAnnonce($id, $user_id)`** - Supprime une annonce
- **`rechercherAnnonces($filters)`** - Recherche les annonces avec filtres

#### Caractéristiques :

- Utilise PDO pour les requêtes sécurisées (prepared statements)
- Gestion d'erreurs avec try/catch
- Logs d'erreurs en cas de problème
- Jointures avec les tables `utilisateurs` et `animals`

```php
// Exemple d'utilisation
$annonces = obtenirToutesAnnonces();
$filtered = rechercherAnnonces([
    'type' => 'gardiennage',
    'city' => 'Paris',
    'min_price' => 20
]);
```

### 2. **Contrôleur** (`app/Controller/AdvertisementsController.php`)

Le contrôleur fait le lien entre le modèle et la vue. Il récupère les données du modèle et les prépare pour la vue.

#### Méthodes statiques :

- **`afficherAnnonces()`** - Récupère et filtre les annonces pour l'affichage
- **`afficherDetailAnnonce($id)`** - Récupère les détails d'une annonce
- **`afficherMesAnnonces()`** - Récupère les annonces de l'utilisateur connecté
- **`creerAnnonce()`** - Traite la création (validation, sanitization, insertion)
- **`mettreAJourAnnonce($id)`** - Traite la mise à jour
- **`supprimerAnnonce($id)`** - Traite la suppression

#### Exemple d'utilisation dans la vue :

```php
// Dans advertisements.php
$data = AdvertisementsController::afficherAnnonces();
$annonces = $data['annonces'] ?? [];
$filters = $data['filters'] ?? [];
$count = $data['count'] ?? 0;
```

### 3. **Vue** (`app/Views/advertisements.php`)

La vue affiche les données fournies par le contrôleur.

#### Structure HTML :

1. **Barre de recherche** - Formulaire GET pour la recherche en temps réel
2. **Filtres** - Sélecteurs déroulants et champs de texte pour filtrer
3. **Grille à deux colonnes** :
   - **Colonne gauche** : Liste des annonces (cartes cliquables)
   - **Colonne droite** : Détails de l'annonce sélectionnée
4. **Section utilisateur** : Informations du propriétaire avec boutons de contact

#### Caractéristiques :

- Utilise `htmlspecialchars()` pour prévenir les injections XSS
- Gestion des sessions pour la connexion utilisateur
- Affichage conditionnel (bouton dépôt annonce si connecté)
- Dates formatées en français

### 4. **Styles** (`public/assets/css/advertisements.css`)

CSS moderne et responsive avec :

- Gradients linéaires (violet/bleu)
- Animations hover
- Grid et flexbox pour le layout
- Design mobile-first
- Breakpoints responsifs (768px, 480px)

### 5. **JavaScript** (`public/assets/js/advertisements.js`)

JavaScript pour l'interactivité client-side :

- **`selectAd(ad)`** - Sélectionne une annonce et affiche ses détails
- **`displayAdDetail(ad)`** - Met à jour le panneau de droite avec les détails
- Auto-sélection de la première annonce au chargement

## Flux de données

```
Utilisateur clique sur le lien "Annonces"
        ↓
PHP charge advertisements.php
        ↓
Vue appelle AdvertisementsController::afficherAnnonces()
        ↓
Contrôleur récupère les filtres GET
        ↓
Contrôleur appelle Modèle::obtenirToutesAnnonces() ou rechercherAnnonces()
        ↓
Modèle exécute requête SQL + PDO
        ↓
Données retournées au contrôleur
        ↓
Contrôleur formate et retourne les données
        ↓
Vue affiche les annonces dans la liste
        ↓
Utilisateur clique sur une annonce
        ↓
JavaScript selectAd() affiche les détails
        ↓
Utilisateur clique "Envoyer un message"
        ↓
Email ou formulaire de contact
```

## Base de données

### Table `advertisements`

```sql
CREATE TABLE advertisements (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  animal_id INT NOT NULL,
  title VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  start_date DATE NOT NULL,
  start_hour TIME NOT NULL,
  end_date DATE NOT NULL,
  end_hour TIME NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  type ENUM('gardiennage','promenade') NOT NULL,
  FOREIGN KEY (user_id) REFERENCES utilisateurs(id),
  FOREIGN KEY (animal_id) REFERENCES animals(id)
);
```

## Sécurité

- ✅ Utilisation de PDO avec prepared statements
- ✅ Sanitization avec `htmlspecialchars()`
- ✅ Validation des données côté serveur
- ✅ Vérification des sessions pour l'accès utilisateur
- ✅ Logs d'erreurs sécurisés (pas d'exposition de détails aux utilisateurs)

## Extensibilité

Pour ajouter une nouvelle fonctionnalité :

1. **Ajouter une fonction dans le Modèle** (`requests.advertisements.php`)
2. **Ajouter une méthode dans le Contrôleur** (`AdvertisementsController.php`)
3. **Utiliser les données dans la Vue** (`advertisements.php`)
4. **Ajouter les styles CSS** si nécessaire
5. **Ajouter le JavaScript** pour l'interactivité

## Tests

Accédez à la page à : `http://localhost:8888/keep-my-pet/app/Views/advertisements.php`

Les annonces s'affichent si la base de données contient des données dans la table `advertisements`.
