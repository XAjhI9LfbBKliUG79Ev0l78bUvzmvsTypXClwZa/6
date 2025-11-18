<?php

function get_all_users() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $users = [];
    foreach ($lines as $line) {
        $users[] = explode(';', $line);
    }
    return $users;
}

function find_user_by_login($login) {
    $users = get_all_users();
    foreach ($users as $user_data) {
        if ($user_data[0] === $login) {
            return [
                'login' => $user_data[0],
                'password_hash' => $user_data[1],
                'email' => $user_data[2] ?? '',
                'lastname' => $user_data[3] ?? '',
                'firstname' => $user_data[4] ?? '',
                'patronymic' => $user_data[5] ?? '',
                'newsletter' => $user_data[6] ?? '0',
                'profile_image' => $user_data[7] ?? '',
                'remember_token_hash' => $user_data[8] ?? ''
            ];
        }
    }
    return null;
}

function find_user_by_token($token) {
    $users = get_all_users();
    foreach ($users as $user_data) {
        $token_hash = $user_data[8] ?? '';
        if (!empty($token_hash) && password_verify($token, $token_hash)) {
            return [
                'login' => $user_data[0],
                'password_hash' => $user_data[1],
                'email' => $user_data[2] ?? '',
                'lastname' => $user_data[3] ?? '',
                'firstname' => $user_data[4] ?? '',
                'patronymic' => $user_data[5] ?? '',
                'newsletter' => $user_data[6] ?? '0',
                'profile_image' => $user_data[7] ?? ''
            ];
        }
    }
    return null;
}

function save_users($users) {
    $lines = [];
    foreach ($users as $user_data) {
        $lines[] = implode(';', $user_data);
    }
    file_put_contents(USERS_FILE, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
}

function update_user($login, $updated_data) {
    $users = get_all_users();
    $user_found = false;
    foreach ($users as $i => $user_data) {
        if ($user_data[0] === $login) {
            $users[$i] = [
                $login,
                $updated_data['password_hash'] ?? $user_data[1],
                $updated_data['email'] ?? $user_data[2] ?? '',
                $updated_data['lastname'] ?? $user_data[3] ?? '',
                $updated_data['firstname'] ?? $user_data[4] ?? '',
                $updated_data['patronymic'] ?? $user_data[5] ?? '',
                $updated_data['newsletter'] ?? $user_data[6] ?? '0',
                $updated_data['profile_image'] ?? $user_data[7] ?? '',
                $updated_data['remember_token_hash'] ?? $user_data[8] ?? ''
            ];
            $user_found = true;
            break;
        }
    }
    if ($user_found) {
        save_users($users);
    }
    return $user_found;
}

function add_user($new_user_data) {
    $line = implode(';', [
        $new_user_data['login'],
        $new_user_data['password_hash'],
        $new_user_data['email'] ?? '',
        '', '', '', '0', '', ''
    ]) . PHP_EOL;
    file_put_contents(USERS_FILE, $line, FILE_APPEND | LOCK_EX);
}
