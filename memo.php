<?php
session_start();

// 로그인한 사용자 이름 가져오기 (없으면 guest)
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

// 메모 저장 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['memo'])) {
    $memo = trim($_POST['memo']);
    if ($memo !== '') {
        $stmt = $pdo->prepare("INSERT INTO memo (uid, content) VALUES (?, ?)");
        $stmt->execute([$appUser, $memo]);
    }
}

// 현재 사용자 메모 불러오기
$stmt = $pdo->prepare("SELECT content, created_at FROM memo WHERE uid = ? ORDER BY id DESC");
$stmt->execute([$appUser]);
$memos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <link rel="stylesheet" href="css/menu.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>메모장</title>
    <style>
        .main {
            width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffffff;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            border-radius: 12px;
            text-align : center;
        }
        h2 {
            margin-bottom: 15px;
        }
        .memo-form textarea {
            width: 100%;
            height: 80px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            resize: none;
            font-size: 15px;
        }
        .memo-form button {
            margin-top: 10px;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            background: #ffda77;
            cursor: pointer;
            font-weight: bold;
        }
        .memo-form button:hover {
            background: #ffc447;
        }
        .memo-list {
            margin-top: 30px;
        }
        .memo-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .memo-item small {
            display: block;
            color: #888;
            font-size: 12px;
            margin-top: 5px;
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

    <div class="main">
        <h2><?= htmlspecialchars($appUser) ?>님의 메모장</h2>
        <h4>자유롭게 글을 남길 수 있는 공간입니다!<br>※ 글 삭제 시 다시 복구되지 않습니다 ※</h4>
        <form method="post" class="memo-form">
            <textarea name="memo" placeholder="메모를 입력하세요..." required></textarea><br>
            <button type="submit">저장</button>
        </form>

        <div class="memo-list">
            <?php if ($memos): ?>
                <?php foreach ($memos as $row): ?>
                    <div class="memo-item">
                        <?= nl2br(htmlspecialchars($row['content'])) ?>
                        <?php if (isset($row['created_at'])): ?>
                            <small>작성일: <?= htmlspecialchars($row['created_at']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>아직 메모가 없습니다.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
