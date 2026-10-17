<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Méthode non autorisée', 405);

$token = getTokenFromHeader();
if ($token) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('UPDATE users SET token = NULL WHERE token = ?');
    $stmt->execute([$token]);
}

jsonResponse(['success' => true]);
