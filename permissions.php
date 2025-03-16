<?php
require_once 'config.php';

function hasPermission($requiredRole) {
    if (!isset($_SESSION['role'])) {
        return false;
    }
    $rolesHierarchy = [
        'user' => 1,
        'manager' => 2,
        'comptable' => 3,
        'admin' => 4
    ];
    return $rolesHierarchy[$_SESSION['role']] >= $rolesHierarchy[$requiredRole];
}
?>
