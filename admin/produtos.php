<?php
session_start();

if (!isset($_SESSION['administrador_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../conexao.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function escapar($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$sql = "SELECT id, nome, descricao, tipo, valor, estoque, ativo
        FROM itens
        ORDER BY id DESC";

$resultado = mysqli_query($conexao, $sql);

if (!$resultado) {
    error_log(mysqli_error($conexao));
    exit('Não foi possível carregar os produtos e serviços.');
}

$mensagensSucesso = [
    'cadastrado' => 'Item cadastrado com sucesso!',
    'editado' => 'Item atualizado com sucesso!',
    'excluido' => 'Item excluído com sucesso.'
];

$mensagensErro = [
    'id_invalido' => 'O item informado é inválido.',
    'exclusao' => 'Não foi possível excluir o item.',
    'edicao' => 'Não foi possível atualizar o item.',
    'cadastro' => 'Não foi possível cadastrar o item.',
    'csrf' => 'A sessão expirou ou a solicitação é inválida. Atualize a página e tente novamente.'
];

$sucesso = $_GET['sucesso'] ?? '';
$erro = $_GET['erro'] ?? '';

$mensagemSucesso = $mensagensSucesso[$sucesso] ?? null;
$mensagemErro = $mensagensErro[$erro] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produtos e Serviços | Pet Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-paw-fill me-2"></i>Pet Shop
            </a>

            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-speedometer2 me-1"></i>Painel
                </a>
                <a href="../logout.php" class="btn btn-light btn-sm text-success">
                    <i class="bi bi-box-arrow-right me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Produtos e serviços</h1>
                <p class="text-secondary mb-0">
                    Gerencie os itens cadastrados na sua loja.
                </p>
            </div>

            <a href="cadastrar.php" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>Cadastrar item
            </a>
        </div>

        <?php if ($mensagemSucesso !== null): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= escapar($mensagemSucesso) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <?php if ($mensagemErro !== null): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= escapar($mensagemErro) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h2 class="h6 fw-bold mb-0">
                    <i class="bi bi-list-ul text-success me-2"></i>Itens cadastrados
                </h2>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th class="text-center pe-3">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (mysqli_num_rows($resultado) > 0): ?>
                                <?php while ($item = mysqli_fetch_assoc($resultado)): ?>
                                    <tr>
                                        <td class="ps-3"><?= (int)$item['id'] ?></td>

                                        <td>
                                            <span class="fw-semibold"><?= escapar($item['nome']) ?></span>
                                            <?php if (!empty($item['descricao'])): ?>
                                                <div class="small text-secondary">
                                                    <?= escapar($item['descricao']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($item['tipo'] === 'produto'): ?>
                                                <span class="badge text-bg-primary">Produto</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-info">Serviço</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-nowrap fw-semibold">
                                            R$ <?= number_format((float)$item['valor'], 2, ',', '.') ?>
                                        </td>

                                        <td>
                                            <?= $item['estoque'] === null
                                                ? '—'
                                                : (int)$item['estoque'] ?>
                                        </td>

                                        <td>
                                            <?php if ((int)$item['ativo'] === 1): ?>
                                                <span class="badge text-bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center pe-3 text-nowrap">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar"
                                                data-id="<?= (int)$item['id'] ?>"
                                                data-nome="<?= escapar($item['nome']) ?>"
                                                data-descricao="<?= escapar($item['descricao']) ?>"
                                                data-tipo="<?= escapar($item['tipo']) ?>"
                                                data-valor="<?= escapar($item['valor']) ?>"
                                                data-estoque="<?= escapar($item['estoque']) ?>">
                                                <i class="bi bi-pencil-square me-1"></i>Editar
                                            </button>

                                            <form action="excluir.php" method="POST" class="d-inline"
                                                onsubmit="return confirm('Tem certeza de que deseja excluir este item?');">
                                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= escapar($_SESSION['csrf_token']) ?>">

                                                <button type="submit" class="btn btn-sm btn-outline-danger mb-1">
                                                    <i class="bi bi-trash me-1"></i>Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox display-5 text-secondary"></i>
                                        <p class="fw-semibold mt-3 mb-1">Nenhum item cadastrado</p>
                                        <p class="text-secondary small">Cadastre um produto ou serviço para começar.</p>
                                        <a href="cadastrar.php" class="btn btn-success btn-sm">
                                            Cadastrar primeiro item
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white text-secondary small">
                Total de itens: <?= (int)mysqli_num_rows($resultado) ?>
            </div>
        </div>

        <a href="index.php" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-arrow-left me-1"></i>Voltar ao painel
        </a>
    </main>

    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="tituloModalEditar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <form method="POST" action="editar.php">
                    <div class="modal-header bg-success text-white">
                        <h2 class="modal-title fs-5" id="tituloModalEditar">
                            <i class="bi bi-pencil-square me-2"></i>Editar item
                        </h2>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" id="editar_id">
                        <input type="hidden" name="csrf_token" value="<?= escapar($_SESSION['csrf_token']) ?>">

                        <div class="mb-3">
                            <label for="editar_nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" id="editar_nome" maxlength="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="editar_descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" name="descricao" id="editar_descricao" maxlength="255" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="editar_tipo" class="form-label">Tipo</label>
                            <select class="form-select" name="tipo" id="editar_tipo" required>
                                <option value="produto">Produto</option>
                                <option value="servico">Serviço</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editar_valor" class="form-label">Preço (R$)</label>
                            <input type="number" class="form-control" name="valor" id="editar_valor" min="0" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label for="editar_estoque" class="form-label">Estoque</label>
                            <input type="number" class="form-control" name="estoque" id="editar_estoque" min="0" step="1">
                            <div class="form-text" id="ajudaEstoque">
                                Informe a quantidade disponível do produto.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Salvar alterações
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const modalEditar = document.getElementById('modalEditar');
        const campoTipo = document.getElementById('editar_tipo');
        const campoEstoque = document.getElementById('editar_estoque');
        const ajudaEstoque = document.getElementById('ajudaEstoque');

        function atualizarEstoque() {
            const ehServico = campoTipo.value === 'servico';

            campoEstoque.disabled = ehServico;
            campoEstoque.required = !ehServico;

            if (ehServico) {
                campoEstoque.value = '';
                ajudaEstoque.textContent = 'Serviços não possuem controle de estoque.';
            } else {
                ajudaEstoque.textContent = 'Informe a quantidade disponível do produto.';
            }
        }

        modalEditar.addEventListener('show.bs.modal', function(evento) {
            const botao = evento.relatedTarget;

            document.getElementById('editar_id').value = botao.dataset.id;
            document.getElementById('editar_nome').value = botao.dataset.nome;
            document.getElementById('editar_descricao').value = botao.dataset.descricao;
            campoTipo.value = botao.dataset.tipo;
            document.getElementById('editar_valor').value = botao.dataset.valor;
            campoEstoque.value = botao.dataset.estoque;

            atualizarEstoque();
        });

        campoTipo.addEventListener('change', atualizarEstoque);
    </script>

</body>

</html>