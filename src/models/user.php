<?php

class User {
    public $login;
    public $password_hash;
    public $email;
    public $lastname;
    public $firstname;
    public $patronymic;
    public $newsletter;
    public $profile_image;
    public $remember_token_hash;
    public $viewed_posts;

    public function __construct($data = []) {
        $this->login = $data['login'] ?? null;
        $this->password_hash = $data['password_hash'] ?? null;
        $this->email = $data['email'] ?? '';
        $this->lastname = $data['lastname'] ?? '';
        $this->firstname = $data['firstname'] ?? '';
        $this->patronymic = $data['patronymic'] ?? '';
        $this->newsletter = $data['newsletter'] ?? '0';
        $this->profile_image = $data['profile_image'] ?? '';
        $this->remember_token_hash = $data['remember_token_hash'] ?? '';
        $this->viewed_posts = $data['viewed_posts'] ?? [];
    }

    public static function find_by_login($login) {
        $users = self::get_all();
        foreach ($users as $user) {
            if ($user->login === $login) {
                return $user;
            }
        }
        return null;
    }

    public static function find_by_token($token) {
        $users = self::get_all();
        foreach ($users as $user) {
            if (!empty($user->remember_token_hash) && password_verify($token, $user->remember_token_hash)) {
                return $user;
            }
        }
        return null;
    }

    public function save() {
        $lines = file_exists(USERS_FILE) ? file(USERS_FILE, FILE_IGNORE_NEW_LINES) : [];
        if ($lines === false) {
            $lines = [];
        }
        $user_found = false;

        $new_line = implode(';', [
            $this->login,
            $this->password_hash,
            $this->email,
            $this->lastname,
            $this->firstname,
            $this->patronymic,
            $this->newsletter,
            $this->profile_image,
            $this->remember_token_hash,
            implode(',', $this->viewed_posts)
        ]);

        foreach ($lines as $i => $line) {
            $data = explode(';', $line);
            if ($data[0] === $this->login) {
                $lines[$i] = $new_line;
                $user_found = true;
                break;
            }
        }

        if (!$user_found) {
            $lines[] = $new_line;
        }
        file_put_contents(USERS_FILE, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
    }

    public static function get_all() {
        if (!file_exists(USERS_FILE)) {
            return [];
        }
        $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $users = [];
        foreach ($lines as $line) {
            $data = explode(';', $line);
            $users[] = new self([
                'login' => $data[0],
                'password_hash' => $data[1],
                'email' => $data[2] ?? '',
                'lastname' => $data[3] ?? '',
                'firstname' => $data[4] ?? '',
                'patronymic' => $data[5] ?? '',
                'newsletter' => $data[6] ?? '0',
                'profile_image' => $data[7] ?? '',
                'remember_token_hash' => $data[8] ?? '',
                'viewed_posts' => isset($data[9]) && $data[9] !== '' ? explode(',', $data[9]) : []
            ]);
        }
        return $users;
    }

    public static function save_all($users) {
        $lines = [];
        foreach ($users as $user) {
            $lines[] = implode(';', [
                $user->login,
                $user->password_hash,
                $user->email,
                $user->lastname,
                $user->firstname,
                $user->patronymic,
                $user->newsletter,
                $user->profile_image,
                $user->remember_token_hash,
                implode(',', $user->viewed_posts)
            ]);
        }
        file_put_contents(USERS_FILE, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
    }
}
