<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/controllers/admin_controller.php';

handle_admin_request();

$page_title = __('admin_title');
$active_page = 'admin';
$current_view = $_GET['view'] ?? 'session';

require_once __DIR__ . '/../src/views/admin.php';
