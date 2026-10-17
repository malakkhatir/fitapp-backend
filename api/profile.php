<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body       = readJsonBody();
    $prenom     = trim($body['prenom'] ?? $user['prenom']);
    $objectif   = (int)($body['objectifHebdo'] ?? $user['objectif_hebdo']);
    $poids      = (float)($body['poids'] ?? $user['poids']);
    $taille     = (int)($body['taille'] ?? $user['taille']);
    $age        = (int)($body['age'] ?? $user['age']);
    $sexe       = $body['sexe'] ?? $user['sexe'];

    if ($prenom === '') jsonError('Prénom obligatoire');
    if ($objectif < 1) $objectif = 3;
    if ($poids < 20) $poids = 70;
    if ($taille < 50) $taille = 170;
    if ($age < 10) $age = 25;

    $stmt = $pdo->prepare('UPDATE users SET prenom = ?, objectif_hebdo = ?, poids = ?, taille = ?, age = ?, sexe = ? WHERE id = ?');
    $stmt->execute([$prenom, $objectif, $poids, $taille, $age, $sexe, $user['id']]);

    // Jbed l'photo_url li kayna bach nrj3oha
    $stmtPhoto = $pdo->prepare('SELECT photo_url FROM users WHERE id = ?');
    $stmtPhoto->execute([$user['id']]);
    $photoUrl = $stmtPhoto->fetchColumn();

    jsonResponse([
        'id'            => (int)$user['id'],
        'prenom'        => $prenom,
        'email'         => $user['email'],
        'objectifHebdo' => $objectif,
        'photoUrl'      => $photoUrl ? 'http://' . $_SERVER['HTTP_HOST'] . '/fitapp-backend/' . $photoUrl : null,
        'poids'         => $poids,
        'taille'        => $taille,
        'age'           => $age,
        'sexe'          => $sexe,
    ]);
}

jsonError('Méthode non autorisée', 405);