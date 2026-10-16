<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$projeto_id = $_SESSION['projeto_id'] ?? 1; // ID do Projeto associado

$stmt = $pdo->prepare("SELECT * FROM TAREFA WHERE IDP = ? ORDER BY DATAFINAL ASC");
$stmt->execute([$projeto_id]);
$tarefas = $stmt->fetchAll();

$total = count($tarefas);
$por_fazer = count(array_filter($tarefas, fn($t) => $t['ESTADO'] === 'Por fazer'));
$em_curso = count(array_filter($tarefas, fn($t) => $t['ESTADO'] === 'Em curso'));
$terminadas = count(array_filter($tarefas, fn($t) => $t['ESTADO'] === 'Terminada'));
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
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .user-info h1 { font-size: 32px; font-weight: 800; color: #4338ca; }
        .user-info p { color: #64748b; font-size: 15px; font-weight: 600; margin-top: 2px; }
        
        .actions { display: flex; gap: 12px; }
        .btn { padding: 14px 24px; border-radius: 16px; font-weight: 800; font-size: 15px; text-decoration: none; cursor: pointer; transition: all 0.2s; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
        .btn-add:hover { transform: translateY(-3px); }
        .btn-logout { background: #fee2e2; color: #ef4444; font-weight: 800; }
        .btn-logout:hover { background: #fca5a5; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px; }
        .stat-card { padding: 24px; border-radius: 22px; color: white; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .card-total { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
        .card-porfazer { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .card-emcurso { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); }
        .card-concluida { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
        .stat-number { font-size: 38px; font-weight: 800; margin-top: 5px; }
        .stat-label { font-size: 13px; font-weight: 800; text-transform: uppercase; opacity: 0.9; }

        .controls { display: flex; flex-direction: column; gap: 20px; margin-bottom: 35px; background: #ffffff; padding: 24px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .search-box input { width: 100%; background: #f8fafc; border: 2px solid #e2e8f0; padding: 14px 18px; border-radius: 16px; color: #0f172a; outline: none; font-size: 15px; font-weight: 600; }

        .task-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .task-card { background: #ffffff; border-radius: 24px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-left: 8px solid #6366f1; }

        .task-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .task-title { font-size: 20px; font-weight: 800; color: #0f172a; }
        .task-desc { color: #64748b; font-size: 14px; margin-bottom: 20px; font-weight: 500; }

        .badge { padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .e-Por-fazer { background: #ffedd5; color: #ea580c; }
        .e-Em-curso { background: #e0f2fe; color: #0284c7; }
        .e-Terminada { background: #dcfce7; color: #16a34a; }

        .task-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 2px solid #f1f5f9; }
        .task-date { font-size: 13px; color: #64748b; font-weight: 700; }
        .card-actions { display: flex; gap: 8px; }
        .btn-sm { padding: 8px 14px; border-radius: 10px; font-size: 13px; text-decoration: none; font-weight: 800; border: none; cursor: pointer; }
        .btn-edit { background: #e0e7ff; color: #4338ca; }
        .btn-del { background: #ffe4e6; color: #e11d48; }

        .modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px); justify-content: center; align-items: center; z-index: 100; }
        .modal-content { background: #ffffff; padding: 35px; border-radius: 28px; text-align: center; max-width: 400px; width: 90%; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="user-info">
            <h1>Olá, <?= htmlspecialchars($_SESSION['user_nome']) ?>! ⚡</h1>
            <p>Perfil: <strong><?= htmlspecialchars($_SESSION['user_tipo'] ?? 'Aluno') ?></strong></p>
        </div>
        <div class="actions">
            <a href="add_task.php" class="btn btn-add">+ Nova Tarefa</a>
            <a href="logout.php" class="btn btn-logout">Sair</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card card-total">
            <div class="stat-label">Total</div>
            <div class="stat-number"><?= $total ?></div>
        </div>
        <div class="stat-card card-porfazer">
            <div class="stat-label">Por Fazer</div>
            <div class="stat-number"><?= $por_fazer ?></div>
        </div>
        <div class="stat-card card-emcurso">
            <div class="stat-label">Em Curso</div>
            <div class="stat-number"><?= $em_curso ?></div>
        </div>
        <div class="stat-card card-concluida">
            <div class="stat-label">Terminadas</div>
            <div class="stat-number"><?= $terminadas ?></div>
        </div>
    </div>

    <div class="task-grid">
        <?php foreach ($tarefas as $t): ?>
            <div class="task-card">
                <div>
                    <div class="task-header">
                        <span class="badge e-<?= str_replace(' ', '-', $t['ESTADO']) ?>"><?= htmlspecialchars($t['ESTADO']) ?></span>
                    </div>
                    <div class="task-title"><?= htmlspecialchars($t['TITULO']) ?></div>
                    <div class="task-desc"><?= htmlspecialchars($t['DESCRICAO'] ?? 'Sem descrição.') ?></div>
                </div>
                <div class="task-footer">
                    <div class="task-date">📅 <?= $t['DATAFINAL'] ? date('d/m/Y H:i', strtotime($t['DATAFINAL'])) : 'Sem limite' ?></div>
                    <div class="card-actions">
                        <a href="edit_task.php?id=<?= $t['IDT'] ?>" class="btn-sm btn-edit">Editar</a>
                        <button onclick="confirmDelete(<?= $t['IDT'] ?>)" class="btn-sm btn-del">Eliminar</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal" id="deleteModal">
    <div class="modal-content">
        <h3 style="margin-bottom: 10px; font-size: 22px; color: #ef4444;">Eliminar Tarefa?</h3>
        <p style="color: #64748b; font-size: 15px; margin-bottom: 25px; font-weight: 600;">Tens a certeza que queres apagar esta tarefa?</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <a id="confirmBtn" href="#" class="btn btn-add" style="background: #ef4444;">Sim, Apagar</a>
            <button onclick="closeModal()" class="btn btn-logout" style="background: #f1f5f9; color: #475569;">Cancelar</button>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('confirmBtn').href = 'delete_task.php?id=' + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }
</script>

</body>
</html>