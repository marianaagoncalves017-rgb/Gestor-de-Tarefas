<?php
// 1. Iniciar sessão e carregar ligação à base de dados
session_start();
require_once __DIR__ . '/db.php';

// 2. Obter o caminho pedido no URL
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri, PHP_URL_PATH);

// 3. Roteamento simples para servir as páginas do projeto
if (strpos($parsed_url, 'login.php') !== false) {
    require __DIR__ . '/login.php';
    exit();
}

if (strpos($parsed_url, 'student.php') !== false) {
    require __DIR__ . '/student.php';
    exit();
}

if (strpos($parsed_url, 'logout.php') !== false) {
    session_destroy();
    header("Location: /login.php");
    exit();
}

// 4. Se acederes à raiz (/), verifica o tipo de utilizador e redireciona
if (isset($_SESSION['user_id'])) {
    $tipo = $_SESSION['user_tipo'] ?? 'Aluno';
    if ($tipo === 'Professor') {
        // Se for professor, carrega o painel do professor (index.php)
        require __DIR__ . '/index.php';
    } else {
        header("Location: /student.php");
    }
    exit();
}

// 5. Por defeito, abre a página de login
require __DIR__ . '/login.php';
