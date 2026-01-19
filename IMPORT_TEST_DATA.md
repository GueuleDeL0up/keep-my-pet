# 🚀 Importer les données de test

## Comment charger les données de test dans la base de données

### Option 1 : Via le navigateur (Facile)

1. Démarrez MAMP
2. Accédez à : `http://localhost:8888/keep-my-pet/app/Models/load_test_data.php`
3. Le script charge automatiquement les données et affiche les comptes de test

### Option 2 : Via phpMyAdmin

1. Ouvrez phpMyAdmin : `http://localhost:8888/phpmyadmin`
2. Sélectionnez la base de données `keepMyPet`
3. Allez à l'onglet `SQL`
4. Copiez/collez le contenu de `test_data.sql`
5. Cliquez sur `Exécuter`

### Option 3 : Via la ligne de commande

```bash
cd /Applications/MAMP/htdocs/keep-my-pet
mysql -h 127.0.0.1 -P 8889 -u root -p keepMyPet < test_data.sql
```
Mot de passe : `root`

---

## 📊 Données créées

### Utilisateurs de test (4)
| Email | Mot de passe | Ville |
|-------|-------------|-------|
| jean@example.com | password123 | Paris |
| marie@example.com | password123 | Lyon |
| pierre@example.com | password123 | Marseille |
| sophie@example.com | password123 | Nice |

### Animaux (8)
- Rocky (Labrador) - Jean
- Milo (Chat Persan) - Marie
- Bella (Golden Retriever) - Pierre
- Whiskers (Chat Roux) - Sophie
- Luna (Border Collie) - Jean
- Charlie (Cocker Spaniel) - Marie
- Max (Husky) - Pierre
- Nala (Chat Bengale) - Sophie

### Annonces (8)
- Garde complète (6 annonces)
- Promenade/Visite (2 annonces)
- Villes : Paris, Lyon, Marseille, Nice, Toulouse, Bordeaux, Grenoble, Nantes
- Prix : entre 12€ et 35€/jour

---

## ✅ Vérification

Après l'import, allez à :
`http://localhost:8888/keep-my-pet/app/Views/advertisements.php`

Vous devriez voir les 8 annonces avec les détails complets !

---

## 🔄 Réinitialiser les données

Si vous voulez effacer les données de test et recommencer :

### Via phpMyAdmin
1. Allez dans `keepMyPet`
2. Sélectionnez toutes les tables sauf `utilisateurs`, `animals`, `advertisements`
3. Cliquez sur `Vider` pour chaque table

### Via ligne de commande
```bash
# Vider les tables
mysql -h 127.0.0.1 -P 8889 -u root -p -e "USE keepMyPet; DELETE FROM advertisements; DELETE FROM animals; DELETE FROM utilisateurs;"
```

Puis relancez l'import avec `load_test_data.php`

---

## 📝 Notes

- ✅ Les dates des annonces sont dans le futur (novembre/décembre 2025)
- ✅ Tous les animaux ont une note (4.5 à 5.0 étoiles)
- ✅ Les annonces ont des descriptions réalistes et variées
- ✅ Les emails sont au format test et ne reçoivent pas de vrais messages
- ✅ Les mots de passe sont tous `password123` (sans espaces)

---

**Besoin d'aide ?** Vérifiez que MAMP est démarré et que le port 8889 est accessible.
