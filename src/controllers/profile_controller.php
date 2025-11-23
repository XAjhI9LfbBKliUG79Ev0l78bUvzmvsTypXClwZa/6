<?php

require_once __DIR__ . '/../models/user.php';

function handle_profile_request() {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    $upload_dir = 'uploads/';
    $user = new User($_SESSION['user']);
    $success_message = '';
    $error_message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $img = $_FILES['profile_image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($img['type'], $allowed_types)) {
                $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid($user->login . '_', true) . '.' . $ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($img['tmp_name'], $destination)) {
                    if (!empty($user->profile_image) && file_exists($user->profile_image)) {
                        unlink($user->profile_image);
                    }
                    $user->profile_image = $destination;
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
                $user->email = trim($_POST['email']);
                $user->lastname = $lastname;
                $user->firstname = $firstname;
                $user->patronymic = $patronymic;
                $user->newsletter = isset($_POST['newsletter']) ? '1' : '0';
                $new_password = trim($_POST['new_password']);
                $new_password_confirm = trim($_POST['new_password_confirm']);

                if (!empty($user->email) && !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    $error_messages[] = 'Неверный формат электронной почты.';
                }

                if (!empty($new_password) && ($new_password !== $new_password_confirm)) {
                    $error_messages[] = 'Новые пароли не совпадают.';
                }

                if (empty($error_messages)) {
                    if (!empty($new_password)) {
                        $user->password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    }

                    $user->save();
                    unset($user->password_hash);
                    $_SESSION['user'] = (array)$user;
                    $success_message = __('profile_updated_success');
                }
            }

            if (!empty($error_messages)) {
                $error_message = implode(' ', $error_messages);
            }
        }
    }

    return [(array)$user, $success_message, $error_message];
}
