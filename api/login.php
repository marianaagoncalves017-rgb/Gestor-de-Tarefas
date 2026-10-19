<?php
// Iniciar a sessão para guardar o utilizador autenticado
session_start();

// Incluir o ficheiro de ligação à base de dados (Supabase Session Pooler)
require_once __DIR__ . '/db.php';

// Definir o cabeçalho para resposta JSON (caso seja uma chamada AJAX) ou redirecionamento
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter os dados enviados pelo formulário (suporta 'email'/'EMAIL' e 'password'/'PASSWORD')
    $email    = trim($_POST['email'] ?? $_POST['EMAIL'] ?? '');
    $password = $_POST['password'] ?? $_POST['PASSWORD'] ?? '';

    // Validar se os campos não estão vazios
    if (empty($email) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, preencha o e-mail e a palavra-passe.'
        ]);
        exit;
    }

    try {
        // Procurar o utilizador na tabela 'utilizador' pelo e-mail
        $stmt = $pdo->prepare("SELECT * FROM utilizador WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Identificar a coluna da palavra-passe (senha ou password) na tabela
        $hashNaBD = $user['senha'] ?? $user['password'] ?? null;

        // Verificar se o utilizador existe e se a palavra-passe está correta
        if ($user && $hashNaBD !== null && password_verify($password, $hashNaBD)) {
            // Guardar dados relevantes na sessão PHP
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['nome'] ?? $user['name'] ?? 'Utilizador';
            $_SESSION['user_email'] = $user['email'];

            echo json_encode([
                'success' => true,
                'message' => 'Login efetuado com sucesso!',
                'redirect' => '/index.html' // Redireciona para a página principal
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'E-mail ou palavra-passe incorretos.'
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar o login: ' . $e->getMessage()
        ]);
        exit;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de requisição não suportado.'
    ]);
    exit;
}
?>
