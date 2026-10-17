<?php
require_once __DIR__ . '/../config/database.php';

$pdo = getPDO();
$niveau = $_GET['niveau'] ?? null;
$categorie = $_GET['categorie'] ?? null;
$zone = $_GET['zone'] ?? null;

$sql = 'SELECT id, nom, categorie, zone, niveau, duree, repetitions, description, instructions, icone, image_url FROM exercises WHERE 1=1';
$params = [];

if ($niveau) { $sql .= ' AND niveau = ?'; $params[] = $niveau; }
if ($categorie) { $sql .= ' AND categorie = ?'; $params[] = $categorie; }
if ($zone) { $sql .= ' AND zone = ?'; $params[] = $zone; }
$sql .= ' ORDER BY id';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

foreach ($rows as &$r) {
    $r['id'] = (int)$r['id'];
    $r['duree'] = (int)$r['duree'];
    $r['instructions'] = $r['instructions'] ? explode('|', $r['instructions']) : [];
    $r['imageUrl'] = $r['image_url'];
    unset($r['image_url']);
}

jsonResponse($rows);

