<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$projeto_id = $_SESSION['projeto_id'] ?? 1;
$task_id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM TAREFA WHERE IDT = ? AND IDP = ?");
$stmt->execute([$task_id, $projeto_id]);
$tarefa = $stmt->fetch();

if (!$tarefa) {
    header("Location: list_task.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data_final = $_POST['data_final'] ?: NULL;
    $estado = $_POST['estado'] ?? 'Por fazer';

    if (!empty($titulo)) {
        $update = $pdo->prepare("UPDATE TAREFA SET TITULO = ?, DESCRICAO = ?, DATAFINAL = ?, ESTADO = ? WHERE IDT = ? AND IDP = ?");
        $update->execute([$titulo, $descricao, $data_final, $estado, $task_id, $projeto_id]);
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
    <title>Editar Tarefa | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #ffffff; padding: 40px; border-radius: 28px; width: 100%; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        h2 { color: #4338ca; font-size: 26px; margin-bottom: 25px; font-weight: 800; text-align: center; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #3730a3; font-size: 13px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; }
        input, textarea, select { width: 100%; background: #e0e7ff; border: 2px solid #c7d2fe; padding: 14px; border-radius: 14px; color: #1e1b4b; font-size: 15px; outline: none; font-weight: 600; }
        input:focus, textarea:focus, select:focus { border-color: #6366f1; background: #ffffff; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }
        button { width: 100%; padding: 16px; background: linear-gradient(135deg, #6366f1, #4338ca); color: white; border: none; border-radius: 14px; font-weight: 800; font-size: 16px; cursor: pointer; transition: all 0.2s; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); }
        button:hover { transform: translateY(-2px); }
        .cancel { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Editar Tarefa ✏️</h2>
        <form method="POST">
            <div class="form-group">
                <label>Título *</label>
                <input type="text" name="titulo" value="<?= htmlspecialchars($tarefa['TITULO']) ?>" required>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" rows="3"><?= htmlspecialchars($tarefa['DESCRICAO'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Data Limite (DATAFINAL)</label>
                <input type="datetime-local" name="data_final" value="<?= $tarefa['DATAFINAL'] ? date('Y-m-d\TH:i', strtotime($tarefa['DATAFINAL'])) : '' ?>">
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="Por fazer" <?= $tarefa['ESTADO'] === 'Por fazer' ? 'selected' : '' ?>>Por fazer</option>
                    <option value="Em curso" <?= $tarefa['ESTADO'] === 'Em curso' ? 'selected' : '' ?>>Em curso</option>
                    <option value="Terminada" <?= $tarefa['ESTADO'] === 'Terminada' ? 'selected' : '' ?>>Terminada</option>
                </select>
            </div>
            <button type="submit">Atualizar Tarefa</button>
            <a href="list_task.php" class="cancel">Voltar</a>
        </form>
    </div>
</body>
</html>
