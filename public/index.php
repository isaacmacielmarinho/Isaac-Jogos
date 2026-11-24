<?php
require __DIR__ . '/../src/auth.php';
$pdo = db();

// Filtros de busca
$busca = trim($_GET['busca'] ?? '');
$categoria_id = $_GET['categoria'] ?? '';

// Monta a query com Prepared Statements (Segurança!)
$sql = "SELECT p.*, c.nome as categoria_nome FROM produtos p 
        JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.ativo = 1";
$params = [];

if ($busca) {
    $sql .= " AND (p.nome LIKE ? OR p.descricao LIKE ?)";
    $params[] = "%$busca%";
    $params[] = "%$busca%";
}
if ($categoria_id) {
    $sql .= " AND p.categoria_id = ?";
    $params[] = $categoria_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();

$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ecommerce Games - Vitrine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎮 ISAAC JOGOS INSANOS 🎮</a>
            <div class="d-flex align-items-center gap-3">
                <?php if (is_logged_in()): ?>
                    <span class="text-white">Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                    
                    <!-- BOTÃO NOVO AQUI -->
                    <?php if (is_admin()): ?>
                        <a href="admin/index.php" class="btn btn-warning btn-sm fw-bold">⚙️ Painel Admin</a>
                    <?php endif; ?>
                    <!-- FIM DO BOTÃO NOVO -->

                    <a href="logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm">Entrar</a>
                    <a href="cadastro.php" class="btn btn-primary btn-sm">Cadastrar</a>
                <?php endif; ?>
                <a href="carrinho.php" class="btn btn-success btn-sm">🛒 Carrinho</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Barra de Busca e Filtros -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="busca" class="form-control" placeholder="Buscar jogo..." value="<?= htmlspecialchars($busca) ?>">
            </div>
            <div class="col-md-4">
                <select name="categoria" class="form-select">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoria_id == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Filtrar</button>
            </div>
        </form>

        <!-- Lista de Produtos -->
        <div class="row">
            <?php if (count($produtos) === 0): ?>
                <div class="col-12 text-center mt-5">
                    <h4>Nenhum produto encontrado. 😢</h4>
                </div>
            <?php endif; ?>

            <?php foreach ($produtos as $prod): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- Coloquei uma imagem genérica já que não temos as fotos reais ainda -->
                        <img src="<?= htmlspecialchars($prod['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($prod['nome']) ?>" style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-secondary mb-2 align-self-start"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                            <h5 class="card-title"><?= htmlspecialchars($prod['nome']) ?></h5>
                            <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($prod['descricao']) ?></p>
                            <h4 class="text-success">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></h4>
                            <a href="produto.php?id=<?= $prod['id'] ?>" class="btn btn-outline-dark mt-2">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>