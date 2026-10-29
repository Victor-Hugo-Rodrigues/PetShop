<?php
require_once __DIR__ . '/conexao.php';

function escapar($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
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

function imagemItem($imagem): string
{
    if (empty($imagem)) {
        return '';
    }

    $arquivo = basename((string)$imagem);
    $caminho = __DIR__ . '/uploads/produtos/' . $arquivo;

    return is_file($caminho)
        ? 'uploads/produtos/' . rawurlencode($arquivo)
        : '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Produtos, banho, cuidados e carinho para seu melhor amigo no PetShop.">
    <title>PetShop | Um cuidado a mais para quem você ama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --pet-green: #246b52;
            --pet-green-dark: #164936;
            --pet-cream: #fbf7ef;
            --pet-peach: #f5e3d5;
            --pet-ink: #25352d;
        }
        body { background: var(--pet-cream); color: var(--pet-ink); }
        .navbar { background: #fff; }
        .navbar-brand { color: var(--pet-green-dark); letter-spacing: -.04em; }
        .navbar-brand:hover { color: var(--pet-green); }
        .navbar .nav-link { color: #526359; font-weight: 500; }
        .navbar .nav-link:hover { color: var(--pet-green); }
        .btn-pet { background: var(--pet-green); border-color: var(--pet-green); color: #fff; }
        .btn-pet:hover { background: var(--pet-green-dark); border-color: var(--pet-green-dark); color: #fff; }
        .btn-outline-pet { color: var(--pet-green); border-color: var(--pet-green); }
        .btn-outline-pet:hover { color: #fff; background: var(--pet-green); }
        .hero { background: var(--pet-peach); overflow: hidden; }
        .hero-kicker { color: var(--pet-green); letter-spacing: .13em; font-size: .76rem; font-weight: 800; }
        .hero h1 { color: var(--pet-green-dark); letter-spacing: -.055em; line-height: 1.03; }
        .hero-copy { color: #5f6b62; max-width: 38rem; }
        .hero-photo { min-height: 350px; height: 100%; width: 100%; object-fit: cover; border-radius: 48% 48% 22px 22px; }
        .trust-strip { background: #fff; }
        .trust-item { display: flex; align-items: center; gap: .85rem; justify-content: center; }
        .trust-icon { display: grid; place-items: center; width: 46px; height: 46px; border-radius: 15px; background: #e8f0e9; color: var(--pet-green); font-size: 1.3rem; }
        .section-eyebrow { font-size: .75rem; letter-spacing: .15em; font-weight: 800; color: var(--pet-green); }
        .section-title { color: var(--pet-green-dark); letter-spacing: -.04em; }
        .item-card { border: 1px solid #eee8dd; border-radius: 18px; overflow: hidden; background: #fff; transition: transform .2s ease, box-shadow .2s ease; }
        .item-card:hover { transform: translateY(-4px); box-shadow: 0 14px 28px rgba(37,53,45,.09) !important; }
        .product-media { min-height: 225px; background: #f4f0e8; display: grid; place-items: center; }
        .product-media img { height: 225px; width: 100%; object-fit: contain; }
        .service-media { background: #e8f0e9; min-height: 140px; display: grid; place-items: center; }
        .service-media img { height: 170px; width: 100%; object-fit: contain; }
        .price { color: var(--pet-green); }
        .contact-band { background: var(--pet-green-dark); }
        footer { background: #18382b; color: #fff; }
        .empty-state { border: 1px dashed #cbd7cc; border-radius: 18px; background: rgba(255,255,255,.7); }
        @media (max-width: 767.98px) {
            .hero { text-align: center; }
            .hero-copy { margin-inline: auto; }
            .hero-photo { min-height: 250px; max-height: 310px; border-radius: 24px; }
            .trust-item { justify-content: flex-start; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container py-2">
        <a class="navbar-brand fw-bold fs-3" href="index.php"><i class="bi bi-heart-fill me-2"></i>pet<span class="fw-normal">shop</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#inicio">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="#produtos">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="#servicos">Serviços</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
                <li class="nav-item ms-lg-2"><a href="admin/login.php" class="btn btn-outline-pet rounded-pill px-3 mt-2 mt-lg-0"><i class="bi bi-person-lock me-1"></i>Administração</a></li>
            </ul>
        </div>
    </div>
</nav>

<header id="inicio" class="hero py-5">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 py-lg-4">
                <div class="hero-kicker mb-3">CUIDADO QUE VEM DO CORAÇÃO</div>
                <h1 class="display-2 fw-bold mb-4">Seu pet merece<br>um mundo de carinho.</h1>
                <p class="hero-copy fs-5 mb-4">Tudo para deixar a rotina do seu melhor amigo mais saudável, feliz e cheia de momentos bons.</p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="#produtos" class="btn btn-pet btn-lg rounded-pill px-4">Explorar produtos <i class="bi bi-arrow-right ms-2"></i></a>
                    <a href="#servicos" class="btn btn-outline-pet btn-lg rounded-pill px-4">Conhecer serviços</a>
                </div>
                <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-2 mt-4 text-secondary small">
                    <i class="bi bi-heart-fill" style="color:var(--pet-green)"></i>
                    <span>Mais carinho em cada detalhe.</span>
                </div>
            </div>
            <div class="col-lg-6">
                <img class="hero-photo" src="https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=1200&q=85" alt="Cachorro feliz olhando para a câmera" fetchpriority="high">
            </div>
        </div>
    </div>
</header>

<section class="trust-strip py-4 border-bottom">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-icon"><i class="bi bi-heart-pulse"></i></span><div><div class="fw-bold">Bem-estar</div><div class="small text-secondary">Cuidado de verdade</div></div></div></div>
            <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-icon"><i class="bi bi-stars"></i></span><div><div class="fw-bold">Qualidade</div><div class="small text-secondary">Escolhas especiais</div></div></div></div>
            <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-icon"><i class="bi bi-scissors"></i></span><div><div class="fw-bold">Banho e cuidado</div><div class="small text-secondary">Serviços para seu pet</div></div></div></div>
            <div class="col-6 col-lg-3"><div class="trust-item"><span class="trust-icon"><i class="bi bi-whatsapp"></i></span><div><div class="fw-bold">Atendimento próximo</div><div class="small text-secondary">Fale com a gente</div></div></div></div>
        </div>
    </div>
</section>

<section id="produtos" class="py-5">
    <div class="container py-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
            <div>
                <div class="section-eyebrow mb-2">ESCOLHIDOS COM CARINHO</div>
                <h2 class="section-title display-5 fw-bold mb-2">Achadinhos para seu pet</h2>
                <p class="text-secondary mb-0">Produtos que ajudam seu companheiro a viver melhor.</p>
            </div>
            <span class="text-secondary small"><i class="bi bi-box-seam me-1"></i> <?= count($produtos) ?> produto(s) em destaque</span>
        </div>
        <div class="row g-4">
            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                    <?php $imagemProduto = imagemItem($produto['imagem']); ?>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <article class="item-card h-100 shadow-sm d-flex flex-column">
                            <div class="product-media">
                                <?php if ($imagemProduto !== ''): ?>
                                    <img src="<?= escapar($imagemProduto) ?>" alt="<?= escapar($produto['nome']) ?>" loading="lazy">
                                <?php else: ?>
                                    <i class="bi bi-box-seam display-2" style="color:#7d9b85"></i>
                                <?php endif; ?>
                            </div>
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <span class="small fw-bold text-uppercase mb-2" style="color:var(--pet-green);letter-spacing:.08em">Produto</span>
                                <h3 class="h5 fw-bold"><?= escapar($produto['nome']) ?></h3>
                                <p class="text-secondary small flex-grow-1"><?= nl2br(escapar($produto['descricao'])) ?></p>
                                <div class="price fs-4 fw-bold mb-3">R$ <?= number_format((float)$produto['valor'], 2, ',', '.') ?></div>
                                <a href="https://wa.me/<?= escapar($whatsapp) ?>?text=<?= rawurlencode('Olá! Tenho interesse no produto: ' . $produto['nome']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-pet rounded-pill w-100"><i class="bi bi-whatsapp me-2"></i>Consultar produto</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><div class="empty-state text-center py-5 px-3"><i class="bi bi-box-seam display-4" style="color:var(--pet-green)"></i><h3 class="h5 fw-bold mt-3">Novidades chegando</h3><p class="text-secondary mb-0">Os produtos cadastrados aparecerão aqui.</p></div></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="servicos" class="py-5 bg-white">
    <div class="container py-lg-4">
        <div class="text-center mb-5">
            <div class="section-eyebrow mb-2">MOMENTO DE CUIDAR</div>
            <h2 class="section-title display-5 fw-bold mb-2">Um carinho a mais</h2>
            <p class="text-secondary mx-auto" style="max-width:580px">Serviços pensados para manter seu companheiro confortável, limpinho e bem cuidado.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php if (count($servicos) > 0): ?>
                <?php foreach ($servicos as $servico): ?>
                    <?php $imagemServico = imagemItem($servico['imagem']); ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="item-card h-100 shadow-sm d-flex flex-column">
                            <div class="service-media">
                                <?php if ($imagemServico !== ''): ?>
                                    <img src="<?= escapar($imagemServico) ?>" alt="<?= escapar($servico['nome']) ?>" loading="lazy">
                                <?php else: ?>
                                    <i class="bi bi-scissors display-3" style="color:#5d806b"></i>
                                <?php endif; ?>
                            </div>
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <h3 class="h4 fw-bold text-center"><?= escapar($servico['nome']) ?></h3>
                                <p class="text-secondary text-center flex-grow-1"><?= nl2br(escapar($servico['descricao'])) ?></p>
                                <div class="price fs-5 fw-bold text-center mb-3">
                                    <?php if ($servico['preco_minimo'] !== null && $servico['preco_maximo'] !== null): ?>
                                        R$ <?= number_format((float)$servico['preco_minimo'], 2, ',', '.') ?> a R$ <?= number_format((float)$servico['preco_maximo'], 2, ',', '.') ?>
                                    <?php else: ?>
                                        Consulte o preço
                                    <?php endif; ?>
                                </div>
                                <a href="https://wa.me/<?= escapar($whatsapp) ?>?text=<?= rawurlencode('Olá! Gostaria de saber mais sobre o serviço: ' . $servico['nome']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-pet rounded-pill w-100"><i class="bi bi-whatsapp me-2"></i>Consultar serviço</a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><div class="empty-state text-center py-5 px-3"><i class="bi bi-scissors display-4" style="color:var(--pet-green)"></i><h3 class="h5 fw-bold mt-3">Serviços feitos com carinho</h3><p class="text-secondary mb-0">Os serviços cadastrados aparecerão aqui.</p></div></div>
            <?php endif; ?>
        </div>
    </div>
</section>


<section id="faq" class="py-5">
    <div class="container py-lg-4">
        <div class="row g-5 align-items-start">
            <div class="col-lg-4">
                <div class="section-eyebrow mb-2">DÚVIDAS FREQUENTES</div>
                <h2 class="section-title display-5 fw-bold mb-3">Tem alguma dúvida?</h2>
                <p class="text-secondary mb-4">Reunimos algumas respostas para ajudar você a cuidar melhor do seu pet e conhecer nossos produtos e serviços.</p>
                <a href="https://wa.me/<?= escapar($whatsapp) ?>?text=<?= rawurlencode('Olá! Tenho uma dúvida sobre o PetShop.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-pet rounded-pill px-4">
                    <i class="bi bi-whatsapp me-2"></i>Pergunte pelo WhatsApp
                </a>
            </div>
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 border-bottom bg-transparent">
                        <h3 class="accordion-header">
                            <button class="accordion-button bg-transparent fw-semibold px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqUm" aria-expanded="true" aria-controls="faqUm">
                                Como consulto o preço de um produto?
                            </button>
                        </h3>
                        <div id="faqUm" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-0 pt-0 text-secondary">
                                Os preços dos produtos disponíveis aparecem na seção de produtos. Se precisar confirmar disponibilidade ou tiver alguma dúvida, fale com a gente pelo WhatsApp.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 border-bottom bg-transparent">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-semibold px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqDois" aria-expanded="false" aria-controls="faqDois">
                                Por que o preço do banho pode variar?
                            </button>
                        </h3>
                        <div id="faqDois" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-0 pt-0 text-secondary">
                                O valor pode variar conforme o porte do animal e os cuidados necessários. Consulte a faixa de preço exibida no serviço e confirme o valor pelo WhatsApp antes de agendar.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 border-bottom bg-transparent">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-semibold px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqTres" aria-expanded="false" aria-controls="faqTres">
                                Como faço para solicitar um serviço?
                            </button>
                        </h3>
                        <div id="faqTres" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-0 pt-0 text-secondary">
                                Clique em “Consultar serviço” no cartão do serviço desejado. Você será direcionado ao WhatsApp para tirar dúvidas e combinar os detalhes do atendimento.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 border-bottom bg-transparent">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-semibold px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqQuatro" aria-expanded="false" aria-controls="faqQuatro">
                                Como sei quais produtos e serviços estão disponíveis?
                            </button>
                        </h3>
                        <div id="faqQuatro" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-0 pt-0 text-secondary">
                                A página apresenta os produtos e serviços ativos cadastrados na loja. Para confirmar a disponibilidade de um item específico, entre em contato com nosso atendimento.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 bg-transparent">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-semibold px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faqCinco" aria-expanded="false" aria-controls="faqCinco">
                                Como entro em contato com o PetShop?
                            </button>
                        </h3>
                        <div id="faqCinco" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-0 pt-0 text-secondary">
                                Use qualquer botão de WhatsApp disponível na página. A mensagem já vai preparada para facilitar o início da conversa.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contato" class="contact-band text-white py-5">
    <div class="container py-lg-4 text-center">
        <div class="mb-3"><i class="bi bi-chat-heart display-3"></i></div>
        <p class="small fw-bold text-uppercase mb-2" style="letter-spacing:.16em;color:#d7e7d8">ESTAMOS AQUI POR VOCÊ</p>
        <h2 class="display-5 fw-bold mb-3">Vamos deixar o dia dele mais feliz?</h2>
        <p class="lead mb-4 text-white-50">Converse com a gente sobre produtos, preços e cuidados para seu pet.</p>
        <a href="https://wa.me/<?= escapar($whatsapp) ?>?text=<?= rawurlencode('Olá! Gostaria de conhecer os produtos e serviços do PetShop.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-lg rounded-pill px-4" style="color:var(--pet-green-dark)"><i class="bi bi-whatsapp me-2"></i>Falar pelo WhatsApp</a>
    </div>
</section>

<footer class="py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="text-center text-md-start">
            <div class="fs-4 fw-bold"><i class="bi bi-heart-fill me-2" style="color:#b5d2b9"></i>pet<span class="fw-normal">shop</span></div>
            <div class="small text-white-50">Pequenos cuidados. Grandes demonstrações de amor.</div>
        </div>
        <div class="text-center text-md-end small">
            <a href="admin/login.php" class="link-light text-decoration-none"><i class="bi bi-person-lock me-1"></i>Área administrativa</a>
            <div class="text-white-50 mt-2">&copy; <?= date('Y') ?> PetShop. Todos os direitos reservados.</div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>