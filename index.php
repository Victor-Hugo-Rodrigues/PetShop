<?php
require_once __DIR__ . '/conexao.php';

function escapar($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$sqlProdutos = "SELECT id, nome, descricao, valor, imagem, preco_minimo, preco_maximo
                FROM itens
                WHERE tipo = 'produto' AND ativo = 1
                ORDER BY id DESC
                LIMIT 8";

$resultadoProdutos = $conexao->query($sqlProdutos);
$produtos = $resultadoProdutos ? $resultadoProdutos->fetch_all(MYSQLI_ASSOC) : [];

$sqlServicos = "SELECT id, nome, descricao, valor, imagem, preco_minimo, preco_maximo
                FROM itens
                WHERE tipo = 'servico' AND ativo = 1
                ORDER BY id DESC
                LIMIT 6";


$resultadoServicos = $conexao->query($sqlServicos);
$servicos = $resultadoServicos ? $resultadoServicos->fetch_all(MYSQLI_ASSOC) : [];

$whatsapp = '5534999999999';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PetShop: produtos, cuidados e serviços para seu melhor amigo.">
    <title>PetShop | Cuidado e carinho para seu pet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">
                <i class="bi bi-heart-fill me-2"></i>PetShop
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#produtos">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#servicos">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contato">Contato</a>
                    </li>
                    <li class="nav-item">
                        <a href="admin/login.php" class="btn btn-outline-light btn-sm mt-2 mt-lg-0">
                            <i class="bi bi-person-lock me-1"></i>Área administrativa
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header id="inicio" class="bg-success text-white py-5">
        <div class="container py-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge text-bg-light text-success mb-3">
                        <i class="bi bi-heart-fill me-1"></i>Amor em cada cuidado
                    </span>

                    <h1 class="display-4 fw-bold">
                        Tudo para o seu melhor amigo!
                    </h1>

                    <p class="lead my-4">
                        Produtos de qualidade e serviços especiais para deixar seu pet saudável, feliz e bem cuidado.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="#produtos" class="btn btn-light btn-lg text-success fw-semibold">
                            <i class="bi bi-bag-heart me-2"></i>Ver produtos
                        </a>

                        <a href="#servicos" class="btn btn-outline-light btn-lg">
                            Conhecer serviços
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 text-center">
                    <i class="bi bi-heart-pulse display-1"></i>
                    <div class="mt-3 fs-5 fw-semibold">
                        Seu pet merece o melhor!
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <div class="row text-center g-3">
                <div class="col-6 col-md-3">
                    <i class="bi bi-shield-check fs-2 text-success"></i>
                    <p class="fw-semibold mb-0 mt-2">Qualidade</p>
                </div>

                <div class="col-6 col-md-3">
                    <i class="bi bi-heart fs-2 text-success"></i>
                    <p class="fw-semibold mb-0 mt-2">Carinho</p>
                </div>

                <div class="col-6 col-md-3">
                    <i class="bi bi-bag-check fs-2 text-success"></i>
                    <p class="fw-semibold mb-0 mt-2">Variedade</p>
                </div>

                <div class="col-6 col-md-3">
                    <i class="bi bi-whatsapp fs-2 text-success"></i>
                    <p class="fw-semibold mb-0 mt-2">Atendimento</p>
                </div>
            </div>
        </div>
    </section>

    <section id="produtos" class="py-5" bg-white>
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-success fw-semibold">NOSSA LOJA</span>
                <h2 class="fw-bold mt-2">Produtos para seu pet</h2>
                <p class="text-secondary">
                    Confira alguns dos produtos disponíveis.
                </p>
            </div>

            <div class="row g-4">
                <?php if (count($produtos) > 0): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <?php
                        $imagemProduto = '';

                        if (!empty($produto['imagem'])) {
                            $nomeArquivo = basename($produto['imagem']);
                            $caminhoProduto = __DIR__ . '/uploads/produtos/' . $nomeArquivo;

                            if (is_file($caminhoProduto)) {
                                $imagemProduto = 'uploads/produtos/' . rawurlencode($nomeArquivo);
                            }
                        }
                        ?>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="bg-white text-center overflow-hidden">
                                    <?php if ($imagemProduto !== ''): ?>
                                        <img
                                            src="<?= escapar($imagemProduto) ?>"
                                            alt="<?= escapar($produto['nome']) ?>"
                                            class="img-fluid w-100"
                                            style="height: 220px; object-fit: contain;"
                                            loading="lazy">
                                    <?php else: ?>
                                        <div class="bg-success-subtle text-success py-5">
                                            <i class="bi bi-box-seam display-3"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <span class="badge text-bg-success align-self-start mb-2">
                                        Produto
                                    </span>

                                    <h5 class="card-title fw-bold">
                                        <?= escapar($produto['nome']) ?>
                                    </h5>

                                    <p class="card-text text-secondary flex-grow-1">
                                        <?= nl2br(escapar($produto['descricao'])) ?>
                                    </p>

                                    <div class="fs-4 fw-bold text-success mb-3">
                                        R$ <?= number_format((float) $produto['valor'], 2, ',', '.') ?>
                                    </div>

                                    <a
                                        href="https://wa.me/<?= escapar($whatsapp) ?>?text=<?= rawurlencode('Olá! Tenho interesse no produto: ' . $produto['nome']) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-success">
                                        <i class="bi bi-whatsapp me-2"></i>Tenho interesse
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-light border text-center py-4">
                            <i class="bi bi-box-seam display-5 text-success"></i>
                            <h5 class="mt-3">Nenhum produto cadastrado</h5>
                            <p class="text-secondary mb-0">
                                Os produtos ativos aparecerão aqui quando forem cadastrados.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="servicos" class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-success fw-semibold">CUIDADOS ESPECIAIS</span>
                <h2 class="fw-bold mt-2">Nossos serviços</h2>
                <p class="text-secondary">
                    Cuidado e atenção para o bem-estar do seu companheiro.
                </p>
            </div>

            <div class="row g-4">
                <?php if (count($servicos) > 0): ?>
                    <?php foreach ($servicos as $servico): ?>
                        <?php
                        $imagemServico = '';

                        if (!empty($servico['imagem'])) {
                            $nomeArquivo = basename($servico['imagem']);
                            $caminhoServico = __DIR__ . '/uploads/produtos/' . $nomeArquivo;

                            if (is_file($caminhoServico)) {
                                $imagemServico = 'uploads/produtos/' . rawurlencode($nomeArquivo);
                            }
                        }
                        ?>

                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="text-center mb-3 overflow-hidden">
                                        <?php if ($imagemServico !== ''): ?>
                                            <img
                                                src="<?= escapar($imagemServico) ?>"
                                                alt="<?= escapar($servico['nome']) ?>"
                                                class="img-fluid rounded"
                                                style="height: 160px; width: 100%; object-fit: contain;"
                                                loading="lazy">
                                        <?php else: ?>
                                            <div class="bg-success-subtle text-success rounded py-4">
                                                <i class="bi bi-scissors display-5"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <h5 class="fw-bold text-center">
                                        <?= escapar($servico['nome']) ?>
                                    </h5>

                                    <p class="text-secondary text-center flex-grow-1">
                                        <?= nl2br(escapar($servico['descricao'])) ?>
                                    </p>

                                    <div class="fs-5 fw-bold text-success text-center mb-3">
                                        <?php if ($servico['preco_minimo'] !== null && $servico['preco_maximo'] !== null): ?>
                                            R$ <?= number_format((float) $servico['preco_minimo'], 2, ',', '.') ?>
                                            a
                                            R$ <?= number_format((float) $servico['preco_maximo'], 2, ',', '.') ?>
                                        <?php else: ?>
                                            Consulte o preço
                                        <?php endif; ?>
                                    </div>

                                    <a
                                        href="https://wa.me/<?= escapar($whatsapp) ?>?text=<?= rawurlencode('Olá! Gostaria de saber mais sobre o serviço: ' . $servico['nome']) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-outline-success">
                                        <i class="bi bi-whatsapp me-2"></i>Consultar serviço
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-light border text-center py-4">
                            <i class="bi bi-scissors display-5 text-success"></i>
                            <h5 class="mt-3">Nenhum serviço cadastrado</h5>
                            <p class="text-secondary mb-0">
                                Os serviços ativos aparecerão aqui quando forem cadastrados.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="contato" class="py-5 bg-success text-white">
        <div class="container text-center">
            <i class="bi bi-chat-dots display-4"></i>
            <h2 class="fw-bold mt-3">Vamos cuidar do seu pet?</h2>
            <p class="lead mb-4">
                Entre em contato para consultar produtos e serviços.
            </p>

            <a
                href="https://wa.me/<?= escapar($whatsapp) ?>?text=<?= rawurlencode('Olá! Gostaria de conhecer os produtos e serviços do PetShop.') ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-light btn-lg text-success fw-semibold">
                <i class="bi bi-whatsapp me-2"></i>Fale conosco
            </a>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-md-6 text-center text-md-start">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-heart-fill text-success me-2"></i>PetShop
                    </h5>
                    <p class="text-white-50 mb-0">
                        Cuidado, carinho e qualidade para seu melhor amigo.
                    </p>
                </div>

                <div class="col-md-6 text-center text-md-end">
                    <a href="admin/login.php" class="link-light text-decoration-none">
                        <i class="bi bi-person-lock me-1"></i>Área administrativa
                    </a>

                    <p class="text-white-50 small mt-2 mb-0">
                        &copy; <?= date('Y') ?> PetShop. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>