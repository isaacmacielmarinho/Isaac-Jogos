<?php
require __DIR__ . '/../src/config.php';
try {
    $pdo = db();
    echo "✅ Conexão OK!<br>";
    $total = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    echo "Produtos cadastrados no banco: $total";
} catch (Throwable $e) {
    echo "❌ Erro: " . $e->getMessage();
}