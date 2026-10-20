<?php
// Ativar exibição de erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/db.php';

// Capturar o caminho pedido no URL
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri, PHP_URL_PATH);

// 1. Encaminhamento para páginas específicas
if (strpos($parsed_url, 'login.php') !== false) {
    require __DIR__ . '/login.php';
    exit();
}

if (strpos($parsed_url, 'registo.php') !== false) {
    require __DIR__ . '/registo.php';
    exit();
}

if (strpos($parsed_url, 'student.php') !== false) {
    require __DIR__ . '/student.php';
    exit();
}

if (strpos($parsed_url, 'recursos.php') !== false) {
    require __DIR__ . '/recursos.php';
    exit();
}

if (strpos($parsed_url, 'add_task.php') !== false) {
    require __DIR__ . '/add_task.php';
    exit();
}

if (strpos($parsed_url, 'edit_task.php') !== false) {
    require __DIR__ . '/edit_task.php';
    exit();
}

if (strpos($parsed_url, 'delete_task.php') !== false) {
    require __DIR__ . '/delete_task.php';
    exit();
}

if (strpos($parsed_url, 'list_task.php') !== false) {
    require __DIR__ . '/list_task.php';
    exit();
}

if (strpos($parsed_url, 'ver_feedback.php') !== false) {
    require __DIR__ . '/ver_feedback.php';
    exit();
}

if (strpos($parsed_url, 'logout.php') !== false) {
    session_destroy();
    header("Location: /login.php");
    exit();
}

// 2. Se acederes à raiz (/) ou se o utilizador já tiver sessão
if (isset($_SESSION['user_id'])) {
    $tipo = $_SESSION['user_tipo'] ?? 'Aluno';
    if ($tipo === 'Aluno') {
        require __DIR__ . '/student.php';
        exit();
    }
}

// Por defeito, abre a página de login
require __DIR__ . '/login.php';
