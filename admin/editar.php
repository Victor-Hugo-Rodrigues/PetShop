
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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$tipo = $_POST['tipo'] ?? '';
$valorTexto = str_replace(',', '.', trim($_POST['valor'] ?? ''));
$estoqueTexto = trim($_POST['estoque'] ?? '');

$valorValido = $valorTexto !== ''
    && is_numeric($valorTexto)
    && is_finite((float) $valorTexto)
    && (float) $valorTexto >= 0
    && (float) $valorTexto <= 99999999.99;

$estoqueValido = $estoqueTexto === ''
    || (ctype_digit($estoqueTexto) && (float) $estoqueTexto <= 2147483647);

if (
    !$id ||
    $id < 1 ||
    $nome === '' ||
    mb_strlen($nome) > 100 ||
    mb_strlen($descricao) > 255 ||
    !in_array($tipo, ['produto', 'servico'], true) ||
    !$valorValido ||
    !$estoqueValido
) {
    header('Location: produtos.php?erro=dados_invalidos');
    exit;
}

$valor = (float) $valorTexto;
$estoque = $estoqueTexto === '' ? null : (int) $estoqueTexto;

if ($tipo === 'servico') {
    $estoque = null;
}

$sql = "UPDATE itens
        SET nome = ?, descricao = ?, tipo = ?, valor = ?, estoque = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    error_log(mysqli_error($conexao));
    header('Location: produtos.php?erro=edicao');
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'sssdii',
    $nome,
    $descricao,
    $tipo,
    $valor,
    $estoque,
    $id
);

$executado = mysqli_stmt_execute($stmt);
$afetadas = mysqli_stmt_affected_rows($stmt);
$erro = mysqli_stmt_error($stmt);

mysqli_stmt_close($stmt);

if ($executado && $afetadas >= 0) {
    header('Location: produtos.php?sucesso=editado');
    exit;
}

error_log($erro);
header('Location: produtos.php?erro=edicao');
exit;
