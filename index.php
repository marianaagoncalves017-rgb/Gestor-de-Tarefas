<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_nome = $_SESSION['user_nome'];
$user_tipo = $_SESSION['user_tipo'] ?? 'Aluno';

if ($user_tipo === 'Aluno') {
    header("Location: student.php");
    exit();
}

$msg_status = "";

// 1. Processar Ação: Assumir Orientação do Projeto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['assumir_projeto'])) {
    $idp = intval($_POST['idp'] ?? 0);
    if ($idp > 0) {
        $stmt = $pdo->prepare("UPDATE PROJETO SET IDProfessor = ? WHERE IDP = ?");
        $stmt->execute([$user_id, $idp]);
        $msg_status = "Assumiu a orientação deste projeto com sucesso!";
    }
}

// 2. Processar Ação: Remover-se da Orientação do Projeto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remover_orientacao'])) {
    $idp = intval($_POST['idp'] ?? 0);
    if ($idp > 0) {
        $stmt = $pdo->prepare("UPDATE PROJETO SET IDProfessor = NULL WHERE IDP = ? AND IDProfessor = ?");
        $stmt->execute([$idp, $user_id]);
        $msg_status = "Deixou de orientar o projeto selecionado.";
    }
}

// 3. Processar Ação: Enviar Feedback
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enviar_feedback'])) {
    $comentario = trim($_POST['comentario'] ?? '');
    $idp_projeto = intval($_POST['idp_projeto'] ?? 0);

    if (!empty($comentario) && $idp_projeto > 0) {
        $stmt_fb = $pdo->prepare("INSERT INTO FEEDBACK (COMENTARIO, DATA, IDP, IDProfessor) VALUES (?, NOW(), ?, ?)");
        $stmt_fb->execute([$comentario, $idp_projeto, $user_id]);
        $msg_status = "Feedback enviado com sucesso!";
    }
}

