<?php

require_once __DIR__ . '/../models/user.php';

function handle_login_request() {
    $error_message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($login) || empty($password)) {
            $error_message = 'Логин и пароль не могут быть пустыми.';
        } else {
            $user = User::find_by_login($login);
            if ($user && password_verify($password, $user->password_hash)) {
                unset($user->password_hash);
                $_SESSION['user'] = (array)$user;
                setcookie('last_activity', time(), time() + INACTIVITY_TIMEOUT, '/');
                setcookie('last_visited', date('Y-m-d H:i:s'), time() + 3600 * 24 * 365, '/');

                if (isset($_POST['remember_me'])) {
                    $token = bin2hex(random_bytes(32));
                    $user->remember_token_hash = password_hash($token, PASSWORD_DEFAULT);
                    $user->save();
                    setcookie('remember_me', $token, time() + 3600 * 24 * 30, '/');
                }

                if ($user->login === 'admin') {
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
    return $error_message;
}

function handle_register_request() {
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
        } elseif (User::find_by_login($login)) {
            $error_message = 'Пользователь с таким логином уже существует!';
        } else {
            $user = new User([
                'login' => $login,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'email' => $email
            ]);
            $user->save();

            $_SESSION['success_message'] = "Поздравляем, регистрация прошла успешно! Теперь вы можете войти.";
            $_SESSION['show_profile_prompt'] = true;
            header('Location: index.php');
            exit();
        }
    }
    return $error_message;
}
