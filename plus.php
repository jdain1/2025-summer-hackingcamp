<?php
// plus.php
session_start();
$appUser = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';

$dbHost = 'localhost';
$dbName = 'sqli_test';
$dbUser = 'root';
$dbPass = '1110';
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
$pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 파일 업로드 처리 (간단히)
    if (!empty($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['icon']['tmp_name'];
        $dest = __DIR__ . '/uploads/icon.png';
        // 간단한 체크(이미지만 허용)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp);
        finfo_close($finfo);
        if (strpos($mime, 'image/') === 0) {
            move_uploaded_file($tmp, $dest);
            $notice .= "아이콘 업로드 완료. ";
        } else {
            $notice .= "이미지 파일만 업로드하세요. ";
        }
    }

    // 메모 저장 (user_table.memo 사용)
    $memo = isset($_POST['memo']) ? trim($_POST['memo']) : '';
    if ($memo !== '') {
        $stmt = $pdo->prepare("UPDATE user_table SET memo = CONCAT(IFNULL(memo,''), ?) WHERE uid = ?");
        $stmt->execute(["\n" . $appUser . ": " . $memo, $appUser]);
        $notice .= "메모 저장 완료.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/plus.css">
    <link rel="stylesheet" href="css/menu.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sqli</title>
</head>
<body>
    <div class="menu">
        <a href="index.php"><h2>index</h2></a>
        <a href="login.php"><h2>login</h2></a>
        <a href="memo.php"><h2>memo</h2></a>
        <a href="plus.php"><h2>plus</h2></a>
    </div>
<p>현재 사용자: <?= htmlspecialchars($appUser) ?></p>
<form method="post" enctype="multipart/form-data">
  <div>
    <label>아이콘(이미지):</label>
    <input type="file" name="icon" accept="image/*">
  </div>
  <div>
    <label>메모:</label><br>
    <textarea name="memo" rows="4" cols="40"></textarea>
  </div>
  <button type="submit">업로드/저장</button>
</form>
<p><?= htmlspecialchars($notice) ?></p>
</body>

</html>
