<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

// GET : liste des favoris
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT e.id, e.nom, e.categorie, e.zone, e.niveau, e.duree, e.repetitions,
                e.description, e.instructions, e.icone, e.image_url
         FROM favorites f
         JOIN exercises e ON e.id = f.exercise_id
         WHERE f.user_id = ?
         ORDER BY f.created_at DESC'
    );
    $stmt->execute([$user['id']]);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$r) {
        $r['id']    = (int)$r['id'];
        $r['duree'] = (int)$r['duree'];
        $r['instructions'] = $r['instructions'] ? explode('|', $r['instructions']) : [];
        $r['imageUrl'] = $r['image_url'];
        unset($r['image_url']);
    }

    jsonResponse($rows);
}

// POST : ajouter un favori
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = readJsonBody();
    $exerciseId = (int)($body['exerciseId'] ?? 0);

    if ($exerciseId <= 0) jsonError('exerciseId manquant');

    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO favorites (user_id, exercise_id) VALUES (?, ?)'
    );
    $stmt->execute([$user['id'], $exerciseId]);

    jsonResponse(['success' => true, 'exerciseId' => $exerciseId]);
}

// DELETE : retirer un favori
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $exerciseId = (int)($_GET['exerciseId'] ?? 0);
    if ($exerciseId <= 0) jsonError('exerciseId manquant');

    $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND exercise_id = ?');
    $stmt->execute([$user['id'], $exerciseId]);

    jsonResponse(['success' => true]);
}

jsonError('Méthode non autorisée', 405);

