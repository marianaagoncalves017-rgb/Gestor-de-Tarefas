<?php
// Captura o caminho pedido na URL
$request_uri = $_SERVER['REQUEST_URI'];

// Se pedir diretamente login.php, carrega o login
if (strpos($request_uri, 'login.php') !== false) {
    require __DIR__ . '/login.php';
    exit();
}

// Se pedir student.php, carrega o student
if (strpos($request_uri, 'student.php') !== false) {
    require __DIR__ . '/student.php';
    exit();
}

// Por defeito, abre o login.php
require __DIR__ . '/login.php';
