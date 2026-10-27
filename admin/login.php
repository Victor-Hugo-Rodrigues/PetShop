<?php
session_start();

require_once __DIR__ . '/../conexao.php';

if (isset($_SESSION['administrador_id'])) {
    header('Location: admin/index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $erro = 'Informe um e-mail válido e sua senha.';
    } else {
        $sql = 'SELECT id, nome, email, senha
                FROM administradores
                WHERE email = ?
                LIMIT 1';

        $stmt = mysqli_prepare($conexao, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);

            $resultado = mysqli_stmt_get_result($stmt);
            $administrador = mysqli_fetch_assoc($resultado);

            mysqli_stmt_close($stmt);

            if (
                $administrador &&
                password_verify($senha, $administrador['senha'])
            ) {
                session_regenerate_id(true);

                $_SESSION['administrador_id'] = $administrador['id'];
                $_SESSION['administrador_nome'] = $administrador['nome'];
                $_SESSION['administrador_email'] = $administrador['email'];

                header('Location: admin/index.php');
                exit;
            }

            $erro = 'E-mail ou senha incorretos.';
        } else {
            error_log(mysqli_error($conexao));
            $erro = 'Não foi possível realizar o login. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | Pet Shop</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>

<body class="login-pet">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
        <div class="card login-card border-0 shadow">
            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">
                    <div class="login-logo">🐾</div>
                    <h1 class="h3 fw-bold">Pet Shop</h1>
                    <p class="text-secondary">Área administrativa</p>
                </div>

                <?php if ($erro !== ''): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            maxlength="150"
                            autocomplete="username"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input
                            type="password"
                            class="form-control"
                            id="senha"
                            name="senha"
                            autocomplete="current-password"
                            required>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2">
                        Entrar
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none">
                        Voltar ao início
                    </a>
                </div>

            </div>
        </div>
    </main>
</body>

</html>