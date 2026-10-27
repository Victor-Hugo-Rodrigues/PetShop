<?php
session_start();

if (!isset($_SESSION['administrador_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../conexao.php';

$erro = '';
$nome = '';
$descricao = '';
$tipo = 'produto';
$valor = '';
$estoque = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $valor = str_replace(',', '.', $_POST['valor'] ?? '');
    $estoque = $_POST['estoque'] ?? '';

    if (
        $nome === '' ||
        !in_array($tipo, ['produto', 'servico'], true) ||
        !is_numeric($valor) ||
        (float)$valor < 0
    ) {
        $erro = 'Preencha corretamente o nome, o tipo e o preço.';
    } elseif (
        $tipo === 'produto' &&
        ($estoque === '' || filter_var($estoque, FILTER_VALIDATE_INT) === false || (int)$estoque < 0)
    ) {
        $erro = 'Informe um estoque válido para o produto.';
    } elseif (
        $tipo === 'servico' &&
        $estoque !== '' &&
        (filter_var($estoque, FILTER_VALIDATE_INT) === false || (int)$estoque < 0)
    ) {
        $erro = 'O estoque deve ser vazio ou um número inteiro não negativo.';
    } else {
        $valorBanco = (float)$valor;
        $estoqueBanco = $tipo === 'servico' && $estoque === ''
            ? null
            : (int)$estoque;

        $sql = 'INSERT INTO itens (nome, descricao, tipo, valor, estoque)
                VALUES (?, ?, ?, ?, ?)';

        $stmt = mysqli_prepare($conexao, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                'sssdi',
                $nome,
                $descricao,
                $tipo,
                $valorBanco,
                $estoqueBanco
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: produtos.php?sucesso=cadastrado');
                exit;
            }

            error_log(mysqli_stmt_error($stmt));
            $erro = 'Não foi possível cadastrar o item.';
            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($conexao));
            $erro = 'Erro ao preparar o cadastro.';
        }
    }
}

function escapar($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar item | Pet Shop</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estilo.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="index.php">🐾 Pet Shop</a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Sair</a>
        </div>
    </nav>

    <main class="container py-5">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 650px;">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-4">Cadastrar produto ou serviço</h1>

                <?php if ($erro !== ''): ?>
                    <div class="alert alert-danger">
                        <?= escapar($erro) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input
                            type="text"
                            class="form-control"
                            id="nome"
                            name="nome"
                            maxlength="100"
                            value="<?= escapar($nome) ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea
                            class="form-control"
                            id="descricao"
                            name="descricao"
                            maxlength="255"
                            rows="3"><?= escapar($descricao) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="produto" <?= $tipo === 'produto' ? 'selected' : '' ?>>
                                Produto
                            </option>
                            <option value="servico" <?= $tipo === 'servico' ? 'selected' : '' ?>>
                                Serviço
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="valor" class="form-label">Preço (R$)</label>
                        <input
                            type="number"
                            class="form-control"
                            id="valor"
                            name="valor"
                            min="0"
                            step="0.01"
                            value="<?= escapar($valor) ?>"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="estoque" class="form-label">
                            Estoque (obrigatório para produtos)
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="estoque"
                            name="estoque"
                            min="0"
                            step="1"
                            value="<?= escapar($estoque) ?>">
                        <div class="form-text">
                            Para serviços, deixe vazio se não houver estoque aplicável.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        Salvar item
                    </button>

                    <a href="produtos.php" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </form>
            </div>
        </div>
    </main>

</body>

</html>