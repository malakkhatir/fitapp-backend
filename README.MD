# 🏋️ FitApp — Backend API

> API REST en **PHP 8** et **MySQL** pour l'application mobile de fitness **FitApp**.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-XAMPP-CA4245?style=for-the-badge&logo=apache&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

---

## 📌 À propos

Ce dépôt contient l'**API REST** qui alimente l'application mobile **FitApp**. Elle gère :

- 🔐 L'**authentification** des utilisateurs (inscription, connexion, déconnexion)
- 🏋️ Le **catalogue d'exercices** (60 exercices répartis par zone et niveau)
- 📋 Les **programmes sportifs** (20 programmes standards + programmes personnalisés)
- ⭐ Les **favoris** des utilisateurs
- 💬 Les **commentaires** sur les exercices
- 🏆 Les **badges** (achievements) calculés dynamiquement
- 📸 L'**upload de photos de profil**
- 📊 L'**historique des séances** et la **progression** dans les programmes

L'application mobile (React Native + Expo) consomme cette API via des requêtes HTTP avec authentification par **token Bearer**.

🔗 **Frontend** : [github.com/malakkhatir/fit-app](https://github.com/malakkhatir/fit-app)

---

## ✨ Fonctionnalités

| Fonctionnalité | Description |
|---|---|
| 🔐 **Authentification** | Inscription, connexion, déconnexion avec hash bcrypt |
| 👤 **Profil utilisateur** | Modification du prénom, poids, taille, âge, objectif de poids |
| 🏋️ **Exercices** | 60 exercices avec filtres (zone, niveau, catégorie) |
| 📋 **Programmes** | 20 programmes + Création/Modification de programmes personnalisés |
| ⭐ **Favoris** | Ajout/Suppression d'exercices favoris |
| 💬 **Commentaires** | Ajout/Suppression de commentaires sur les exercices |
| 🏆 **Badges** | 10 badges débloqués selon l'activité (streak, séances, favoris...) |
| 📸 **Avatar** | Upload de photo de profil avec validation MIME |
| 📊 **Suivi de séances** | Enregistrement et suppression des séances réalisées |
| 🎯 **Progression programmes** | Déblocage séance par séance avec suivi en base |
| 🔒 **Sécurité** | Tokens Bearer, mots de passe hashés (bcrypt), PDO préparé |

---

## 🛠️ Stack technique

- **PHP** 8.2 (POO, PDO)
- **MySQL** 8.0 (InnoDB, utf8mb4)
- **Apache** (via XAMPP)
- **API REST** — échanges en JSON
- **Authentification** — Token Bearer (64 caractères hex)

---

## 📁 Structure du projet

```text
fitapp-backend/
├── config/
│   └── database.php              # Connexion PDO + helpers
├── api/
│   ├── register.php              # POST   — Inscription
│   ├── login.php                 # POST   — Connexion
│   ├── logout.php                # POST   — Déconnexion
│   ├── profile.php               # PUT    — Modifier profil
│   ├── exercises.php             # GET    — Liste exercices
│   ├── programs.php              # GET    — Liste programmes
│   ├── custom-program.php        # POST/PUT/DELETE — Programmes personnalisés
│   ├── favorites.php             # GET/POST/DELETE — Favoris
│   ├── comments.php              # GET/POST/DELETE — Commentaires
│   ├── badges.php                # GET    — Badges
│   ├── upload-avatar.php         # POST   — Upload photo
│   ├── sessions.php              # GET/POST/DELETE — Historique séances
│   └── program-progress.php      # GET/POST/DELETE — Progression programmes
├── uploads/
│   └── avatars/                  # Dossier de stockage des photos
├── sql/
│   └── fitapp.sql                # Script de création de la base + données
├── .gitignore
└── README.md
```

---

## 🚀 Installation

### Prérequis

- XAMPP (Apache + MySQL)
- Navigateur web (pour phpMyAdmin)

### Étape 1 — Cloner le dépôt

```bash
git clone https://github.com/malakkhatir/fitapp-backend.git
```

Place ensuite le dossier dans le répertoire XAMPP (`htdocs`) :

```text
C:/xampp/htdocs/fitapp-backend
```

### Étape 2 — Démarrer XAMPP

Ouvre le XAMPP Control Panel et démarre :

- ✅ Apache
- ✅ MySQL

### Étape 3 — Créer la base de données

1. Ouvre phpMyAdmin : <http://localhost/phpmyadmin/>
2. Clique sur l'onglet **Importer**
3. Sélectionne le fichier `sql/fitapp.sql`
4. Clique sur **Exécuter**

La base `fitapp` est créée avec :

- 8 tables : `users`, `exercises`, `programs`, `program_exercises`, `sessions`, `program_progress`, `favorites`, `comments`
- 60 exercices répartis par zone
- 20 programmes (Débutant, Intermédiaire, Avancé)

### Étape 4 — Configuration

La configuration par défaut dans `config/database.php` est :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'fitapp');
define('DB_USER', 'root');
define('DB_PASS', '');           // XAMPP : pas de mot de passe par défaut
```

Si ton installation MySQL a un mot de passe, modifie `DB_PASS`.

### Étape 5 — Vérifier l'installation

Ouvre ton navigateur :

```text
http://localhost/fitapp-backend/api/exercises.php
```

Tu dois voir un tableau JSON avec les 60 exercices. ✅

---

## 🔌 API Reference

### Base URL

```text
http://localhost/fitapp-backend/api
```

Depuis un téléphone (même Wi-Fi) :

```text
http://TON-IP-LOCALE/fitapp-backend/api
```

Exemple : `http://192.168.1.42/fitapp-backend/api`

