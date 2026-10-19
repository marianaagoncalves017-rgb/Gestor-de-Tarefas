<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? $_POST['EMAIL'] ?? '');
    $password = $_POST['password'] ?? $_POST['PASSWORD'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM utilizador WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $hashNaBD = $user['senha'] ?? $user['password'] ?? null;

        if ($user && $hashNaBD !== null && password_verify($password, $hashNaBD)) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['nome'] ?? $user['name'] ?? 'Utilizador';
            $_SESSION['user_email'] = $user['email'];

            echo json_encode(['success' => true, 'message' => 'Login efetuado com sucesso!']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'E-mail ou palavra-passe incorretos.']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro na BD: ' . $e->getMessage()]);
        exit;
    }
}
?>
