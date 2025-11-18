<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/persistence.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$upload_dir = 'uploads/';
$user = $_SESSION['user'];
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_user_data = find_user_by_login($user['login']);
    $updated_data = $current_user_data;

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $img = $_FILES['profile_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($img['type'], $allowed_types)) {
            $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid($user['login'] . '_', true) . '.' . $ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($img['tmp_name'], $destination)) {
                if (!empty($updated_data['profile_image']) && file_exists($updated_data['profile_image'])) {
                    unlink($updated_data['profile_image']);
                }
                $updated_data['profile_image'] = $destination;
            } else {
                $error_message = 'Не удалось загрузить изображение.';
            }
        } else {
            $error_message = 'Недопустимый формат файла. Разрешены JPG, PNG, GIF.';
        }
    }

    if (empty($error_message)) {
	    $lastname = trim($_POST['lastname']);
	    $firstname = trim($_POST['firstname']);
	    $patronymic = trim($_POST['patronymic']);
	    $cyrillic_pattern = '/^[\p{Cyrillic}\s-]+$/u';
	    $latin_pattern = '/^[\p{Latin}\s-]+$/u';
	    $error_messages = [];

	    if (empty($lastname)) {
		    $error_messages[] = 'Фамилия не должна быть пустой.';
	    }

	    if (empty($firstname)) {
		    $error_messages[] = 'Имя не должно быть пустым.';
	    }

	    if (empty($patronymic)) {
		    $error_messages[] = 'Отчество не должно быть пустым.';
	    }

	    if (empty($error_messages)) {
		    $updated_data['email'] = trim($_POST['email']);
		    $updated_data['lastname'] = $lastname;
		    $updated_data['firstname'] = $firstname;
		    $updated_data['patronymic'] = $patronymic;
		    $updated_data['newsletter'] = isset($_POST['newsletter']) ? '1' : '0';
		    $new_password = trim($_POST['new_password']);
		    $new_password_confirm = trim($_POST['new_password_confirm']);

		    if (!empty($updated_data['email']) && !filter_var($updated_data['email'], FILTER_VALIDATE_EMAIL)) {
			    $error_messages[] = 'Неверный формат электронной почты.';
		    }

		    if (!empty($new_password) && ($new_password !== $new_password_confirm)) {
			    $error_messages[] = 'Новые пароли не совпадают.';
		    }

		    if (empty($error_messages)) {
			    if (!empty($new_password)) {
				    $updated_data['password_hash'] = password_hash($new_password, PASSWORD_DEFAULT);
			    }

			    if (update_user($user['login'], $updated_data)) {
				    unset($updated_data['password_hash']);
				    $_SESSION['user'] = $updated_data;
				    $user = $_SESSION['user'];
				    $success_message = __('profile_updated_success');
			    }
		    }
	    }

	    if (!empty($error_messages)) {
		    $error_message = implode(' ', $error_messages);
	    }
    }
}

$page_title = __('profile_title');
$active_page = 'profile';
require __DIR__ . '/../src/templates/header.php';
?>
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
