-- ============================================================
-- DONNÉES DE TEST POUR KeepMyPet
-- ============================================================

-- 1. Ajouter quelques utilisateurs de test
INSERT INTO utilisateurs (first_name, last_name, email, password, phone_number, address, postal_code, gender, is_admin, theme, language, created_at)
VALUES 
('Jean', 'Dupont', 'jean@example.com', '$2y$10$EIXvqxXjWxrN/ZI0Hu1fGO.6vwAI3VrGf.vlmF8qGxN3aHhL2KMKO', '0612345678', '123 Rue de Paris', '75001', 'male', 0, 'light', 'fr', NOW()),
('Marie', 'Martin', 'marie@example.com', '$2y$10$EIXvqxXjWxrN/ZI0Hu1fGO.6vwAI3VrGf.vlmF8qGxN3aHhL2KMKO', '0623456789', '456 Avenue Lyon', '69001', 'female', 0, 'light', 'fr', NOW()),
('Pierre', 'Bernard', 'pierre@example.com', '$2y$10$EIXvqxXjWxrN/ZI0Hu1fGO.6vwAI3VrGf.vlmF8qGxN3aHhL2KMKO', '0634567890', '789 Boulevard Nice', '06000', 'male', 0, 'light', 'fr', NOW()),
('Sophie', 'Thomas', 'sophie@example.com', '$2y$10$EIXvqxXjWxrN/ZI0Hu1fGO.6vwAI3VrGf.vlmF8qGxN3aHhL2KMKO', '0645678901', '321 Chemin Marseille', '13000', 'female', 0, 'light', 'fr', NOW());

-- 2. Ajouter des animaux (propriétaires des annonces)
INSERT INTO animals (user_id, name, race, gender, birthdate, note)
VALUES 
(1, 'Rocky', 'Labrador Retriever', 'male', '2019-05-15', 4.8),
(2, 'Milo', 'Chat Persan', 'male', '2020-03-20', 4.5),
(3, 'Bella', 'Golden Retriever', 'female', '2018-07-10', 5.0),
(4, 'Whiskers', 'Chat Roux', 'male', '2021-11-05', 4.9),
(1, 'Luna', 'Border Collie', 'female', '2019-01-22', 4.7),
(2, 'Charlie', 'Cocker Spaniel', 'male', '2020-06-14', 4.6),
(3, 'Max', 'Husky', 'male', '2017-09-30', 4.9),
(4, 'Nala', 'Chat Bengale', 'female', '2022-02-28', 5.0);

-- 3. Ajouter des annonces
INSERT INTO advertisements (user_id, animal_id, title, description, city, start_date, start_hour, end_date, end_hour, price, type)
VALUES 
(1, 1, 'Garde d''un chien labrador pendant les vacances', 
 'Je recherche une personne de confiance pour garder mon labrador Rocky pendant mes vacances. Il est très sociable et adore les promenades. Fourniture de nourriture et accessoires assurée.',
 'Paris', '2025-12-20', '09:00:00', '2025-12-27', '18:00:00', 25.00, 'gardiennage'),

(2, 2, 'Promenade quotidienne pour chat d''intérieur',
 'Mon chat Milo a besoin de compagnie et de stimulation mentale. Je recherche quelqu''un pour jouer avec lui et le distraire pendant la journée. Chat calme et affectueux.',
 'Lyon', '2025-11-15', '14:00:00', '2025-11-30', '17:00:00', 15.00, 'promenade'),

(3, 3, 'Garde complète Golden Retriever - Week-end',
 'Bella est une chienne adorable qui adore les autres chiens et les enfants. Week-end joyeux garanti ! Nourritures spéciales fournies.',
 'Marseille', '2025-11-22', '09:00:00', '2025-11-24', '18:00:00', 30.00, 'gardiennage'),

(4, 4, 'Visite chat - Nourrissage et jeux',
 'Whiskers est un chat très indépendant mais qui aime qu''on s''occupe de lui. Visite simple : nourrir et jouer un peu. Très peu contraignant.',
 'Nice', '2025-11-10', '18:00:00', '2025-11-14', '10:00:00', 12.00, 'promenade'),

(1, 5, 'Garde Border Collie très énergique - Sport et jeux',
 'Luna a besoin d''exercice quotidien et de beaucoup de stimulation mentale. Idéal pour quelqu''un d''actif ! Elle adore courir et faire des jeux.',
 'Toulouse', '2025-12-01', '08:00:00', '2025-12-08', '20:00:00', 28.00, 'gardiennage'),

(2, 6, 'Promenade Cocker Spaniel - Parc et rivière',
 'Charlie adore l''eau et les longues promenades. Parfait pour les amoureux de nature. Chien très affectueux et obéissant.',
 'Bordeaux', '2025-11-25', '10:00:00', '2025-11-25', '14:00:00', 18.00, 'promenade'),

(3, 7, 'Garde Husky - Besoin de grand espace',
 'Max est un husky magnifique mais très énergique. Il a besoin d''un grand espace ou au minimum 2-3 heures de promenade par jour.',
 'Grenoble', '2025-12-15', '09:00:00', '2025-12-22', '18:00:00', 35.00, 'gardiennage'),

(4, 8, 'Garde chat exotic - Attention particulière',
 'Nala est une belle chat bengale avec un caractère affirmé. Nécessite de l''attention et des jeux réguliers. Excellent pour les amateurs de chats.',
 'Nantes', '2025-11-18', '08:00:00', '2025-11-21', '18:00:00', 20.00, 'gardiennage');

-- ============================================================
-- Notes importantes :
-- ============================================================
-- Les mots de passe sont tous : "password123" (hashé en bcrypt)
-- Vous pouvez tester avec ces comptes pour vérifier les annonces
-- Les annonces ont des dates futures pour avoir du contenu actuel
-- 
-- Pour se connecter :
-- Email: jean@example.com, Mot de passe: password123
-- Email: marie@example.com, Mot de passe: password123
-- Email: pierre@example.com, Mot de passe: password123
-- Email: sophie@example.com, Mot de passe: password123
-- ============================================================
