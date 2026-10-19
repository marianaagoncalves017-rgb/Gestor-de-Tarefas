<?php
// Usar o Host do Pooler do Supabase (compatível com IPv4 no Render)
$host     = 'aws-0-eu-central-1.pooler.supabase.com'; // Ou o host exato indicado na tua aba Connection Pooling
$port     = '5432';
$dbname   = 'postgres';
$user     = 'postgres.cnkvkapszqxbclvzvwae';
$password = 'Blackpink In Your Area 08-08-2016'; // Coloca aqui a tua palavra-passe real da base de dados

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}
?>
