<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo  = getPDO();

// ---------- GET : liste des séances ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT id, programme_id, nom_programme, date, minutes FROM sessions WHERE user_id = ? ORDER BY date DESC');
    $stmt->execute([$user['id']]);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$r) {
        $r['id']           = (int)$r['id'];
        $r['programmeId']  = $r['programme_id'];
        $r['nomProgramme'] = $r['nom_programme'];
        $r['minutes']      = (int)$r['minutes'];
        unset($r['programme_id'], $r['nom_programme']);
    }

    jsonResponse($rows);
}

// ---------- POST : ajouter une séance ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body          = readJsonBody();
    $programmeId   = $body['programmeId'] ?? null;
    $nomProgramme  = trim($body['nomProgramme'] ?? '');
    $minutes       = (int)($body['minutes'] ?? 0);

    if ($nomProgramme === '' || $minutes <= 0) jsonError('Données invalides');

    $stmt = $pdo->prepare('INSERT INTO sessions (user_id, programme_id, nom_programme, minutes) VALUES (?, ?, ?, ?)');
    $stmt->execute([$user['id'], $programmeId, $nomProgramme, $minutes]);

    $id = (int)$pdo->lastInsertId();

    jsonResponse([
        'id'           => $id,
        'programmeId'  => $programmeId,
        'nomProgramme' => $nomProgramme,
        'minutes'      => $minutes,
        'date'         => date('c'),
    ], 201);
}

// ---------- DELETE : supprimer une séance ----------
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) jsonError('ID manquant');

    $stmt = $pdo->prepare('DELETE FROM sessions WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);

    jsonResponse(['success' => true]);
}

jsonError('Méthode non autorisée', 405);