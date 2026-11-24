<?php
require __DIR__ . '/../src/auth.php';
require_login(); // Bloqueia quem não tá logado
$pdo = db();

$itens = $_SESSION['carrinho'] ?? [];
if (empty($itens)) {
    header('Location: carrinho.php');
    exit;
}

// Processa a compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagamento = $_POST['pagamento'] ?? 'pix';
    
    try {
        $pdo->beginTransaction();

        // 1. Calcula total real (Corrigido!)
        $ids = array_keys($itens);
        $total = 0;
        
        foreach ($ids as $id) {
            // Pega preço e estoque travando a linha para evitar conflito (FOR UPDATE)
            $stmt_check = $pdo->prepare("SELECT preco, estoque FROM produtos WHERE id = ? FOR UPDATE");
            $stmt_check->execute([$id]);
            $p = $stmt_check->fetch();
            
            if ($p['estoque'] < $itens[$id]) {
                throw new Exception("Estoque insuficiente para um dos produtos.");
            }
            $total += $p['preco'] * $itens[$id];
        }

        // 2. Cria o pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, forma_pagamento, total) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $pagamento, $total]);
        $pedido_id = $pdo->lastInsertId();

        // 3. Insere itens e baixa estoque
        foreach ($ids as $id) {
            $stmt_p = $pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
            $stmt_p->execute([$id]);
            $preco_unitario = $stmt_p->fetchColumn();
            $qtd = $itens[$id];

            // Insere item congelando o preço unitário
            $stmt_item = $pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
            $stmt_item->execute([$pedido_id, $id, $qtd, $preco_unitario]);

            // Baixa estoque
            $stmt_estoque = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
            $stmt_estoque->execute([$qtd, $id]);
        }

        $pdo->commit();
        
        // Limpa carrinho
        $_SESSION['carrinho'] = [];
        
        // Redireciona com sucesso
        header("Location: sucesso.php?pedido=$pedido_id");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $erro = "Erro ao processar compra: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="mb-4">Finalizar Compra 💳</h3>
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <p class="fw-bold">Escolha a forma de pagamento:</p>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="pagamento" value="pix" id="pix" checked>
                                <label class="form-check-label" for="pix">PIX</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="pagamento" value="boleto" id="boleto">
                                <label class="form-check-label" for="boleto">Boleto</label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="radio" name="pagamento" value="cartao" id="cartao">
                                <label class="form-check-label" for="cartao">Cartão de Crédito</label>
                            </div>

                            <button type="submit" class="btn btn-success w-100 btn-lg">Confirmar Pagamento</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="carrinho.php">Voltar ao carrinho</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>