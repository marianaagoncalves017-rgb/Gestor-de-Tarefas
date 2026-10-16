<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data_final = $_POST['data_final'] ?: NULL;
    $estado = $_POST['estado'] ?? 'Por fazer';
    $projeto_id = $_SESSION['projeto_id'] ?? 1;

    if (!empty($titulo)) {
        $stmt = $pdo->prepare("INSERT INTO TAREFA (TITULO, DESCRICAO, DATAFINAL, ESTADO, IDP) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descricao, $data_final, $estado, $projeto_id]);
        header("Location: list_task.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Tarefa | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, #10b981 0%, #047857 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #ffffff; padding: 40px; border-radius: 28px; width: 100%; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        h2 { color: #047857; font-size: 26px; margin-bottom: 25px; font-weight: 800; text-align: center; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #065f46; font-size: 13px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; }
        input, textarea, select { width: 100%; background: #ecfdf5; border: 2px solid #a7f3d0; padding: 14px; border-radius: 14px; color: #064e3b; font-size: 15px; outline: none; font-weight: 600; }
        input:focus, textarea:focus, select:focus { border-color: #10b981; background: #ffffff; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15); }
        button { width: 100%; padding: 16px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 14px; font-weight: 800; font-size: 16px; cursor: pointer; transition: all 0.2s; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
        button:hover { transform: translateY(-2px); }
        .cancel { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Criar Tarefa 📝</h2>
        <form method="POST">
            <div class="form-group">
                <label>Título *</label>
                <input type="text" name="titulo" required placeholder="Ex: Desenvolver a base de dados">
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" rows="3" placeholder="Detalhes opcionais..."></textarea>
            </div>
            <div class="form-group">
                <label>Data Limite (DATAFINAL)</label>
                <input type="datetime-local" name="data_final">
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="Por fazer" selected>Por fazer</option>
                    <option value="Em curso">Em curso</option>
                    <option value="Terminada">Terminada</option>
                </select>
            </div>
            <button type="submit">Guardar Tarefa</button>
            <a href="list_task.php" class="cancel">Voltar</a>
        </form>
    </div>
</body>
</html>
