<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_nome = $_SESSION['user_nome'];
$projeto_id = $_SESSION['projeto_id'] ?? 1;

// Processar envio de novo Recurso
$msg_sucesso = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_recurso'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $link = trim($_POST['link'] ?? '');

    if (!empty($titulo) && !empty($link)) {
        $stmt = $pdo->prepare("INSERT INTO RECURSO (TITULO, DESCRICAO, LINK, DATA, IDP) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute([$titulo, $descricao, $link, $projeto_id]);
        $msg_sucesso = "Recurso adicionado com sucesso!";
    }
}

// 1. Procurar estatísticas das tarefas
$tStmt = $pdo->prepare("SELECT * FROM TAREFA WHERE IDP = ?");
$tStmt->execute([$projeto_id]);
$tarefas = $tStmt->fetchAll();

$total = count($tarefas);
$por_fazer = count(array_filter($tarefas, fn($t) => $t['ESTADO'] === 'Por fazer'));
$em_curso = count(array_filter($tarefas, fn($t) => $t['ESTADO'] === 'Em curso'));
$terminadas = count(array_filter($tarefas, fn($t) => $t['ESTADO'] === 'Terminada'));

// 2. Procurar os Feedbacks do Professor
$fStmt = $pdo->prepare("
    SELECT f.*, u.NOME as NomeProf 
    FROM FEEDBACK f 
    JOIN UTILIZADOR u ON f.IDProfessor = u.IDU 
    WHERE f.IDP = ? 
    ORDER BY f.DATA DESC
");
$fStmt->execute([$projeto_id]);
$feedbacks = $fStmt->fetchAll();

// 3. Procurar Recursos do projeto
$rStmt = $pdo->prepare("SELECT * FROM RECURSO WHERE IDP = ? ORDER BY DATA DESC");
$rStmt->execute([$projeto_id]);
$recursos = $rStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarefas | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f0f4f8; color: #1e293b; padding: 40px 20px; min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        /* Cabeçalho exatamente igual ao da imagem */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .user-info h1 { font-size: 32px; font-weight: 800; color: #4338ca; }
        .user-info p { color: #64748b; font-size: 15px; font-weight: 600; margin-top: 2px; }
        
        .actions { display: flex; gap: 12px; }
        .btn { padding: 14px 24px; border-radius: 16px; font-weight: 800; font-size: 15px; text-decoration: none; cursor: pointer; transition: all 0.2s; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add { background: #10b981; color: white; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
        .btn-add:hover { transform: translateY(-2px); }
        .btn-logout { background: #fee2e2; color: #ef4444; font-weight: 800; }
        .btn-logout:hover { background: #fca5a5; }

        /* Dashboard de Métricas igual à imagem */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px; }
        .stat-card { padding: 24px; border-radius: 22px; color: white; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .card-total { background: #6366f1; }
        .card-porfazer { background: #f59e0b; }
        .card-emcurso { background: #0284c7; }
        .card-concluida { background: #10b981; }
        .stat-number { font-size: 38px; font-weight: 800; margin-top: 5px; }
        .stat-label { font-size: 13px; font-weight: 800; text-transform: uppercase; opacity: 0.9; }

        /* Estrutura de Duas Colunas para Feedback e Recursos */
        .content-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 30px; margin-top: 20px; }
        
        .section-box { background: #ffffff; padding: 30px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .section-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }

        /* Estilos dos Feedbacks */
        .feedback-item { background: #f8fafc; border-left: 5px solid #7928ca; padding: 18px; border-radius: 16px; margin-bottom: 15px; }
        .fb-prof { font-size: 13px; font-weight: 800; color: #7928ca; margin-bottom: 4px; display: flex; justify-content: space-between; }
        .fb-text { font-size: 14px; color: #334155; font-weight: 600; line-height: 1.5; }

        /* Estilos do Formulário de Recurso */
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 12px; font-weight: 800; color: #0284c7; margin-bottom: 6px; text-transform: uppercase; }
        input, textarea { width: 100%; padding: 12px 14px; border-radius: 12px; border: 2px solid #e2e8f0; background: #f8fafc; font-weight: 600; outline: none; font-size: 14px; }
        input:focus, textarea:focus { border-color: #0284c7; background: #ffffff; }
        .btn-rec { background: #0284c7; color: white; width: 100%; padding: 14px; border-radius: 12px; font-weight: 800; border: none; cursor: pointer; }
        
        .rec-item { background: #f0fdf4; border-left: 5px solid #10b981; padding: 14px; border-radius: 14px; margin-top: 10px; }
        .rec-title { font-size: 14px; font-weight: 800; color: #065f46; }
        .rec-link { font-size: 12px; color: #0284c7; text-decoration: none; font-weight: 700; display: inline-block; margin-top: 4px; }
    </style>
</head>
<body>

<div class="container">
    <!-- Cabeçalho (Fiel à imagem) -->
    <div class="header">
        <div class="user-info">
            <h1>Olá, <?= htmlspecialchars($user_nome) ?>! ⚡</h1>
            <p>Perfil: <strong>Aluno</strong></p>
        </div>
        <div class="actions">
            <a href="add_task.php" class="btn btn-add">+ Nova Tarefa</a>
            <a href="logout.php" class="btn btn-logout">Sair</a>
        </div>
    </div>

    <!-- Cartões de Métricas (Fiel à imagem) -->
    <div class="stats-grid">
        <div class="stat-card card-total">
            <div class="stat-label">TOTAL</div>
            <div class="stat-number"><?= $total ?></div>
        </div>
        <div class="stat-card card-porfazer">
            <div class="stat-label">POR FAZER</div>
            <div class="stat-number"><?= $por_fazer ?></div>
        </div>
        <div class="stat-card card-emcurso">
            <div class="stat-label">EM CURSO</div>
            <div class="stat-number"><?= $em_curso ?></div>
        </div>
        <div class="stat-card card-concluida">
            <div class="stat-label">TERMINADAS</div>
            <div class="stat-number"><?= $terminadas ?></div>
        </div>
    </div>

    <!-- Novas Secções: Feedback e Recursos -->
    <div class="content-grid">
        
        <!-- Caixa 1: Feedback do Professor -->
        <div class="section-box">
            <div class="section-title">💬 Feedbacks do Orientador</div>
            <?php if (empty($feedbacks)): ?>
                <p style="color: #64748b; font-size: 14px; font-weight: 600;">Nenhum feedback recebido até ao momento.</p>
            <?php else: ?>
                <?php foreach ($feedbacks as $fb): ?>
                    <div class="feedback-item">
                        <div class="fb-prof">
                            <span>👨‍🏫 <?= htmlspecialchars($fb['NomeProf']) ?></span>
                            <span>📅 <?= date('d/m/Y H:i', strtotime($fb['DATA'])) ?></span>
                        </div>
                        <div class="fb-text"><?= nl2br(htmlspecialchars($fb['COMENTARIO'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Caixa 2: Adicionar e Listar Recursos -->
        <div class="section-box">
            <div class="section-title">🔗 Adicionar Recurso do Projeto</div>
            
            <?php if ($msg_sucesso): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 10px; border-radius: 10px; margin-bottom: 15px; font-weight: 700; font-size: 13px;">
                    <?= $msg_sucesso ?>
                </div>
            <?php endif; ?>

            <form method="POST" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label>Título do Recurso *</label>
                    <input type="text" name="titulo" placeholder="Ex: Documentação / Tutorial" required>
                </div>
                <div class="form-group">
                    <label>Link (URL) *</label>
                    <input type="url" name="link" placeholder="https://..." required>
                </div>
                <div class="form-group">
                    <label>Descrição (opcional)</label>
                    <textarea name="descricao" rows="2" placeholder="Breve nota sobre o recurso..."></textarea>
                </div>
                <button type="submit" name="add_recurso" class="btn-rec">+ Guardar Recurso</button>
            </form>

            <h4 style="font-size: 14px; font-weight: 800; color: #475569; margin-bottom: 10px;">Recursos Guardados:</h4>
            <?php if (empty($recursos)): ?>
                <p style="color: #64748b; font-size: 13px; font-weight: 600;">Sem recursos adicionados.</p>
            <?php else: ?>
                <?php foreach ($recursos as $r): ?>
                    <div class="rec-item">
                        <div class="rec-title"><?= htmlspecialchars($r['TITULO']) ?></div>
                        <a href="<?= htmlspecialchars($r['LINK']) ?>" target="_blank" class="rec-link">🌐 Abrir Link</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
