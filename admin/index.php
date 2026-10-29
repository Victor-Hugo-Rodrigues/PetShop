<?php
session_start();

if (!isset($_SESSION['administrador_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../conexao.php';

$nome = $_SESSION['administrador_nome'] ?? 'Administrador';

function escapar($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$totalProdutos = 0;
$totalServicos = 0;
$totalItens = 0;
$valorEstoque = 0.0;

$sqlResumo = "
    SELECT
        COUNT(*) AS total_itens,
        SUM(CASE WHEN tipo = 'produto' THEN 1 ELSE 0 END) AS total_produtos,
        SUM(CASE WHEN tipo = 'servico' THEN 1 ELSE 0 END) AS total_servicos,
        COALESCE(SUM(CASE
            WHEN tipo = 'produto' THEN valor * COALESCE(estoque, 0)
            ELSE 0
        END), 0) AS valor_estoque
    FROM itens
    WHERE ativo = 1
";

$resultadoResumo = mysqli_query($conexao, $sqlResumo);

if ($resultadoResumo) {
    $resumo = mysqli_fetch_assoc($resultadoResumo);

    $totalItens = (int) $resumo['total_itens'];
    $totalProdutos = (int) $resumo['total_produtos'];
    $totalServicos = (int) $resumo['total_servicos'];
    $valorEstoque = (float) $resumo['valor_estoque'];
} else {
    error_log(mysqli_error($conexao));
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Painel Administrativo | Pet Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">
                <i class="bi bi-paw-fill"></i> Pet Shop
            </a>

            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#menuAdmin"
                aria-controls="menuAdmin" aria-expanded="false"
                aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuAdmin">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Painel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produtos.php">Produtos e serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cadastrar.php">Cadastrar item</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <span class="text-white">
                        <i class="bi bi-person-circle"></i>
                        <?= escapar($nome) ?>
                    </span>

                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-lg-5">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">
                    Olá, <?= escapar($nome) ?>!
                </h1>
                <p class="text-secondary mb-0">
                    Bem-vindo ao painel de gerenciamento da sua loja.
                </p>
            </div>

            <a href="cadastrar.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Novo cadastro
            </a>
        </div>

        <div class="row g-3 mb-4">

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Itens ativos</p>
                                <h2 class="fw-bold mb-0"><?= $totalItens ?></h2>
                            </div>
                            <div class="bg-success-subtle text-success rounded-3 p-3">
                                <i class="bi bi-box-seam fs-3"></i>
                            </div>
                        </div>
                        <p class="small text-secondary mt-3 mb-0">
                            Produtos e serviços
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Produtos ativos</p>
                                <h2 class="fw-bold mb-0"><?= $totalProdutos ?></h2>
                            </div>
                            <div class="bg-primary-subtle text-primary rounded-3 p-3">
                                <i class="bi bi-basket2 fs-3"></i>
                            </div>
                        </div>
                        <p class="small text-secondary mt-3 mb-0">
                            Itens para venda
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Serviços ativos</p>
                                <h2 class="fw-bold mb-0"><?= $totalServicos ?></h2>
                            </div>
                            <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-3">
                                <i class="bi bi-scissors fs-3"></i>
                            </div>
                        </div>
                        <p class="small text-secondary mt-3 mb-0">
                            Serviços oferecidos
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-secondary mb-1">Valor estimado do estoque</p>
                                <h2 class="h4 fw-bold mb-0">
                                    R$ <?= number_format($valorEstoque, 2, ',', '.') ?>
                                </h2>
                            </div>
                            <div class="bg-info-subtle text-info-emphasis rounded-3 p-3">
                                <i class="bi bi-currency-dollar fs-3"></i>
                            </div>
                        </div>
                        <p class="small text-secondary mt-3 mb-0">
                            Preço × quantidade cadastrada
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">

            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">
                            <i class="bi bi-grid text-success"></i>
                            Gerenciamento da loja
                        </h2>

                        <p class="text-secondary">
                            Acesse as funções principais do sistema.
                        </p>

                        <div class="d-grid gap-3">

                            <a href="produtos.php"
                                class="btn btn-outline-success text-start p-3">
                                <i class="bi bi-box-seam fs-5 me-2"></i>
                                <strong>Produtos e serviços</strong>
                                <span class="d-block small ms-5">
                                    Consultar, editar e excluir itens.
                                </span>
                            </a>

                            <a href="cadastrar.php"
                                class="btn btn-outline-success text-start p-3">
                                <i class="bi bi-plus-circle fs-5 me-2"></i>
                                <strong>Cadastrar novo item</strong>
                                <span class="d-block small ms-5">
                                    Adicionar produtos e serviços à loja.
                                </span>
                            </a>

                            <a href="../index.php"
                                class="btn btn-outline-secondary text-start p-3">
                                <i class="bi bi-globe fs-5 me-2"></i>
                                <strong>Visualizar site</strong>
                                <span class="d-block small ms-5">
                                    Abrir a página pública do Pet Shop.
                                </span>
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">
                            <i class="bi bi-shield-check text-success"></i>
                            Informações do painel
                        </h2>

                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            Você está na área administrativa.
                        </div>

                        <p class="mb-2">
                            <strong>Administrador:</strong>
                            <?= escapar($nome) ?>
                        </p>

                        <p class="mb-2">
                            <strong>Banco de dados:</strong> conectado
                        </p>

                        <p class="text-secondary small mb-0">
                            Os indicadores são calculados a partir dos itens
                            ativos registrados no banco de dados.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <footer class="bg-white border-top py-3 mt-4">
        <div class="container text-center">
            <small class="text-secondary">
                &copy; <?= date('Y') ?> Pet Shop — Painel Administrativo
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>