### Authentification

Les endpoints protégés nécessitent un header :

```text
Authorization: Bearer TON_TOKEN
```

Le token est retourné par `/register.php` et `/login.php`.

---

## 📌 Endpoints

### 🔐 Authentification

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/register.php` | ❌ | Créer un compte |
| POST | `/login.php` | ❌ | Se connecter |
| POST | `/logout.php` | ✅ | Se déconnecter |

### 👤 Profil

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| PUT | `/profile.php` | ✅ | Modifier prénom, poids, taille, objectif |

### 🏋️ Exercices

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/exercises.php` | ❌ | Liste tous les exercices |

### 📋 Programmes

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/programs.php` | ❌ | Liste tous les programmes (standards + perso) |
| POST | `/custom-program.php` | ✅ | Créer un programme |
| PUT | `/custom-program.php?id=X` | ✅ | Modifier un programme |
| DELETE | `/custom-program.php?id=X` | ✅ | Supprimer un programme |

### ⭐ Favoris

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/favorites.php` | ✅ | Liste des favoris |
| POST | `/favorites.php` | ✅ | Ajouter un favori |
| DELETE | `/favorites.php?exerciseId=X` | ✅ | Supprimer un favori |

### 💬 Commentaires

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/comments.php?exerciseId=X` | ✅ | Liste des commentaires |
| POST | `/comments.php` | ✅ | Ajouter un commentaire |
| DELETE | `/comments.php?id=X` | ✅ | Supprimer son commentaire |

### 🏆 Badges

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/badges.php` | ✅ | Liste des badges + progression |

### 📸 Avatar

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/upload-avatar.php` | ✅ | Uploader une photo de profil |

### 📊 Séances (Historique)

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/sessions.php` | ✅ | Historique |
| POST | `/sessions.php` | ✅ | Ajouter une séance |
| DELETE | `/sessions.php?id=X` | ✅ | Supprimer une séance |

### 🎯 Progression programmes

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| GET | `/program-progress.php?programId=X` | ✅ | Sessions terminées |
| POST | `/program-progress.php` | ✅ | Marquer une session terminée |
| DELETE | `/program-progress.php?programId=X` | ✅ | Réinitialiser la progression |

---

## 🗄️ Schéma de la base de données

```text
users
├── id (PK)
├── prenom
├── email (UNIQUE)
├── mot_de_passe (hash bcrypt)
├── token (Bearer)
├── objectif_hebdo
├── poids, taille, age, sexe, target_weight, photo_url
└── created_at

exercises
├── id (PK)
├── nom, categorie, zone, niveau
├── duree, repetitions, description, instructions
├── icone, image_url

programs
├── id (PK)
├── user_id (FK vers users, NULL si standard)
├── is_custom (0/1)
├── titre, niveau, public, objectif
├── duree, description, icone, image_url

program_exercises
├── program_id (FK vers programs)
├── exercise_id (FK vers exercises)
└── ordre

sessions
├── id (PK)
├── user_id (FK vers users)
├── programme_id (FK vers programs)
├── nom_programme, date, minutes

program_progress
├── id (PK)
├── user_id (FK vers users)
├── program_id (FK vers programs)
├── session_index, completed_at

favorites
├── id (PK)
├── user_id (FK vers users)
├── exercise_id (FK vers exercises)
└── created_at

comments
├── id (PK)
├── user_id (FK vers users)
├── exercise_id (FK vers exercises)
├── content
└── created_at
```

---

## 🔒 Sécurité

- ✅ Mots de passe hashés avec `password_hash()` (bcrypt)
- ✅ Requêtes préparées (PDO) contre les injections SQL
- ✅ Tokens Bearer générés avec `random_bytes(32)`
- ✅ Validation des entrées côté serveur
- ✅ Messages d'erreur génériques

---

## 🧪 Tester l'API

### Avec le navigateur

```text
http://localhost/fitapp-backend/api/exercises.php
http://localhost/fitapp-backend/api/programs.php
```

### Avec Postman / Thunder Client

#### Inscription

- **Method** : `POST`
- **URL** : `http://localhost/fitapp-backend/api/register.php`
- **Body (JSON)** :

```json
{
  "prenom": "Test",
  "email": "test@example.com",
  "motDePasse": "123456"
}
```

#### Connexion

- **Method** : `POST`
- **URL** : `http://localhost/fitapp-backend/api/login.php`
- **Body (JSON)** :

```json
{
  "email": "test@example.com",
  "motDePasse": "123456"
}
```

---

## 🚑 Dépannage

| Problème | Solution |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | Vérifie que la base `fitapp` est créée (étape 3) |
| Token invalide ou expiré | Reconnecte-toi pour obtenir un nouveau token |
| Impossible de joindre le serveur | Vérifie que XAMPP (Apache + MySQL) est démarré et que l'IP est correcte |
| Réponse invalide du serveur | Erreur PHP — regarde le fichier de log d'Apache |

---

## 👤 Auteur

**Malak Khatir**

- GitHub : [@malakkhatir](https://github.com/malakkhatir)
- Repository : [fitapp-backend](https://github.com/malakkhatir/fitapp-backend)

---

## 📄 Licence

Ce projet est sous licence **MIT**. Voir le fichier `LICENSE` pour plus de détails.
