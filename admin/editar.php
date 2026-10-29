
<?php
session_start();

if (!isset($_SESSION['administrador_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: produtos.php');
    exit;
}

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')
) {
    header('Location: produtos.php?erro=csrf');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$tipo = $_POST['tipo'] ?? '';

$valorTexto = str_replace(',', '.', trim($_POST['valor'] ?? ''));
$minimoTexto = str_replace(',', '.', trim($_POST['preco_minimo'] ?? ''));
$maximoTexto = str_replace(',', '.', trim($_POST['preco_maximo'] ?? ''));
$estoqueTexto = trim($_POST['estoque'] ?? '');

function precoValido(string $texto): bool
{
    return $texto !== ''
        && is_numeric($texto)
        && is_finite((float) $texto)
        && (float) $texto >= 0
        && (float) $texto <= 99999999.99;
}

$valorValido = precoValido($valorTexto);

$estoqueValido = $estoqueTexto === ''
    || (ctype_digit($estoqueTexto) && (float) $estoqueTexto <= 2147483647);

if (
    !$id ||
    $id < 1 ||
    $nome === '' ||
    mb_strlen($nome) > 100 ||
    mb_strlen($descricao) > 255 ||
    !in_array($tipo, ['produto', 'servico'], true) ||
    !$estoqueValido
) {
    header('Location: produtos.php?erro=dados_invalidos');
    exit;
}

if ($tipo === 'produto') {
    if (!$valorValido) {
        header('Location: produtos.php?erro=dados_invalidos');
        exit;
    }

    $valor = (float) $valorTexto;
    $estoque = $estoqueTexto === '' ? null : (int) $estoqueTexto;
    $precoMinimo = null;
    $precoMaximo = null;
} else {
    if (
        !precoValido($minimoTexto) ||
        !precoValido($maximoTexto) ||
        (float) $minimoTexto > (float) $maximoTexto
    ) {
        header('Location: produtos.php?erro=dados_invalidos');
        exit;
    }

    $precoMinimo = (float) $minimoTexto;
    $precoMaximo = (float) $maximoTexto;
    $valor = $precoMinimo;
    $estoque = null;
}

$sql = "UPDATE itens
        SET nome = ?, descricao = ?, tipo = ?, valor = ?, estoque = ?,
            preco_minimo = ?, preco_maximo = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    error_log(mysqli_error($conexao));
    header('Location: produtos.php?erro=edicao');
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'sssidddi',
    $nome,
    $descricao,
    $tipo,
    $valor,
    $estoque,
    $precoMinimo,
    $precoMaximo,
    $id
);

$executado = mysqli_stmt_execute($stmt);
$erro = mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);

if ($executado) {
    header('Location: produtos.php?sucesso=editado');
    exit;
}

error_log($erro);
header('Location: produtos.php?erro=edicao');
exit;
