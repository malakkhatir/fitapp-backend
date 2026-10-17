<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();
$token = getTokenFromHeader();
$userId = null;

if ($token) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE token = ?');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) $userId = $user['id'];
}

// Jbed les programmes standards (is_custom=0) + dial l'user (is_custom=1 AND user_id=?)
$sql = 'SELECT id, titre, niveau, public, objectif, duree, description, icone, image_url, is_custom 
        FROM programs 
        WHERE is_custom = 0';
$params = [];

if ($userId) {
    $sql .= ' OR (is_custom = 1 AND user_id = ?)';
    $params[] = $userId;
}

$sql .= ' ORDER BY is_custom DESC, id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$programs = $stmt->fetchAll();

$stmtEx = $pdo->prepare('SELECT exercise_id FROM program_exercises WHERE program_id = ? ORDER BY ordre');

foreach ($programs as &$p) {
    $stmtEx->execute([$p['id']]);
    $ids = array_map(fn($row) => (string)$row['exercise_id'], $stmtEx->fetchAll());
    
    $p['id']           = (int)$p['id'];
    $p['duree']        = (int)$p['duree'];
    $p['exercicesIds'] = $ids;
    $p['imageUrl']     = $p['image_url'] ?: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&q=80'; // Image par défaut
    $p['isCustom']     = (bool)$p['is_custom'];
    unset($p['image_url'], $p['is_custom']);
}

jsonResponse($programs);
