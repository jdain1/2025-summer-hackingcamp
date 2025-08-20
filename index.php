<?php
session_start();

$appUser = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';

// DB 연결 (PDO)
$dbHost = 'localhost';
$dbName = 'sqli_test';
$dbUser = 'root';
$dbPass = '1110';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log("DB 연결 실패: " . $e->getMessage());
    die('DB 연결 실패');
}

$stmt = $pdo->prepare('SELECT game_speed, role FROM user_table WHERE uid = ? LIMIT 1');
$stmt->execute([$appUser]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$gameSpeed = $row ? (int)$row['game_speed'] : 10;
$userRole  = $row ? $row['role'] : 'guest';

$uploadedIcon = file_exists(__DIR__ . '/uploads/icon.png') ? '/uploads/icon.png' : null;
?>
<!doctype html>
<html lang="ko">
<head>
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/menu.css">
  <meta charset="utf-8">
  <title>Guest Game</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body { font-family: Inter, Arial, sans-serif; background:#f6f7fb; margin:0; height:100vh; display:flex; align-items:center; justify-content:center; }
  .wrap { padding:40px; width:920px; max-width:95%; background:#fff; border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,0.08); position:relative; }
  .game-area { height:260px; position:relative; border:1px dashed #e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden; background:linear-gradient(180deg,#fff,#fbfdff); }
  .player {
    width:60px; height:60px; border-radius:50%;
    background: linear-gradient(135deg,#3aa0ff,#0e74d1);
    position:absolute;
    top:50%; transform:translateY(-50%);
    left: calc(50% - 220px);
    box-shadow:0 6px 18px rgba(12,48,90,0.12);
    transition: none;
    background-size: cover; background-position:center;
  }
  .wall {
    position:absolute; right:40px; top:50%; transform:translateY(-50%);
    width:48px; height:140px; background:#333; border-radius:6px;
  }
  .ui { margin-top:16px; display:flex; justify-content:space-between; align-items:center; }
  .timer { font-weight:700; color:#111; }
  .hint { color:#666; font-size:13px; }
  .notice { margin-top:10px; color:#b33; font-size:14px; min-height:18px; }
  #retryBtn { display:none; margin-top:12px; padding:8px 12px; border-radius:6px; border:none; background:#0e74d1; color:#fff; cursor:pointer; }
  #retryBtn:active { transform:translateY(1px); }
  @media (max-width:600px) {
    .wrap { padding:20px; }
    .game-area { height:200px; }
    .player { width:48px; height:48px; left: calc(50% - 170px); }
    .wall { height:120px; width:44px; right:22px; }
  }
</style>
</head>
<body>
    <div class="menu">
        <a href="index.php"><h2>index</h2></a>
        <a href="login.php"><h2>login</h2></a>
        <a href="memo.php"><h2>memo</h2></a>
        <a href="plus.php"><h2>plus</h2></a>
    </div>
<div class="wrap">
  <h3>오른쪽 벽에 도달하시오</h3>
  <div class="game-area" id="gameArea">
    <div class="player" id="player" style="<?= $uploadedIcon ? "background-image:url('{$uploadedIcon}');" : "" ?>"></div>
    <div class="wall" id="wall"></div>
  </div>
  <div class="ui">
    <div class="timer">남은시간: <span id="time">10.0</span>s</div>
  </div>
  <div class="notice" id="notice"></div>

  <!-- Retry 버튼 -->
  <div>
    <button id="retryBtn">Retry</button>
  </div>

  <div style="margin-top:12px; color:#444; font-size:13px;">
    현재 사용자: <strong><?= htmlspecialchars($appUser) ?></strong> (role: <?= htmlspecialchars($userRole) ?>)
  </div>
</div>

<script>
// 서버값
const serverSpeed = Number(<?= json_encode($gameSpeed) ?>) || 10.0;
</script>
<script src="js/game-play.js"></script>
</body>
</html>