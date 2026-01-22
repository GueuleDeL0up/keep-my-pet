-- Migration pour ajouter les colonnes de réinitialisation de mot de passe
-- À exécuter dans phpMyAdmin sur la base keep-my-pet

ALTER TABLE `users` 
ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL AFTER `language`,
ADD COLUMN `reset_token_expiry` DATETIME DEFAULT NULL AFTER `reset_token`;
