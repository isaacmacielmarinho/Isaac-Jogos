<?php
require __DIR__ . '/../src/auth.php';
$pdo = db();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ? AND p.ativo = 1");
$stmt->execute([$id]);
$prod = $stmt->fetch();

if (!$prod) {
    die("Produto não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($prod['nome']) ?> - Ecommerce Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎮 Ecommerce Games</a>
            <a href="carrinho.php" class="btn btn-success btn-sm">🛒 Ver Carrinho</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <img src="https://via.placeholder.com/500x400?text=<?= urlencode($prod['nome']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($prod['nome']) ?>">
                    </div>
                    <div class="col-md-7">
                        <span class="badge bg-secondary mb-2"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                        <h2><?= htmlspecialchars($prod['nome']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($prod['descricao']) ?></p>
                        <h3 class="text-success mb-4">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></h3>
                        
                        <p><strong>Estoque disponível:</strong> <?= $prod['estoque'] ?> unidades</p>

                        <?php if ($prod['estoque'] > 0): ?>
                            <form action="carrinho.php" method="POST" class="d-flex gap-2 mt-4">
                                <input type="hidden" name="acao" value="adicionar">
                                <input type="hidden" name="produto_id" value="<?= $prod['id'] ?>">
                                <input type="number" name="quantidade" class="form-control w-25" value="1" min="1" max="<?= $prod['estoque'] ?>">
                                <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">Produto esgotado!</div>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn btn-outline-secondary mt-3">Voltar para a Vitrine</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>