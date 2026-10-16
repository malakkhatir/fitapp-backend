<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();
$niveau = $_GET['niveau'] ?? null;
$public = $_GET['public'] ?? null;
$objectif = $_GET['objectif'] ?? null;

$sql = 'SELECT id, titre, niveau, public, objectif, duree, description, icone, image_url FROM programs WHERE 1=1';
$params = [];

if ($niveau) { $sql .= ' AND niveau = ?'; $params[] = $niveau; }
if ($public) { $sql .= ' AND public = ?'; $params[] = $public; }
if ($objectif) { $sql .= ' AND objectif = ?'; $params[] = $objectif; }
$sql .= ' ORDER BY id';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$programs = $stmt->fetchAll();

$stmtEx = $pdo->prepare('SELECT exercise_id FROM program_exercises WHERE program_id = ? ORDER BY ordre');

foreach ($programs as &$p) {
    $stmtEx->execute([$p['id']]);
    $ids = array_map(fn($row) => (string)$row['exercise_id'], $stmtEx->fetchAll());
    $p['id'] = (int)$p['id'];
    $p['duree'] = (int)$p['duree'];
    $p['exercicesIds'] = $ids;
    $p['imageUrl'] = $p['image_url'];
    unset($p['image_url']);
}

jsonResponse($programs);