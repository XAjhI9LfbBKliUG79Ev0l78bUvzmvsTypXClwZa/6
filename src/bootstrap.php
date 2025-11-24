<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers/session.php';

Session::start();
Session::manage();
Session::set_defaults();

require_once __DIR__ . '/i18n.php';
