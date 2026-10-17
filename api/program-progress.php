<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

// GET : progression f programme
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $programId = (int)($_GET['programId'] ?? 0);
    if ($programId <= 0) jsonError('programId manquant');

    $stmt = $pdo->prepare(
        'SELECT session_index FROM program_progress 
         WHERE user_id = ? AND program_id = ? 
         ORDER BY session_index'
    );
    $stmt->execute([$user['id'], $programId]);
    $rows = $stmt->fetchAll();

    $indices = array_map(fn($r) => (int)$r['session_index'], $rows);
    jsonResponse(['completedSessions' => $indices]);
}

// POST : marquer session comme terminée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = readJsonBody();
    $programId    = (int)($body['programId'] ?? 0);
    $sessionIndex = (int)($body['sessionIndex'] ?? -1);

    if ($programId <= 0 || $sessionIndex < 0) jsonError('Données invalides');

    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO program_progress (user_id, program_id, session_index) 
         VALUES (?, ?, ?)'
    );
    $stmt->execute([$user['id'], $programId, $sessionIndex]);

    jsonResponse(['success' => true, 'sessionIndex' => $sessionIndex]);
}

// DELETE : réinitialiser progression
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $programId = (int)($_GET['programId'] ?? 0);
    if ($programId <= 0) jsonError('programId manquant');

    $stmt = $pdo->prepare('DELETE FROM program_progress WHERE user_id = ? AND program_id = ?');
    $stmt->execute([$user['id'], $programId]);

    jsonResponse(['success' => true]);
}

jsonError('Méthode non autorisée', 405);

