<?php
// Base pieces
$base_url = "/keep-my-pet/";  // For HTML links
$base_dir = __DIR__ . "/../../";  // For PHP includes

// Call logout controller
include $base_dir . 'app/Controller/LogoutController.php';
LogoutController::handle($base_dir, $base_url);
