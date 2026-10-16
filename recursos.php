<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$projeto_id = $_SESSION['projeto_id'] ?? 1;
$user_tipo = $_SESSION['user_tipo'] ?? 'Aluno';

// Inserir novo recurso
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_recurso'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $link = trim($_POST['link'] ?? '');

    if (!empty($titulo) && !empty($link)) {
        $stmt = $pdo->prepare("INSERT INTO RECURSO (TITULO, DESCRICAO, LINK, DATA, IDP) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute([$titulo, $descricao, $link, $projeto_id]);
        header("Location: recursos.php");
        exit();
    }
}

// Procurar recursos do projeto
$stmt = $pdo->prepare("SELECT * FROM RECURSO WHERE IDP = ? ORDER BY DATA DESC");
$stmt->execute([$projeto_id]);
$recursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f0f4f8; color: #1e293b; padding: 40px 20px; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 28px; font-weight: 800; color: #0284c7; }
        .btn-back { padding: 12px 20px; background: #e2e8f0; color: #334155; text-decoration: none; border-radius: 12px; font-weight: 800; }
        .card { background: #ffffff; padding: 28px; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 13px; font-weight: 700; color: #0369a1; margin-bottom: 6px; text-transform: uppercase; }
        input, textarea { width: 100%; padding: 12px; border-radius: 12px; border: 2px solid #bae6fd; background: #f0f9ff; font-weight: 600; outline: none; }
        button { padding: 14px 20px; background: #0284c7; color: white; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; width: 100%; }
        .rec-grid { display: grid; gap: 15px; }
        .rec-item { background: #ffffff; padding: 20px; border-radius: 18px; border-left: 6px solid #10b981; box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
        .rec-title { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 5px; }
        .rec-desc { font-size: 14px; color: #64748b; margin-bottom: 12px; }
        .rec-link { display: inline-block; padding: 8px 14px; background: #d1fae5; color: #047857; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔗 Recursos & Documentação</h1>
        <a href="<?= $user_tipo === 'Professor' ? 'index.php' : 'student.php' ?>" class="btn-back">Voltar</a>
    </div>

    <!-- Formulário de Adicionar Recurso -->
    <div class="card">
        <h3 style="margin-bottom: 15px; color: #0f172a;">Adicionar Novo Recurso</h3>
        <form method="POST">
            <div class="form-group">
                <label>Título do Recurso *</label>
                <input type="text" name="titulo" placeholder="Ex: Tutorial Laravel / Documentação PHP" required>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" rows="2" placeholder="Notas sobre o recurso..."></textarea>
            </div>
            <div class="form-group">
                <label>Link (URL) *</label>
                <input type="url" name="link" placeholder="https://..." required>
            </div>
            <button type="submit" name="add_recurso">+ Guardar Recurso</button>
        </form>
    </div>

    <!-- Lista de Recursos -->
    <h2>Recursos Registados</h2>
    <div class="rec-grid" style="margin-top: 15px;">
        <?php if (empty($recursos)): ?>
            <p style="color: #64748b;">Nenhum recurso adicionado até ao momento.</p>
        <?php else: ?>
            <?php foreach ($recursos as $r): ?>
                <div class="rec-item">
                    <div class="rec-title"><?= htmlspecialchars($r['TITULO']) ?></div>
                    <div class="rec-desc"><?= htmlspecialchars($r['DESCRICAO'] ?? '') ?></div>
                    <a href="<?= htmlspecialchars($r['LINK']) ?>" target="_blank" class="rec-link">🌐 Abrir Link Externo</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>