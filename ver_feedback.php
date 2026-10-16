<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$projeto_id = $_SESSION['projeto_id'] ?? 1;

// Consulta os feedbacks e o nome do professor que o escreveu
$stmt = $pdo->prepare("
    SELECT f.*, u.NOME as NomeProf 
    FROM FEEDBACK f 
    JOIN UTILIZADOR u ON f.IDProfessor = u.IDU 
    WHERE f.IDP = ? 
    ORDER BY f.DATA DESC
");
$stmt->execute([$projeto_id]);
$feedbacks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback do Professor | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f0f4f8; color: #1e293b; padding: 40px 20px; min-height: 100vh; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 28px; font-weight: 800; color: #7928ca; }
        .btn-back { padding: 12px 20px; background: #e2e8f0; color: #334155; text-decoration: none; border-radius: 12px; font-weight: 800; }
        .feedback-card { background: #ffffff; padding: 25px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.04); margin-bottom: 20px; border-left: 6px solid #7928ca; }
        .prof-info { display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; color: #7928ca; margin-bottom: 10px; }
        .comentario { font-size: 15px; color: #334155; line-height: 1.6; font-weight: 500; }
        .data { font-size: 12px; color: #94a3b8; font-weight: 600; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>💬 Feedback do Orientador</h1>
        <a href="student.php" class="btn-back">Voltar</a>
    </div>

    <?php if (empty($feedbacks)): ?>
        <div style="background: white; padding: 30px; border-radius: 20px; text-align: center; color: #64748b;">
            Ainda não recebeste nenhum feedback do teu professor orientador.
        </div>
    <?php else: ?>
        <?php foreach ($feedbacks as $f): ?>
            <div class="feedback-card">
                <div class="prof-info">
                    <span>👨‍🏫 <?= htmlspecialchars($f['NomeProf']) ?></span>
                    <span class="data">📅 <?= date('d/m/Y H:i', strtotime($f['DATA'])) ?></span>
                </div>
                <div class="comentario"><?= nl2br(htmlspecialchars($f['COMENTARIO'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>