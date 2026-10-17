<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Méthode non autorisée', 405);

$body      = readJsonBody();
$prenom    = trim($body['prenom'] ?? '');
$email     = strtolower(trim($body['email'] ?? ''));
$motDePasse = $body['motDePasse'] ?? '';

// Validations
if ($prenom === '' || $email === '' || $motDePasse === '') {
    jsonError('Champs manquants');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Email invalide');
}
if (strlen($motDePasse) < 6) {
    jsonError('Mot de passe trop court (minimum 6 caractères)');
}

$pdo = getPDO();

// Déjà utilisé ?
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonError('Cet email est déjà utilisé', 409);
}

// Insertion
$hash  = password_hash($motDePasse, PASSWORD_DEFAULT); // bcrypt
$token = generateToken();

$stmt = $pdo->prepare('INSERT INTO users (prenom, email, mot_de_passe, token) VALUES (?, ?, ?, ?)');
$stmt->execute([$prenom, $email, $hash, $token]);

jsonResponse([
    'token' => $token,
    'user'  => [
        'id'            => (int)$pdo->lastInsertId(),
        'prenom'        => $prenom,
        'email'         => $email,
        'objectifHebdo' => 3,
        'photoUrl'      => null,
        'poids'         => 70.0,
        'taille'        => 170,
        'age'           => 25,
        'sexe'          => 'Femme',
    ],
], 201);