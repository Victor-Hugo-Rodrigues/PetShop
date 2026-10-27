<?php
require_once __DIR__ . '/conexao.php';

function escapar($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$sqlProdutos = "SELECT id, nome, descricao, valor
                FROM itens
                WHERE tipo = 'produto' AND ativo = 1
                ORDER BY id DESC
                LIMIT 8";

$sqlServicos = "SELECT id, nome, descricao, valor
                FROM itens
                WHERE tipo = 'servico' AND ativo = 1
                ORDER BY id DESC
                LIMIT 6";

$resultadoProdutos = mysqli_query($conexao, $sqlProdutos);
$resultadoServicos = mysqli_query($conexao, $sqlServicos);

if (!$resultadoProdutos || !$resultadoServicos) {
    error_log(mysqli_error($conexao));
    exit('Não foi possível carregar os itens da loja.');
}

$produtos = mysqli_fetch_all($resultadoProdutos, MYSQLI_ASSOC);
$servicos = mysqli_fetch_all($resultadoServicos, MYSQLI_ASSOC);

$categorias = [
    ['icone' => 'bi-basket2-fill', 'nome' => 'Rações', 'descricao' => 'Nutrição completa'],
    ['icone' => 'bi-controller', 'nome' => 'Brinquedos', 'descricao' => 'Diversão garantida'],
    ['icone' => 'bi-house-heart-fill', 'nome' => 'Acessórios', 'descricao' => 'Conforto e praticidade'],
    ['icone' => 'bi-heart-pulse-fill', 'nome' => 'Higiene', 'descricao' => 'Saúde e bem-estar'],
    ['icone' => 'bi-scissors', 'nome' => 'Banho e tosa', 'descricao' => 'Cuidado profissional']
];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Produtos, acessórios e serviços para cuidar do seu pet.">
    <title>Pet Shop | Cuidado, amor e qualidade</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="#inicio">
                <i class="bi bi-paw-fill"></i> Pet Shop
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal" aria-controls="menuPrincipal"
                aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categorias">Categorias</a></li>
                    <li class="nav-item"><a class="nav-link" href="#produtos">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#servicos">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
                </ul>


                <a href="/petShop/admin/index.php" class="btn btn-outline-light">
                    <i class="bi bi-person-lock"></i> Área administrativa
                </a>

            </div>
        </div>
    </nav>

    <header id="inicio" class="bg-success text-white py-5">
        <div class="container py-lg-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge bg-light text-success mb-3">
                        CUIDADO, AMOR E QUALIDADE
                    </span>

                    <h1 class="display-4 fw-bold">
                        Tudo para o bem-estar do seu pet!
                    </h1>

                    <p class="lead">
                        Produtos, serviços e muito carinho para cães e gatos.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="#produtos" class="btn btn-light btn-lg text-success">
                            <i class="bi bi-cart-fill"></i> Ver produtos
                        </a>

                        <a href="#servicos" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-heart-fill"></i> Nossos serviços
                        </a>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-12 col-sm-4">
                            <i class="bi bi-truck fs-4"></i>
                            <p class="fw-bold mb-0">Entrega rápida</p>
                            <small>Consulte condições</small>
                        </div>

                        <div class="col-12 col-sm-4">
                            <i class="bi bi-shield-check fs-4"></i>
                            <p class="fw-bold mb-0">Qualidade</p>
                            <small>Produtos selecionados</small>
                        </div>

                        <div class="col-12 col-sm-4">
                            <i class="bi bi-heart-fill fs-4"></i>
                            <p class="fw-bold mb-0">Muito carinho</p>
                            <small>Cuidado especial</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 text-center">
                    <div class="bg-white text-success rounded-4 shadow p-5">
                        <i class="bi bi-heart-fill display-1"></i>
                        <h2 class="h3 fw-bold mt-3">Seu pet merece o melhor!</h2>
                        <p class="mb-0 text-secondary">
                            Cuidado e carinho em cada detalhe.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="categorias" class="container py-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Explore nossas categorias</h2>
                <p class="text-secondary">Tudo o que seu melhor amigo precisa.</p>
            </div>

            <div class="row g-3">
                <?php foreach ($categorias as $categoria): ?>
                    <div class="col-6 col-md-4 col-lg">
                        <a href="#produtos"
                            class="card h-100 text-center text-decoration-none text-success shadow-sm">
                            <div class="card-body py-4">
                                <i class="bi <?= escapar($categoria['icone']) ?> display-6"></i>
                                <h3 class="h6 fw-bold mt-3">
                                    <?= escapar($categoria['nome']) ?>
                                </h3>
                                <p class="small text-secondary mb-0">
                                    <?= escapar($categoria['descricao']) ?>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="produtos" class="container py-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h2 class="fw-bold">Produtos em destaque</h2>
                    <p class="text-secondary mb-0">
                        Confira alguns produtos para seu melhor amigo.
                    </p>
                </div>
                <span class="badge text-bg-success">
                    <?= count($produtos) ?> produtos
                </span>
            </div>

            <div class="row g-4">
                <?php if (count($produtos) > 0): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <article class="card h-100 shadow-sm">
                                <div class="bg-success-subtle text-success text-center py-4">
                                    <i class="bi bi-box-seam display-3"></i>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <h3 class="card-title h5">
                                        <?= escapar($produto['nome']) ?>
                                    </h3>

                                    <p class="card-text text-secondary">
                                        <?= escapar($produto['descricao'] ?: 'Um ótimo produto para seu pet.') ?>
                                    </p>

                                    <p class="fs-5 fw-bold text-success mt-auto">
                                        R$ <?= number_format((float)$produto['valor'], 2, ',', '.') ?>
                                    </p>

                                    <a href="#contato" class="btn btn-success w-100">
                                        <i class="bi bi-chat-dots-fill"></i>
                                        Consultar produto
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Em breve, novos produtos estarão disponíveis.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section id="servicos" class="bg-white py-5">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="text-success fw-bold">CARINHO EM CADA DETALHE</span>
                    <h2 class="fw-bold mt-2">Nossos serviços</h2>
                    <p class="text-secondary">Cuidado especial para seu companheiro.</p>
                </div>

                <div class="row g-4">
                    <?php if (count($servicos) > 0): ?>
                        <?php foreach ($servicos as $servico): ?>
                            <div class="col-12 col-md-6 col-lg-4">
                                <article class="card h-100 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="text-success mb-3">
                                            <i class="bi bi-scissors display-5"></i>
                                        </div>

                                        <h3 class="card-title h5 fw-bold">
                                            <?= escapar($servico['nome']) ?>
                                        </h3>

                                        <p class="card-text text-secondary">
                                            <?= escapar($servico['descricao'] ?: 'Cuidado e atenção especial para seu pet.') ?>
                                        </p>

                                        <p class="fs-5 fw-bold text-success">
                                            R$ <?= number_format((float)$servico['valor'], 2, ',', '.') ?>
                                        </p>

                                        <a href="#contato" class="btn btn-outline-success">
                                            Consultar serviço
                                        </a>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p class="text-secondary">
                                Em breve divulgaremos nossos serviços.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="sobre" class="container py-5">
            <div class="bg-success-subtle rounded-4 p-4 p-lg-5">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <i class="bi bi-heart-fill display-3 text-success"></i>
                    </div>
                    <div class="col">
                        <h2 class="fw-bold">Seu pet merece o melhor!</h2>
                        <p class="text-secondary mb-0">
                            Trabalhamos para oferecer produtos de qualidade
                            e cuidado com muito carinho para seu melhor amigo.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contato" class="container pb-5">
            <div class="bg-success text-white rounded-4 p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="fw-bold">
                            <i class="bi bi-calendar-heart"></i>
                            Vamos cuidar do seu pet?
                        </h2>
                        <p class="mb-0">
                            Entre em contato para consultar produtos e serviços.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <a href="https://wa.me/5534999999999"
                            target="_blank" rel="noopener noreferrer"
                            class="btn btn-light btn-lg text-success">
                            <i class="bi bi-whatsapp"></i> Fale conosco
                        </a>
                    </div>
                </div>
            </div>

            <p class="small text-secondary mt-2">
                Substitua o número ilustrativo do WhatsApp pelo contato real da loja.
            </p>
        </section>
    </main>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <a class="navbar-brand fw-bold fs-4 text-white" href="#inicio">
                        <i class="bi bi-paw-fill"></i> Pet Shop
                    </a>
                    <p class="text-white-50 mt-2">
                        Cuidado, amor e qualidade para o seu pet.
                    </p>
                </div>

                <div class="col-6 col-md-3">
                    <h3 class="h6 fw-bold">Links</h3>
                    <ul class="list-unstyled">
                        <li><a class="link-light link-underline-opacity-0" href="#inicio">Início</a></li>
                        <li><a class="link-light link-underline-opacity-0" href="#produtos">Produtos</a></li>
                        <li><a class="link-light link-underline-opacity-0" href="#servicos">Serviços</a></li>
                        <li><a class="link-light link-underline-opacity-0" href="#sobre">Sobre</a></li>
                        <li><a class="link-light link-underline-opacity-0" href="#contato">Contato</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3">
                    <h3 class="h6 fw-bold">Atendimento</h3>
                    <p class="text-white-50">
                        <i class="bi bi-whatsapp"></i> Consulte nosso contato
                    </p>
                    <p class="text-white-50">
                        <i class="bi bi-clock"></i> Consulte nossos horários
                    </p>
                </div>

                <div class="col-12 col-md-2">
                    <h3 class="h6 fw-bold">Acesso</h3>
                    <a href="admin/login.php" class="link-light">
                        Área administrativa
                    </a>
                </div>
            </div>

            <hr class="border-secondary">

            <div class="d-flex flex-wrap justify-content-between gap-2">
                <small class="text-white-50">
                    &copy; <?= date('Y') ?> Pet Shop. Todos os direitos reservados.
                </small>
                <small class="text-white-50">
                    Feito com <i class="bi bi-heart-fill text-danger"></i> para os pets.
                </small>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
</body>

</html>