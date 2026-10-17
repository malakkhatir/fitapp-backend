<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Méthode non autorisée', 405);

$body = readJsonBody();
$email = strtolower(trim($body['email'] ?? ''));
$motDePasse = $body['motDePasse'] ?? '';

if ($email === '' || $motDePasse === '') jsonError('Champs manquants');

$pdo = getPDO();
$stmt = $pdo->prepare('SELECT id, prenom, email, mot_de_passe, objectif_hebdo, photo_url, poids, target_weight, taille, age, sexe FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($motDePasse, $user['mot_de_passe'])) {
    jsonError('Email ou mot de passe incorrect', 401);
}

$token = generateToken();
$stmt = $pdo->prepare('UPDATE users SET token = ? WHERE id = ?');
$stmt->execute([$token, $user['id']]);

jsonResponse([
    'token' => $token,
    'user'  => [
        'id'            => (int)$user['id'],
        'prenom'        => $user['prenom'],
        'email'         => $user['email'],
        'objectifHebdo' => (int)$user['objectif_hebdo'],
        'photoUrl'      => $user['photo_url'] ? 'http://' . $_SERVER['HTTP_HOST'] . '/fitapp-backend/' . $user['photo_url'] : null,
        'poids'         => (float)$user['poids'],
        'targetWeight'  => $user['target_weight'] ? (float)$user['target_weight'] : null,
        'taille'        => (int)$user['taille'],
        'age'           => (int)$user['age'],
        'sexe'          => $user['sexe'],
    ],
]);