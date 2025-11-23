<?php require __DIR__ . '/../templates/header.php'; ?>
<main class="main-container">
    <h1><?= __('profile_header') ?></h1>

    <?php if ($success_message): ?>
        <div class="message success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="message error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
        <div class="profile-image-section">
            <img src="<?= htmlspecialchars($user['profile_image'] ?: 'assets/default-profile.png') ?>" alt="Текущий аватар" class="profile-page-avatar">
            <div class="input-group">
                <label for="profile_image"><?= __('change_profile_image_label') ?></label>
                <input type="file" id="profile_image" name="profile_image" class="file-input">
            </div>
        </div>
        <hr>
        <div class="form-row">
            <div class="input-group">
                <label for="lastname"><?= __('lastname_label') ?></label>
                <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>">
            </div>
            <div class="input-group">
                <label for="firstname"><?= __('firstname_label') ?></label>
                <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>">
            </div>
            <div class="input-group">
                <label for="patronymic"><?= __('patronymic_label') ?></label>
                <input type="text" id="patronymic" name="patronymic" value="<?= htmlspecialchars($user['patronymic'] ?? '') ?>">
            </div>
        </div>
        <div class="input-group">
            <label for="email"><?= __('email_label') ?></label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
        </div>
        <hr>
        <p><strong><?= __('change_password_header') ?></strong></p>
        <div class="form-row">
            <div class="input-group">
                <label for="new_password"><?= __('new_password_label') ?></label>
                <input type="password" id="new_password" name="new_password">
            </div>
            <div class="input-group">
                <label for="new_password_confirm"><?= __('new_password_confirm_label') ?></label>
                <input type="password" id="new_password_confirm" name="new_password_confirm">
            </div>
        </div>
        <hr>
        <div class="input-group checkbox-group">
            <input type="checkbox" id="newsletter" name="newsletter" value="1" <?= ($user['newsletter'] ?? '0') === '1' ? 'checked' : '' ?>>
            <label for="newsletter"><?= __('newsletter_subscribe') ?></label>
        </div>
        <button type="submit"><?= __('save_changes_button') ?></button>
    </form>
</main>
</body>
</html>
