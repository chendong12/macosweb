<?php

declare(strict_types=1);

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function render_admin_layout(string $title, string $active, callable $content): void
{
    $user = require_login();
    $username = (string) $user['username'];
    $navItems = [
        'home' => ['label' => '首页', 'href' => 'dashboard.php'],
        'score_create' => ['label' => '成绩录入', 'href' => 'score_create.php'],
        'score_list' => ['label' => '成绩展示', 'href' => 'score_list.php'],
    ];
    ?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar" aria-label="功能菜单">
            <div class="sidebar-brand">
                <span>Codex</span>
                <strong>教学后台</strong>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($navItems as $key => $item): ?>
                    <a class="<?= $active === $key ? 'active' : '' ?>" href="<?= h($item['href']) ?>">
                        <?= h($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>

        <div class="admin-main">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Codex 编程教学</p>
                    <h1><?= h($title) ?></h1>
                </div>

                <div class="topbar-actions">
                    <span><?= h($username) ?></span>
                    <a class="logout-link" href="logout.php">退出登录</a>
                </div>
            </header>

            <main class="content-area">
                <?php $content(); ?>
            </main>
        </div>
    </div>
</body>
</html>
    <?php
}
