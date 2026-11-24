<?php
require __DIR__ . '/../../src/auth.php';
require_admin();
$pdo = db();
$produtos = $pdo->query("SELECT p.*, c.nome as categoria_nome FROM produtos p JOIN categorias c ON p.categoria_id = c.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📦 Produtos</h2>
            <a href="produto_form.php" class="btn btn-primary">+ Novo Produto</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['nome']) ?></td>
                                <td><?= htmlspecialchars($p['categoria_nome']) ?></td>
                                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                                <td><?= $p['estoque'] ?></td>
                                <td>
                                    <a href="produto_form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="produto_excluir.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="index.php" class="btn btn-secondary">Voltar ao Painel</a>
            </div>
        </div>
    </div>
</body>
</html>