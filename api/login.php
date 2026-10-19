<?php
// Iniciar a sessão para guardar o utilizador autenticado
session_start();

// Incluir o ficheiro de ligação à base de dados
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter os dados enviados pelo formulário (suporta 'email'/'EMAIL' e 'password'/'PASSWORD')
    $email    = trim($_POST['email'] ?? $_POST['EMAIL'] ?? '');
    $password = $_POST['password'] ?? $_POST['PASSWORD'] ?? '';

    // Validar se os campos foram preenchidos
    if (empty($email) || empty($password)) {
        die("Por favor, preencha o e-mail e a palavra-passe.");
    }

    try {
        // Procurar o utilizador na tabela 'utilizador' pelo e-mail
        $stmt = $pdo->prepare("SELECT * FROM utilizador WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Identificar a coluna da palavra-passe na tabela (senha ou password)
        $hashNaBD = $user['senha'] ?? $user['password'] ?? null;

        // Verificar se o utilizador existe e se a palavra-passe está correta
        if ($user && $hashNaBD !== null && password_verify($password, $hashNaBD)) {
            // Guardar dados do utilizador na sessão PHP
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['nome'] ?? $user['name'] ?? 'Utilizador';
            $_SESSION['user_email'] = $user['email'];

            // Se for uma requisição AJAX (JavaScript), devolve JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => true, 'redirect' => '/dashboard.html']);
                exit;
            }

            // Redirecionamento padrão para formulário HTML
            header("Location: /dashboard.html");
            exit;
        } else {
            die("E-mail ou palavra-passe incorretos.");
        }
    } catch (PDOException $e) {
        die("Erro ao processar o login: " . $e->getMessage());
    }
} else {
    // Resposta caso o ficheiro seja acedido diretamente via GET no browser
    header('Content-Type: text/html; charset=utf-8');
    echo "<h2>Acesso Direto Não Permitido</h2>";
    echo "<p>Por favor, utilize o formulário de login em <a href='/'>gestor-de-tarefas</a> para aceder à sua conta.</p>";
    exit;
}
?>
