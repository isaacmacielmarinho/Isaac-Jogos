<?php
require __DIR__ . '/../../src/auth.php';
require_admin();

$pdo = db();
$total_produtos = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$total_pedidos = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$faturamento = $pdo->query("SELECT SUM(total) FROM pedidos")->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">⚙️ Painel Admin</a>
            <a href="../index.php" class="btn btn-outline-light btn-sm">Ver Loja</a>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white text-center p-3 shadow">
                    <h3><?= $total_produtos ?></h3>
                    <p class="mb-0">Produtos Cadastrados</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white text-center p-3 shadow">
                    <h3><?= $total_pedidos ?></h3>
                    <p class="mb-0">Pedidos Realizados</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white text-center p-3 shadow">
                    <h3>R$ <?= number_format($faturamento, 2, ',', '.') ?></h3>
                    <p class="mb-0">Faturamento Total</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="list-group shadow">
                    <a href="produtos.php" class="list-group-item list-group-item-action">📦 Gerenciar Produtos</a>
                    <a href="pedidos.php" class="list-group-item list-group-item-action">🧾 Visualizar Pedidos</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>