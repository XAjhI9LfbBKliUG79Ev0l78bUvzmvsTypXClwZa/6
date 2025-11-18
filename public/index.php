<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/persistence.php';

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $user = find_user_by_token($_COOKIE['remember_me']);
    if ($user) {
        $_SESSION['user'] = $user;
        setcookie('last_activity', time(), time() + INACTIVITY_TIMEOUT, '/');
        header('Location: feed.php');
        exit();
    }
}

if (isset($_SESSION['user'])) {
    header('Location: feed.php');
    exit();
}

$error_message = '';
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

if (isset($_GET['timeout'])) {
    $error_message = 'Ваша сессия истекла из-за неактивности. Пожалуйста, войдите снова.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $error_message = 'Логин и пароль не могут быть пустыми.';
    } else {
        $user = find_user_by_login($login);
        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            setcookie('last_activity', time(), time() + INACTIVITY_TIMEOUT, '/');
            setcookie('last_visited', date('Y-m-d H:i:s'), time() + 3600 * 24 * 365, '/');

            if (isset($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                $token_hash = password_hash($token, PASSWORD_DEFAULT);
                $update_data = ['remember_token_hash' => $token_hash];
                update_user($login, $update_data);
                setcookie('remember_me', $token, time() + 3600 * 24 * 30, '/');
            }

            if ($user['login'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: feed.php');
            }
            exit();
        } else {
            $error_message = 'Неверный логин или пароль.';
        }
    }
}

$page_title = __('login_title');
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>" class="theme-<?= $_SESSION['theme'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1><?= __('login_header') ?></h1>

        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="message success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="input-group">
                <label for="username"><?= __('username_label') ?></label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password"><?= __('password_label') ?></label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group checkbox-group">
                <input type="checkbox" id="remember_me" name="remember_me" value="1">
                <label for="remember_me"><?= __('remember_me') ?></label>
            </div>
            <button type="submit"><?= __('login_button') ?></button>
        </form>
        <div class="link-block">
            <p><?= __('no_account_prompt') ?> <a href="register.php"><?= __('register_link') ?></a></p>
        </div>
    </div>
</body>
</html>
