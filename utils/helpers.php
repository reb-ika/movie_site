<?php

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

function current_timestamp() {
    return date('Y-m-d H:i:s');
}
