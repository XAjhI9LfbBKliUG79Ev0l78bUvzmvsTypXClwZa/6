<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/persistence.php';

if (isset($_SESSION['user'])) {
    header('Location: feed.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['username']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    $email = trim($_POST['email']);

    if (empty($login) || empty($password) || empty($password_confirm)) {
        $error_message = 'Логин и пароль являются обязательными полями.';
    } elseif ($password !== $password_confirm) {
        $error_message = 'Пароли не совпадают.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Неверный формат электронной почты.';
    } elseif (find_user_by_login($login)) {
        $error_message = 'Пользователь с таким логином уже существует!';
    } else {
        add_user([
            'login' => $login,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email
        ]);

        $_SESSION['success_message'] = "Поздравляем, регистрация прошла успешно! Теперь вы можете войти.";
        $_SESSION['show_profile_prompt'] = true;
        header('Location: index.php');
        exit();
    }
}

$page_title = __('register_title');
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
        <h1><?= __('register_header') ?></h1>

        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="input-group">
                <label for="username"><?= __('username_login_label') ?></label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="email"><?= __('email_optional_label') ?></label>
                <input type="email" id="email" name="email">
            </div>
            <div class="input-group">
                <label for="password"><?= __('password_label') ?></label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="password_confirm"><?= __('password_confirm_label') ?></label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit"><?= __('register_button') ?></button>
        </form>
        <div class="link-block">
            <p><?= __('have_account_prompt') ?> <a href="index.php"><?= __('login_button') ?></a></p>
        </div>
    </div>
</body>
</html>
