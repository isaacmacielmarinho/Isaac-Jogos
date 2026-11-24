<?php
require __DIR__ . '/../../src/auth.php';
require_admin();
$pdo = db();

$id = $_GET['id'] ?? null;
if ($id) {
    // Na prática, o ideal é apenas desativar (soft delete), mas pra faculdade o DELETE tá valendo.
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: produtos.php?msg=Produto excluído!");
exit;