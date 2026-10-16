<?php
session_start();
require_once 'db.php';

// Se já estiver logado, encaminha diretamente para a página do seu perfil
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_tipo'] === 'Professor') {
        header("Location: index.php");
    } else {
        header("Location: student.php");
    }
    exit();
}

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Consulta na tabela UTILIZADOR
        $stmt = $pdo->prepare("SELECT IDU, NOME, EMAIL, PASSWORD, TIPO FROM UTILIZADOR WHERE EMAIL = ?");
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if ($u && password_verify($password, $u['PASSWORD'])) {
            // Guarda os dados base na sessão
            $_SESSION['user_id'] = $u['IDU'];
            $_SESSION['user_nome'] = $u['NOME'];
            $_SESSION['user_tipo'] = $u['TIPO'];

            // Procura o ID do Projeto associado ao utilizador
            if ($u['TIPO'] === 'Aluno') {
                $pStmt = $pdo->prepare("SELECT IDP FROM PROJETO WHERE IDAluno = ?");
                $pStmt->execute([$u['IDU']]);
            } else {
                $pStmt = $pdo->prepare("SELECT IDP FROM PROJETO WHERE IDProfessor = ?");
                $pStmt->execute([$u['IDU']]);
            }
            $proj = $pStmt->fetch();
            $_SESSION['projeto_id'] = $proj ? $proj['IDP'] : null;

            // Redirecionamento dinâmico conforme o Perfil
            if ($u['TIPO'] === 'Professor') {
                header("Location: index.php");
            } else {
                header("Location: student.php");
            }
            exit();
        } else {
            $erro = "Email ou palavra-passe incorretos!";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, #4338ca 0%, #7928ca 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #ffffff; padding: 40px; border-radius: 28px; width: 100%; max-width: 420px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: popIn 0.4s ease; }
        @keyframes popIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        h2 { color: #1e1b4b; text-align: center; font-size: 28px; margin-bottom: 25px; font-weight: 800; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #4338ca; font-size: 13px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; }
        input { width: 100%; background: #f1f5f9; border: 2px solid #e2e8f0; padding: 14px 16px; border-radius: 14px; color: #0f172a; font-size: 15px; outline: none; transition: all 0.3s; font-weight: 600; }
        input:focus { border-color: #ff007a; background: #ffffff; box-shadow: 0 0 0 4px rgba(255, 0, 122, 0.15); }
        button { width: 100%; padding: 16px; background: linear-gradient(135deg, #ff007a 0%, #7928ca 100%); color: white; border: none; border-radius: 14px; font-weight: 800; font-size: 16px; cursor: pointer; transition: all 0.3s; box-shadow: 0 8px 20px rgba(255, 0, 122, 0.3); margin-top: 10px; }
        button:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(255, 0, 122, 0.5); }
        .erro { background: #ffe4e6; border: 2px solid #f43f5e; color: #e11d48; padding: 12px; border-radius: 12px; font-size: 14px; text-align: center; margin-bottom: 20px; font-weight: 700; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; font-weight: 600; }
        .footer-link a { color: #ff007a; text-decoration: none; font-weight: 800; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Entrar 🚀</h2>
        <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="seuemail@exemplo.com" required>
            </div>
            <div class="form-group">
                <label>Palavra-passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit">Aceder à Conta</button>
        </form>
        <div class="footer-link">Ainda não tens conta? <a href="registo.php">Regista-te</a></div>
    </div>
</body>
</html>