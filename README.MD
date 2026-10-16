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
- 📋 Les **programmes sportifs** (20 programmes : débutant, intermédiaire, avancé)
- 📊 L'**historique des séances** et la **progression** dans les programmes

L'application mobile (React Native + Expo) consomme cette API via des requêtes HTTP avec authentification par **token Bearer**.

🔗 **Frontend** : [github.com/malakkhatir/fit-app](https://github.com/malakkhatir/fit-app)

---

## ✨ Fonctionnalités

| Fonctionnalité | Description |
|---|---|
| 🔐 **Authentification** | Inscription, connexion, déconnexion avec hash bcrypt |
| 👤 **Profil utilisateur** | Modification du prénom et de l'objectif hebdomadaire |
| 🏋️ **Exercices** | 60 exercices avec filtres (zone, niveau, catégorie) |
| 📋 **Programmes** | 20 programmes (Filles, Garçons, Mobilité, Force, Masse...) |
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
│   ├── sessions.php              # GET/POST/DELETE — Historique séances
│   └── program-progress.php      # GET/POST/DELETE — Progression programmes
├── sql/
│   └── fitapp.sql                # Script de création de la base + données
├── .gitignore
└── README.md
```

---

## 🚀 Installation

### Prérequis

- [XAMPP](https://www.apachefriends.org/fr/index.html) (Apache + MySQL)
- Navigateur web (pour phpMyAdmin)

### Étape 1 — Cloner le dépôt

```bash
git clone https://github.com/malakkhatir/fitapp-backend.git
```

Place ensuite le dossier dans `C:\xampp\htdocs\` :

```text
C:\xampp\htdocs\fitapp-backend\
```

### Étape 2 — Démarrer XAMPP

Ouvre le **XAMPP Control Panel** et démarre :

- ✅ **Apache**
- ✅ **MySQL**

### Étape 3 — Créer la base de données

1. Ouvre **phpMyAdmin** : [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
2. Clique sur l'onglet **Importer**
3. Sélectionne le fichier `sql/fitapp.sql`
4. Clique sur **Exécuter**

La base `fitapp` est créée avec :

- 6 tables : `users`, `exercises`, `programs`, `program_exercises`, `sessions`, `program_progress`
- **60 exercices** répartis par zone (Abdos, Fessiers, Cuisses, Hanches...)
- **20 programmes** (Débutant, Intermédiaire, Avancé)

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
http://<TON-IP-LOCALE>/fitapp-backend/api
```

Exemple : `http://192.168.1.42/fitapp-backend/api`

### Authentification

Les endpoints protégés nécessitent un header :

```text
Authorization: Bearer <TON_TOKEN>
```

Le token est retourné par `/register.php` et `/login.php`.

---

### 📌 Endpoints

#### 🔐 Authentification

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/register.php` | ❌ | Créer un compte |
| `POST` | `/login.php` | ❌ | Se connecter |
| `POST` | `/logout.php` | ✅ | Se déconnecter |

**Exemple — `POST /register.php`**

Requête :

```json
{
  "prenom": "Malak",
  "email": "malak@example.com",
  "motDePasse": "123456"
}
```

Réponse (201) :

```json
{
  "token": "a3f9c2e...",
  "user": {
    "id": 1,
    "prenom": "Malak",
    "email": "malak@example.com",
    "objectifHebdo": 3
  }
}
```

---

#### 👤 Profil

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| `PUT` | `/profile.php` | ✅ | Modifier prénom + objectif |

**Exemple — `PUT /profile.php`**

Requête :

```json
{
  "prenom": "Malak",
  "objectifHebdo": 5
}
```

---

#### 🏋️ Exercices

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/exercises.php` | ❌ | Liste tous les exercices |
| `GET` | `/exercises.php?niveau=Débutant` | ❌ | Filtrer par niveau |
| `GET` | `/exercises.php?categorie=Cardio` | ❌ | Filtrer par catégorie |
| `GET` | `/exercises.php?zone=Abdos` | ❌ | Filtrer par zone |

---

#### 📋 Programmes

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/programs.php` | ❌ | Liste tous les programmes |
| `GET` | `/programs.php?niveau=Avancé` | ❌ | Filtrer par niveau |
| `GET` | `/programs.php?public=Filles` | ❌ | Filtrer par public |
| `GET` | `/programs.php?objectif=Fessiers` | ❌ | Filtrer par objectif |

---

#### 📊 Séances (Historique)

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/sessions.php` | ✅ | Historique de l'utilisateur |
| `POST` | `/sessions.php` | ✅ | Ajouter une séance terminée |
| `DELETE` | `/sessions.php?id=X` | ✅ | Supprimer une séance |

**Exemple — `POST /sessions.php`**

Requête :

```json
{
  "programmeId": 1,
  "nomProgramme": "Découverte",
  "minutes": 20
}
```

---

#### 🎯 Progression programmes

| Méthode | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/program-progress.php?programId=X` | ✅ | Sessions terminées |
| `POST` | `/program-progress.php` | ✅ | Marquer une session terminée |
| `DELETE` | `/program-progress.php?programId=X` | ✅ | Réinitialiser la progression |

**Exemple — `POST /program-progress.php`**

Requête :

```json
{
  "programId": 1,
  "sessionIndex": 0
}
```

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
└── created_at

exercises
├── id (PK)
├── nom
├── categorie
├── zone
├── niveau
├── duree
├── repetitions
├── description
├── instructions
├── icone
└── image_url

programs
├── id (PK)
├── titre
├── niveau
├── public
├── objectif
├── duree
├── description
├── icone
└── image_url

program_exercises
├── program_id (FK → programs)
├── exercise_id (FK → exercises)
└── ordre

sessions
├── id (PK)
├── user_id (FK → users)
├── programme_id (FK → programs)
├── nom_programme
├── date
└── minutes

program_progress
├── id (PK)
├── user_id (FK → users)
├── program_id (FK → programs)
├── session_index
└── completed_at
```

---

## 🔒 Sécurité

- ✅ **Mots de passe hashés** avec `password_hash()` (bcrypt)
- ✅ **Requêtes préparées** (PDO) contre les injections SQL
- ✅ **Tokens Bearer** générés avec `random_bytes(32)`
- ✅ **Validation des entrées** côté serveur
- ✅ **Messages d'erreur génériques** (pas de divulgation d'info)

