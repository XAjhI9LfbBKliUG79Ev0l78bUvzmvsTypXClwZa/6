<?php
// router.php

// If the request is for an API endpoint, route it to the API handler.
if (preg_match('/^\/api\//', $_SERVER["REQUEST_URI"])) {
    require_once __DIR__ . '/api/posts.php';
    return true; // Signal that the request has been handled.
}

// For all other requests, return false to let the built-in server handle them.
// This allows the server to correctly serve static files from the /public directory.
return false;
