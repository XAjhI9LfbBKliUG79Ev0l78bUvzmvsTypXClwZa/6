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
