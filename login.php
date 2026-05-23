<?php

declare(strict_types=1);

session_start();
require __DIR__ . '/config.php';

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'username' => $user['username'],
        ];

        header('Location: dashboard.php');
        exit;
    }

    $error = '用户名或密码错误';
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理员登录</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-page">
        <section class="login-panel" aria-labelledby="login-title">
            <h1 id="login-title">管理员登录</h1>

            <?php if ($error !== ''): ?>
                <div class="alert" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" action="login.php" autocomplete="on">
                <label for="username">用户名</label>
                <input id="username" name="username" type="text" required autofocus>

                <label for="password">密码</label>
                <input id="password" name="password" type="password" required>

                <button type="submit">登录</button>
            </form>
        </section>
    </main>
</body>
</html>
