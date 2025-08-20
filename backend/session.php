<?php
// Extend session lifetime to 7 days
$lifetime = 60 * 60 * 24 * 7; // 7 days in seconds
session_set_cookie_params([
    "lifetime" => $lifetime,
    "path" => "/",
    "domain" => "", // keep default
    "secure" => false, // set true if using HTTPS
    "httponly" => true,
    "samesite" => "Lax"
]);

session_start();
// Check if the session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}