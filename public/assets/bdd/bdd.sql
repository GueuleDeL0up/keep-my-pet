CREATE DATABASE  IF NOT EXISTS keepMyPet CHARACTER SET utf8mb4 COLLATE utf8mb4_general_cli;

USE keepMyPet;

DROP TABLE IF EXISTS Advertisement;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Historical;
DROP TABLE IF EXISTS Own;
DROP TABLE IF EXISTS Animal;
DROP TABLE IF EXISTS FAQ;
DROP TABLE IF EXISTS Legal_Notice;
DROP TABLE IF EXISTS CGU;

CREATE TABLE FAQ(
  id SERIAL PRIMARY KEY,
  question varchar(100) NOT NULL, -- NOT NULL permet de ne pas avoir de champs vide 
  answer varchar(100) NOT NULL
)

CREATE TABLE CGU(
  id SERIAL PRIMARY KEY,
  -- ajouter les champs (on doit les définir)
)

CREATE TABLE Legal_Notice(
  id SERIAL PRIMARY KEY,
  description varchar(100) NOT NULL
)

CREATE TABLE User(
  id SERIAL PRIMARY KEY,
  mail varchar (20) NOT NULL UNIQUE,
  password (32) NOT NULL,
  first_name varchar (32) NOT NULL,
  last_name varchar (32) NOT NULL,
  role varchar (4) NOT NULL DEFAULT 'user',
  gender enum ('Homme', 'Femme') NOT NULL,
  note float NOT NULL,
  theme enum ('white','dark') NOT NULL, 
  lang enum ('fr','eng') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

)

CREATE TABLE Animal(
  id SERIAL PRIMARY KEY,
  name varchar (20) NOT NULL,
  race varchar (20) NOT NULL,
  gender enum ('male', 'femelle', 'female') NOT NULL,
  birthdate DATE NOT NULL,
  note float NOT NULL,
  FOREIGN KEY (user_id), REFERENCES user(id) ON DELETE CASCADE 
)

CREATE TABLE Own(
  id SERIAL PRIMARY KEY,
  FOREIGN KEY (user_id), REFERENCES user(id) ON DELETE CASCADE,
  FOREIGN KEY (animal_id), REFERENCES animal(id) ON DELETE CASCADE
)

CREATE TABLE Historical(
  id SERIAL PRIMARY KEY,
  note float NOT NULL,
  comment varchar(200) NOT NULL,
  FOREIGN KEY (user_id), REFERENCES user(id) ON DELETE CASCADE
)

CREATE TABLE Advertisement(
  id SERIAL PRIMARY KEY,
  title varchar (30) NOT NULL,
  description TEXT NOT NULL,
  localisation TEXT NOT NULL,
  start_date DATE NOT NULL, -- à modifier, je pense que le schéma n'est pas correct ici
  start_hour int NOT NULL, -- on pourrait avoir la date et l'heure seulement 
  end_date DATE NOT NULL, -- avec le type DATE de mysql
  end_hour int NOT NULL,
  price float NOT NULL,
  type enum('gardiennage', 'promenade') NOT NULL,
  FOREIGN KEY (animal_id), REFERENCES animal(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id), REFERENCES user(id) ON DELETE CASCADE
)