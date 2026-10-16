<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: list_task.php");
    exit();
}

$erro = ""; $sucesso = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $tipo = $_POST['tipo'] ?? 'Aluno';

    if (!empty($nome) && !empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT IDU FROM UTILIZADOR WHERE EMAIL = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = "Este email já se encontra registado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO UTILIZADOR (NOME, EMAIL, PASSWORD, TIPO) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nome, $email, $hash, $tipo])) {
                $sucesso = "Conta criada com sucesso! Redirecionando...";
                header("refresh:2;url=login.php");
            }
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #ffffff; padding: 40px; border-radius: 28px; width: 100%; max-width: 420px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        h2 { color: #0284c7; text-align: center; font-size: 28px; margin-bottom: 25px; font-weight: 800; }
        .form-group { margin-bottom: 18px; }
        label { display: block; color: #0369a1; font-size: 13px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; }
        input, select { width: 100%; background: #f0f9ff; border: 2px solid #bae6fd; padding: 14px 16px; border-radius: 14px; color: #0c4a6e; font-size: 15px; outline: none; font-weight: 600; }
        input:focus, select:focus { border-color: #0284c7; background: #ffffff; box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15); }
        button { width: 100%; padding: 16px; background: linear-gradient(135deg, #00f2fe 0%, #0284c7 100%); color: white; border: none; border-radius: 14px; font-weight: 800; font-size: 16px; cursor: pointer; transition: all 0.3s; box-shadow: 0 8px 20px rgba(2, 132, 199, 0.3); margin-top: 10px; }
        button:hover { transform: translateY(-2px); }
        .erro { background: #ffe4e6; color: #e11d48; padding: 12px; border-radius: 12px; font-size: 14px; text-align: center; margin-bottom: 15px; font-weight: 700; }
        .sucesso { background: #dcfce7; color: #15803d; padding: 12px; border-radius: 12px; font-size: 14px; text-align: center; margin-bottom: 15px; font-weight: 700; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; font-weight: 600; }
        .footer-link a { color: #0284c7; text-decoration: none; font-weight: 800; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Criar Conta ✨</h2>
        <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div class="sucesso"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" placeholder="O seu nome" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="seuemail@exemplo.com" required>
            </div>
            <div class="form-group">
                <label>Palavra-passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Tipo de Utilizador</label>
                <select name="tipo">
                    <option value="Aluno" selected>Aluno</option>
                    <option value="Professor">Professor</option>
                </select>
            </div>
            <button type="submit">Criar Registo</button>
        </form>
        <div class="footer-link">Já tens conta? <a href="login.php">Entra aqui</a></div>
    </div>
</body>
</html>