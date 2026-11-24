<?php
require __DIR__ . '/../src/auth.php';
require_login();

$pedido_id = $_GET['pedido'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Compra Realizada!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="card shadow mx-auto" style="max-width: 500px;">
            <div class="card-body py-5">
                <h1 class="display-1">🎉</h1>
                <h2 class="text-success mb-3">Compra Realizada!</h2>
                <p class="lead">Seu pedido <strong>#<?= (int)$pedido_id ?></strong> foi confirmado com sucesso.</p>
                <p class="text-muted">Obrigado por comprar na Ecommerce Games!</p>
                <a href="index.php" class="btn btn-primary mt-4">Voltar para a Vitrine</a>
            </div>
        </div>
    </div>
</body>
</html>