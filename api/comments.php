<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

// GET : liste des commentaires dial exercice
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $exerciseId = (int)($_GET['exerciseId'] ?? 0);
    if ($exerciseId <= 0) jsonError('exerciseId manquant');

    $stmt = $pdo->prepare(
        'SELECT c.id, c.content, c.created_at, c.user_id, u.prenom
         FROM comments c
         JOIN users u ON u.id = c.user_id
         WHERE c.exercise_id = ?
         ORDER BY c.created_at DESC'
    );
    $stmt->execute([$exerciseId]);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$r) {
        $r['id'] = (int)$r['id'];
        $r['isMine'] = ((int)$r['user_id'] === (int)$user['id']);
        unset($r['user_id']);
    }

    jsonResponse($rows);
}

// POST : ajouter un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = readJsonBody();
    $exerciseId = (int)($body['exerciseId'] ?? 0);
    $content = trim($body['content'] ?? '');

    if ($exerciseId <= 0) jsonError('exerciseId manquant');
    if ($content === '') jsonError('Commentaire vide');
    if (strlen($content) > 1000) jsonError('Commentaire trop long (max 1000 caractères)');

    $stmt = $pdo->prepare(
        'INSERT INTO comments (user_id, exercise_id, content) VALUES (?, ?, ?)'
    );
    $stmt->execute([$user['id'], $exerciseId, $content]);

    $id = (int)$pdo->lastInsertId();

    jsonResponse([
        'id' => $id,
        'content' => $content,
        'prenom' => $user['prenom'],
        'created_at' => date('c'),
        'isMine' => true,
    ], 201);
}

// DELETE : supprimer un commentaire (ghir dialek)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) jsonError('id manquant');

    $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);

    if ($stmt->rowCount() === 0) {
        jsonError('Commentaire introuvable ou non autorisé', 404);
    }

    jsonResponse(['success' => true]);
}

jsonError('Méthode non autorisée', 405);