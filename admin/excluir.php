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

// Valida o token CSRF antes de executar qualquer exclusão.
$tokenSessao = $_SESSION['csrf_token'] ?? '';
$tokenFormulario = $_POST['csrf_token'] ?? '';

if (
    !is_string($tokenSessao) ||
    $tokenSessao === '' ||
    !is_string($tokenFormulario) ||
    $tokenFormulario === '' ||
    !hash_equals($tokenSessao, $tokenFormulario)
) {
    header('Location: produtos.php?erro=csrf');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: produtos.php?erro=id_invalido');
    exit;
}

$sql = "DELETE FROM itens WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    error_log(mysqli_error($conexao));
    header('Location: produtos.php?erro=exclusao');
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);

$executado = mysqli_stmt_execute($stmt);
$afetadas = mysqli_stmt_affected_rows($stmt);

mysqli_stmt_close($stmt);

if ($executado && $afetadas > 0) {
    header('Location: produtos.php?sucesso=excluido');
    exit;
}

header('Location: produtos.php?erro=exclusao');
exit;