// Consultar Projetos que O PROFESSOR ATUAL ORIENTA
$stmt_meus = $pdo->prepare("
    SELECT p.*, u.NOME as NomeAluno 
    FROM PROJETO p 
    JOIN UTILIZADOR u ON p.IDAluno = u.IDU 
    WHERE p.IDProfessor = ? 
    ORDER BY u.NOME ASC
");
$stmt_meus->execute([$user_id]);
$meus_projetos = $stmt_meus->fetchAll();

// Consultar Projetos SEM ORIENTADOR ATRIBUÍDO (IDProfessor IS NULL)
$stmt_livres = $pdo->prepare("
    SELECT p.*, u.NOME as NomeAluno 
    FROM PROJETO p 
    JOIN UTILIZADOR u ON p.IDAluno = u.IDU 
    WHERE p.IDProfessor IS NULL 
    ORDER BY u.NOME ASC
");
$stmt_livres->execute();
$projetos_livres = $stmt_livres->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor | ProjectFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f0f4f8; color: #1e293b; padding: 40px 20px; min-height: 100vh; }
        .container { max-width: 1100px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .user-info h1 { font-size: 30px; font-weight: 800; color: #1e1b4b; }
        .badge-prof { background: #7928ca; color: white; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 800; }
        .btn-logout { padding: 12px 20px; background: #fee2e2; color: #ef4444; font-weight: 800; text-decoration: none; border-radius: 14px; }

        .alert-info { background: #dcfce7; color: #15803d; padding: 14px; border-radius: 14px; font-weight: 700; margin-bottom: 25px; text-align: center; }

        .section-title { font-size: 22px; font-weight: 800; color: #334155; margin: 30px 0 15px; }

        .grid-projects { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px; }
        .project-card { background: #ffffff; border-radius: 24px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.04); border-top: 6px solid #7928ca; display: flex; flex-direction: column; justify-content: space-between; }
        .project-card.free { border-top-color: #f59e0b; }

        .aluno-name { font-size: 13px; font-weight: 800; color: #7928ca; text-transform: uppercase; }
        .project-title { font-size: 18px; font-weight: 800; color: #0f172a; margin: 6px 0; }
        .project-area { font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 15px; }

        /* Botões de Ação */
        .btn-action { width: 100%; padding: 12px; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer; border: none; margin-top: 10px; transition: all 0.2s; }
        .btn-join { background: #10b981; color: white; }
        .btn-leave { background: #fee2e2; color: #ef4444; }
        .btn-leave:hover { background: #fca5a5; }

        .feedback-box { background: #f8fafc; padding: 15px; border-radius: 16px; border: 2px solid #e2e8f0; margin-top: 15px; }
        .feedback-box textarea { width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #cbd5e1; outline: none; margin-bottom: 8px; font-size: 13px; }
        .btn-fb { background: #7928ca; color: white; width: 100%; padding: 10px; border-radius: 10px; font-weight: 800; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="user-info">
            <h1>Olá, Prof. <?= htmlspecialchars($user_nome) ?> <span class="badge-prof">Professor</span></h1>
            <p style="color: #64748b; font-weight: 600;">Gestão e Orientação de Projetos PAP</p>
        </div>
        <a href="logout.php" class="btn-logout">Sair</a>
    </div>

    <?php if ($msg_status): ?>
        <div class="alert-info">✨ <?= htmlspecialchars($msg_status) ?></div>
    <?php endif; ?>

    <!-- 1. PROJETOS EM ORIENTAÇÃO PELO PROFESSOR -->
    <h2 class="section-title">📌 Os Meus Projetos em Orientação (<?= count($meus_projetos) ?>)</h2>
    <?php if (empty($meus_projetos)): ?>
        <p style="color: #64748b; font-weight: 600;">Ainda não está a orientar nenhum projeto.</p>
    <?php else: ?>
        <div class="grid-projects">
            <?php foreach ($meus_projetos as $p): ?>
                <div class="project-card">
                    <div>
                        <div class="aluno-name">👤 Aluno: <?= htmlspecialchars($p['NomeAluno']) ?></div>
                        <div class="project-title"><?= htmlspecialchars($p['TEMA']) ?></div>
                        <div class="project-area">📌 Área: <?= htmlspecialchars($p['AREA']) ?></div>
                    </div>

                    <!-- Formulário de Feedback -->
                    <div class="feedback-box">
                        <form method="POST">
                            <input type="hidden" name="idp_projeto" value="<?= $p['IDP'] ?>">
                            <textarea name="comentario" rows="2" placeholder="Escrever feedback..." required></textarea>
                            <button type="submit" name="enviar_feedback" class="btn-fb">Enviar Feedback</button>
                        </form>
                    </div>

                    <!-- Botão para Remover Orientação -->
                    <form method="POST" onsubmit="return confirm('Tem a certeza que deseja deixar de orientar este projeto?');">
                        <input type="hidden" name="idp" value="<?= $p['IDP'] ?>">
                        <button type="submit" name="remover_orientacao" class="btn-action btn-leave">Deixar de Orientar Projeto</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 2. PROJETOS DISPONÍVEIS (SEM PROFESSOR) -->
    <h2 class="section-title">🔍 Projetos Sem Orientador Atribuído (<?= count($projetos_livres) ?>)</h2>
    <?php if (empty($projetos_livres)): ?>
        <p style="color: #64748b; font-weight: 600;">Todos os projetos têm atualmente um orientador atribuído.</p>
    <?php else: ?>
        <div class="grid-projects">
            <?php foreach ($projetos_livres as $pl): ?>
                <div class="project-card free">
                    <div>
                        <div class="aluno-name" style="color: #d97706;">👤 Aluno: <?= htmlspecialchars($pl['NomeAluno']) ?></div>
                        <div class="project-title"><?= htmlspecialchars($pl['TEMA']) ?></div>
                        <div class="project-area">📌 Área: <?= htmlspecialchars($pl['AREA']) ?></div>
                    </div>

                    <!-- Botão para Assumir Orientação -->
                    <form method="POST">
                        <input type="hidden" name="idp" value="<?= $pl['IDP'] ?>">
                        <button type="submit" name="assumir_projeto" class="btn-action btn-join">+ Assumir Orientação</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>