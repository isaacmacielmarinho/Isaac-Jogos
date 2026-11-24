<?php
require __DIR__ . '/../../src/auth.php';
require_admin();
$pdo = db();

$pedidos = $pdo->query("
    SELECT p.*, u.nome as cliente_nome 
    FROM pedidos p 
    JOIN usuarios u ON p.usuario_id = u.id 
    ORDER BY p.data_pedido DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="mb-4">🧾 Pedidos</h2>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Pagamento</th>
                            <th>Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $p): ?>
                            <tr>
                                <td>#<?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['cliente_nome']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></td>
                                <td><?= strtoupper($p['forma_pagamento']) ?></td>
                                <td class="text-success fw-bold">R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalPedido<?= $p['id'] ?>">Ver Itens</button>
                                </td>
                            </tr>
                            
                            <!-- Modal com os itens -->
                            <div class="modal fade" id="modalPedido<?= $p['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Itens do Pedido #<?= $p['id'] ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                <?php
                                                $stmt_itens = $pdo->prepare("SELECT i.*, pr.nome FROM pedido_itens i JOIN produtos pr ON i.produto_id = pr.id WHERE i.pedido_id = ?");
                                                $stmt_itens->execute([$p['id']]);
                                                $itens = $stmt_itens->fetchAll();
                                                foreach ($itens as $item):
                                                ?>
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span><?= $item['quantidade'] ?>x <?= htmlspecialchars($item['nome']) ?></span>
                                                        <span>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="index.php" class="btn btn-secondary mt-3">Voltar ao Painel</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS pro modal funcionar -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>