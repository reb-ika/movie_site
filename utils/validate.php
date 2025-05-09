<?php

function validate_required($fields, $data) {
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            return "$field is required";
        }
    }
    return true;
}

function validate_length($field, $value, $min, $max) {
    $length = strlen($value);
    if ($length < $min || $length > $max) {
        return "$field must be between $min and $max characters";
    }
    return true;
}

function validate_password_strength($password) {
    if (strlen($password) < 6) {
        return "Password must be at least 6 characters";
    }
    if (!preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        return "Password must include at least one uppercase letter and one number";
    }
    return true;
}
