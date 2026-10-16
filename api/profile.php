<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body       = readJsonBody();
    $prenom     = trim($body['prenom'] ?? $user['prenom']);
    $objectif   = (int)($body['objectifHebdo'] ?? $user['objectif_hebdo']);

if ($prenom === '') jsonError('Prénom obligatoire', 400);
    if ($objectif < 1) $objectif = 3;

    $stmt = $pdo->prepare('UPDATE users SET prenom = ?, objectif_hebdo = ? WHERE id = ?');
    $stmt->execute([$prenom, $objectif, $user['id']]);

    jsonResponse([
        'id'            => (int)$user['id'],
        'prenom'        => $prenom,
        'email'         => $user['email'],
        'objectifHebdo' => $objectif,
    ]);
}

jsonError('Méthode non autorisée', 405);