> ⚠️ **Pour la production** : utiliser HTTPS, ne pas committer `config/database.local.php`, limiter les origines CORS.

---

## 🧪 Tester l'API

### Avec le navigateur

```text
http://localhost/fitapp-backend/api/exercises.php
http://localhost/fitapp-backend/api/programs.php
```

### Avec Postman / Thunder Client

**Inscription** :

- Method : `POST`
- URL : `http://localhost/fitapp-backend/api/register.php`
- Body (JSON) :

```json
{
  "prenom": "Test",
  "email": "test@example.com",
  "motDePasse": "123456"
}
```

**Connexion** :

- Method : `POST`
- URL : `http://localhost/fitapp-backend/api/login.php`
- Body (JSON) :

```json
{
  "email": "test@example.com",
  "motDePasse": "123456"
}
```

**Récupérer les séances** (avec token) :

- Method : `GET`
- URL : `http://localhost/fitapp-backend/api/sessions.php`
- Header : `Authorization: Bearer <TON_TOKEN>`

---

## 🚑 Dépannage

| Problème | Solution |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | Vérifie que la base `fitapp` est créée (étape 3) |
| `Token invalide ou expiré` | Reconnecte-toi pour obtenir un nouveau token |
| `Impossible de joindre le serveur` | Vérifie que XAMPP (Apache + MySQL) est démarré et que l'IP est correcte |
| `Réponse invalide du serveur` | Erreur PHP — regarde `C:\xampp\apache\logs\error.log` |
| Page blanche | Active `display_errors` dans `php.ini` pour voir l'erreur |

---

## 🤝 Contribuer

Les contributions sont les bienvenues ! Pour proposer une amélioration :

1. Fork le projet
2. Crée une branche (`git checkout -b feature/ma-fonctionnalite`)
3. Commit (`git commit -m 'Ajout de ma fonctionnalité'`)
4. Push (`git push origin feature/ma-fonctionnalite`)
5. Ouvre une **Pull Request**

---

## 📄 Licence

Ce projet est sous licence **MIT** — voir le fichier `LICENSE` pour plus de détails.

---

## 👤 Auteur

**Malak Khatir**

- GitHub : [@malakkhatir](https://github.com/malakkhatir)
- Repository : [fitapp-backend](https://github.com/malakkhatir/fitapp-backend)

---

## 🙏 Remerciements

- [XAMPP](https://www.apachefriends.org/) pour l'environnement de développement
- [PHP](https://www.php.net/) et [MySQL](https://www.mysql.com/) pour le backend
- [Unsplash](https://unsplash.com/) pour les images des exercices
- La communauté React Native / Expo pour le frontend

---

