<?php

function handle_feed_request() {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }
}
