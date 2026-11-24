<?php
require __DIR__ . '/../../src/auth.php';
require_admin();
$pdo = db();

$id = $_GET['id'] ?? null;
$produto = ['nome' => '', 'descricao' => '', 'preco' => '', 'estoque' => '', 'categoria_id' => '1', 'imagem' => ''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    if (!$produto) die("Produto não encontrado.");
}

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $categoria_id = $_POST['categoria_id'];
    $imagem = $_POST['imagem'] ?: 'https://via.placeholder.com/300x200';

    if ($id) {
        $stmt = $pdo->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, estoque=?, categoria_id=?, imagem=? WHERE id=?");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id, $imagem, $id]);
        $msg = "Produto atualizado!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id, imagem) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $descricao, $preco, $estoque, $categoria_id, $imagem]);
        $msg = "Produto cadastrado!";
    }
    header("Location: produtos.php?msg=" . urlencode($msg));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Editar' : 'Novo' ?> Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="card shadow mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h3 class="mb-4"><?= $id ? 'Editar' : 'Novo' ?> Produto</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Preço (R$)</label>
                            <input type="number" step="0.01" name="preco" class="form-control" value="<?= $produto['preco'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Estoque</label>
                            <input type="number" name="estoque" class="form-control" value="<?= $produto['estoque'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Categoria</label>
                        <select name="categoria_id" class="form-select" required>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $produto['categoria_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>URL da Imagem (opcional)</label>
                        <input type="text" name="imagem" class="form-control" value="<?= htmlspecialchars($produto['imagem']) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Salvar</button>
                    <a href="produtos.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>