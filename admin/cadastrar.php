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

function escaparCadastro($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$erro = '';
$nome = '';
$descricao = '';
$tipo = 'produto';
$valor = '';
$precoMinimo = '';
$precoMaximo = '';
$estoque = '';
$mostrarCampos = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo = $_POST['tipo'] ?? 'produto';
    $valor = str_replace(',', '.', trim($_POST['valor'] ?? ''));
    $precoMinimo = str_replace(',', '.', trim($_POST['preco_minimo'] ?? ''));
    $precoMaximo = str_replace(',', '.', trim($_POST['preco_maximo'] ?? ''));
    $estoque = trim($_POST['estoque'] ?? '');
    $token = $_POST['csrf_token'] ?? '';
    $acao = $_POST['acao'] ?? 'salvar';

    $mostrarCampos = $acao === 'mostrar_campos';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $erro = 'Formulário inválido. Atualize a página e tente novamente.';
    } elseif (!in_array($tipo, ['produto', 'servico'], true)) {
        $erro = 'Selecione um tipo válido.';
    } elseif ($mostrarCampos) {
        // Apenas atualiza os campos exibidos, sem cadastrar.
    } elseif (
        $nome === '' ||
        mb_strlen($nome) > 100 ||
        mb_strlen($descricao) > 255
    ) {
        $erro = 'Informe um nome válido e uma descrição de até 255 caracteres.';
    } elseif (
        $tipo === 'produto' &&
        (
            $valor === '' ||
            !is_numeric($valor) ||
            !is_finite((float) $valor) ||
            (float) $valor < 0 ||
            (float) $valor > 99999999.99
        )
    ) {
        $erro = 'Informe um preço válido para o produto.';
    } elseif (
        $tipo === 'servico' &&
        (
            $precoMinimo === '' ||
            $precoMaximo === '' ||
            !is_numeric($precoMinimo) ||
            !is_numeric($precoMaximo) ||
            !is_finite((float) $precoMinimo) ||
            !is_finite((float) $precoMaximo) ||
            (float) $precoMinimo < 0 ||
            (float) $precoMaximo < (float) $precoMinimo ||
            (float) $precoMaximo > 99999999.99
        )
    ) {
        $erro = 'Informe uma faixa de preços válida. O preço máximo deve ser igual ou superior ao mínimo.';
    } elseif (
        $tipo === 'produto' &&
        ($estoque === '' || !ctype_digit($estoque) || (float) $estoque > 2147483647)
    ) {
        $erro = 'Informe um estoque inteiro válido para o produto.';
    } else {
        $arquivo = $_FILES['imagem'] ?? null;
        $nomeImagem = null;
        $caminhoTemporario = null;
        $destino = null;

        if ($arquivo && $arquivo['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                $erro = 'Não foi possível enviar a imagem.';
            } elseif ($arquivo['size'] > 5 * 1024 * 1024) {
                $erro = 'A imagem deve ter no máximo 5 MB.';
            } else {
                $info = new finfo(FILEINFO_MIME_TYPE);
                $mime = $info->file($arquivo['tmp_name']);

                $tiposPermitidos = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($tiposPermitidos[$mime])) {
                    $erro = 'Envie uma imagem JPG, PNG ou WEBP.';
                } else {
                    $pasta = __DIR__ . '/../uploads/produtos/';

                    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
                        $erro = 'Não foi possível criar a pasta das imagens.';
                    } elseif (!is_writable($pasta)) {
                        $erro = 'A pasta das imagens não permite gravação.';
                    } else {
                        $nomeImagem = bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$mime];
                        $caminhoTemporario = $arquivo['tmp_name'];
                        $destino = $pasta . $nomeImagem;
                    }
                }
            }
        }

        if ($erro === '') {
            if ($tipo === 'produto') {
                $valorBanco = (float) $valor;
                $minimoBanco = null;
                $maximoBanco = null;
                $estoqueBanco = (int) $estoque;
            } else {
                $valorBanco = (float) $precoMinimo;
                $minimoBanco = (float) $precoMinimo;
                $maximoBanco = (float) $precoMaximo;
                $estoqueBanco = null;
            }

            if ($nomeImagem !== null && !move_uploaded_file($caminhoTemporario, $destino)) {
                $erro = 'Não foi possível salvar a imagem enviada.';
            }

            if ($erro === '') {
                $sql = 'INSERT INTO itens
                    (nome, descricao, tipo, valor, estoque, imagem, preco_minimo, preco_maximo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

                $stmt = mysqli_prepare($conexao, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param(
                        $stmt,
                        'sssdisdd',
                        $nome,
                        $descricao,
                        $tipo,
                        $valorBanco,
                        $estoqueBanco,
                        $nomeImagem,
                        $minimoBanco,
                        $maximoBanco
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        header('Location: produtos.php?sucesso=cadastrado');
                        exit;
                    }

                    error_log(mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);

                    if ($nomeImagem !== null && $destino && is_file($destino)) {
                        unlink($destino);
                    }

                    $erro = 'Não foi possível cadastrar o item.';
                } else {
                    error_log(mysqli_error($conexao));

                    if ($nomeImagem !== null && $destino && is_file($destino)) {
                        unlink($destino);
                    }

                    $erro = 'Erro ao preparar o cadastro.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar item | Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-paw-fill me-2"></i>Pet Shop
            </a>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Sair
            </a>
        </div>
    </nav>

    <main class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px;">
            <div class="card-body p-4 p-md-5">

                <h1 class="h3 fw-bold mb-2">Cadastrar produto ou serviço</h1>
                <p class="text-secondary mb-4">
                    Preencha os dados do item que deseja cadastrar.
                </p>

                <?php if ($erro !== ''): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= escaparCadastro($erro) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= escaparCadastro($_SESSION['csrf_token']) ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome"
                            maxlength="100" value="<?= escaparCadastro($nome) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao"
                            maxlength="255" rows="3"><?= escaparCadastro($descricao) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="produto" <?= $tipo === 'produto' ? 'selected' : '' ?>>Produto</option>
                            <option value="servico" <?= $tipo === 'servico' ? 'selected' : '' ?>>Serviço</option>
                        </select>
                    </div>

                    <?php if ($tipo === 'servico'): ?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="preco_minimo" class="form-label">Preço mínimo (R$)</label>
                                <input type="number" class="form-control" id="preco_minimo"
                                    name="preco_minimo" min="0" max="99999999.99" step="0.01"
                                    value="<?= escaparCadastro($precoMinimo) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="preco_maximo" class="form-label">Preço máximo (R$)</label>
                                <input type="number" class="form-control" id="preco_maximo"
                                    name="preco_maximo" min="0" max="99999999.99" step="0.01"
                                    value="<?= escaparCadastro($precoMaximo) ?>" required>
                            </div>
                        </div>

                        <div class="alert alert-info py-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Informe a faixa de preço conforme o porte do animal.
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Preço (R$)</label>
                            <input type="number" class="form-control" id="valor" name="valor"
                                min="0" max="99999999.99" step="0.01"
                                value="<?= escaparCadastro($valor) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="estoque" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="estoque" name="estoque"
                                min="0" step="1" value="<?= escaparCadastro($estoque) ?>" required>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="imagem" class="form-label">Imagem do item</label>
                        <input type="file" class="form-control" id="imagem" name="imagem"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <div class="form-text">Opcional. JPG, PNG ou WEBP, até 5 MB.</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" name="acao" value="mostrar_campos"
                            class="btn btn-outline-success">
                            <i class="bi bi-arrow-repeat me-1"></i>Confirmar tipo
                        </button>

                        <button type="submit" name="acao" value="salvar" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Salvar item
                        </button>

                        <a href="produtos.php" class="btn btn-outline-secondary">Cancelar</a>
                    </div>

                    <p class="form-text mt-3 mb-0">
                        Ao mudar entre produto e serviço, clique em “Confirmar tipo” antes de preencher os campos específicos.
                        Se já tiver selecionado uma imagem, selecione-a novamente após confirmar o tipo.
                    </p>
                </form>

            </div>
        </div>
    </main>

</body>

</html>