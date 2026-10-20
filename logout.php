<?php

require_once __DIR__ . '/config/helpers.php';
start_app_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to(app_url('index.php'));
}

if (!verify_csrf_token(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    redirect_with_flash('login.php', 'auth', 'Sesi Anda berakhir. Silakan masuk lagi.');
}

$_SESSION = array();

if (ini_get('session.use_cookies')) {
    $cookieParameters = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $cookieParameters['path'],
        $cookieParameters['domain'],
        $cookieParameters['secure'],
        $cookieParameters['httponly']
    );
}

session_destroy();
redirect_to(app_url('index.php'));
