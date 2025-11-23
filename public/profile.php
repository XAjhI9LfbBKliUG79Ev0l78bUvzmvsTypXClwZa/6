<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/controllers/profile_controller.php';
require_once __DIR__ . '/../src/persistence.php';

list($user, $success_message, $error_message) = handle_profile_request();

$page_title = __('profile_title');
$active_page = 'profile';

require_once __DIR__ . '/../src/views/profile.php';
