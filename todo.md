# SETUP (Rohan et Tahina)
- [x] creation du projet CodeIgniter 
- [x] installation de la base sqlite
- [x] configuration de l'environnement

# Base 
- [ ] creation de la base sqlite (Rohan)
- [ ] creation des tables (Tahina)
- [ ] insertion des donnees de test (Tahina)

# Flow
## Authentification
- [x] creation de AuthController (Rohan)
  - [x] login
  - [x] logout
- [x] creation de AuthFilter et RoleFilter (Rohan)
- [x] creation des routes principales (/employe, /rh, /admin) (Tahina)
- [x] implementation des password_hash et  verify (Rohan)
- [x] creation de vue login (Tahina)


## Dashboard
- [x] a afficher (rohan)
  - [x] demande en attente
  - [x] demande approuvee
  - [x] nombre de conges restant
  - [x] demande refusee
  - [x] Congees de l'utilisateur connecte
  - [x] liste de demande de congee
- [ ] vue d'affichage (Tahina)


## Demande De Conge
- [ ] foonction qui redirige vers la page
  - [ ] dropdown type de congee (list de typeConge)
- [ ] route get pour aller vers la page
- [ ] route post pour envoyer les datas

## Page de demande perso
- [ ] fonction qui redirige vers la page
  - [ ] liste de conge et leur statut
- [ ] ajax pour le filtre de statut

## Vue d'ensemble
- [ ] liste d'employés
- [ ] nombre de demande total
  - [ ] approuve
  - [ ] en attente
- [ ] nombre de departement
- [ ] nombre d'absence 
- [ ] liste de demande recentes

## Model (Rohan)
- [x] conges
- [x] departements
- [x] employes
- [x] soldes
- [x] typesConge