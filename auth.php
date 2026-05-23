<?php

declare(strict_types=1);

function require_login(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    return $_SESSION['user'];
}
