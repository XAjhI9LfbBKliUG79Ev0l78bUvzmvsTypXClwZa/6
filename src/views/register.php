<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>" class="theme-<?= $_SESSION['theme'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
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
