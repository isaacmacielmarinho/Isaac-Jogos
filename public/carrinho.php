<?php
require __DIR__ . '/../src/auth.php';
$pdo = db();

// Inicializa o carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Lógica de Adicionar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'adicionar') {
    $produto_id = (int)$_POST['produto_id'];
    $quantidade = (int)$_POST['quantidade'];
    
    // Verifica estoque no banco
    $stmt = $pdo->prepare("SELECT estoque FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    $estoque = $stmt->fetchColumn();

    if ($estoque !== false && $quantidade > 0) {
        $qtd_atual = $_SESSION['carrinho'][$produto_id] ?? 0;
        // Não deixa passar do estoque
        if ($qtd_atual + $quantidade <= $estoque) {
            $_SESSION['carrinho'][$produto_id] = $qtd_atual + $quantidade;
        } else {
            $_SESSION['aviso'] = "Quantidade máxima em estoque atingida.";
        }
    }
    header('Location: carrinho.php');
    exit;
}

// Lógica de Remover
if (isset($_GET['remover'])) {
    $id_remover = (int)$_GET['remover'];
    unset($_SESSION['carrinho'][$id_remover]);
    header('Location: carrinho.php');
    exit;
}

// Lógica de Limpar Carrinho
if (isset($_GET['limpar'])) {
    $_SESSION['carrinho'] = [];
    header('Location: carrinho.php');
    exit;
}

// Busca os dados dos produtos no carrinho
$itens = [];
$total = 0;
if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll();

    foreach ($produtos as $p) {
        $qtd = $_SESSION['carrinho'][$p['id']];
        $subtotal = $p['preco'] * $qtd;
        $total += $subtotal;
        $p['quantidade'] = $qtd;
        $p['subtotal'] = $subtotal;
        $itens[] = $p;
    }
}

$aviso = $_SESSION['aviso'] ?? '';
unset($_SESSION['aviso']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎮 Ecommerce Games</a>
            <a href="index.php" class="btn btn-outline-light btn-sm">Continuar Comprando</a>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Seu Carrinho 🛒</h2>
        
        <?php if ($aviso): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($aviso) ?></div>
        <?php endif; ?>

        <?php if (empty($itens)): ?>
            <div class="alert alert-info">Seu carrinho está vazio.</div>
        <?php else: ?>
            <div class="card shadow">
                <div class="card-body">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Qtd</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['nome']) ?></td>
                                    <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                                    <td><?= $item['quantidade'] ?></td>
                                    <td>R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                                    <td class="text-end">
                                        <a href="carrinho.php?remover=<?= $item['id'] ?>" class="btn btn-sm btn-danger">Remover</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Geral:</td>
                                <td colspan="2" class="fw-bold text-success fs-5">R$ <?= number_format($total, 2, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="carrinho.php?limpar=1" class="btn btn-outline-danger">Limpar Carrinho</a>
                        <a href="checkout.php" class="btn btn-success btn-lg">Finalizar Compra</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>