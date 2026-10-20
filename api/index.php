<?php
// Ativar exibição de erros para facilitar o diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar a sessão PHP
session_start();

// Incluir o ficheiro de ligação à base de dados Supabase
require_once __DIR__ . '/db.php';

// Capturar e tratar o URL pedido
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri, PHP_URL_PATH);

// -------------------------------------------------------------
// ROTEAMENTO DAS PÁGINAS DO PROJECTO
// -------------------------------------------------------------

// Página de Login
if (strpos($parsed_url, 'login.php') !== false) {
    require __DIR__ . '/login.php';
    exit();
}

// Página de Registo
if (strpos($parsed_url, 'registo.php') !== false) {
    require __DIR__ . '/registo.php';
    exit();
}

// Dashboard do Aluno
if (strpos($parsed_url, 'student.php') !== false) {
    require __DIR__ . '/student.php';
    exit();
}

// Recursos / Ficheiros
if (strpos($parsed_url, 'recursos.php') !== false) {
    require __DIR__ . '/recursos.php';
    exit();
}

// Ações de Tarefas
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

// Terminar Sessão (Logout)
if (strpos($parsed_url, 'logout.php') !== false) {
    session_destroy();
    header("Location: /login.php");
    exit();
}

// -------------------------------------------------------------
// ROTA PRINCIPAL / RAIZ (/)
// -------------------------------------------------------------

// Se o utilizador já estiver autenticado
if (isset($_SESSION['user_id'])) {
    $tipo = $_SESSION['user_tipo'] ?? 'Aluno';
    
    if ($tipo === 'Professor') {
        // Se for professor, carrega a vista principal
        require __DIR__ . '/index.php';
    } else {
        // Se for aluno, redireciona para a vista do aluno
        header("Location: /student.php");
    }
    exit();
}

// Se não estiver com sessão iniciada, abre por padrão o Login
require __DIR__ . '/login.php';
