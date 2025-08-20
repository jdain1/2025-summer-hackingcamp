<!-- ' UNION SELECT 'guest', NULL # -->

<?php
// 에러 표시 끄기 (문구 노출 방지)
ini_set('display_errors', 0);
error_reporting(0);

// mysqli 경고도 전역 억제
mysqli_report(MYSQLI_REPORT_OFF);

// DB 연결
$servername = "localhost";
$username   = "root";
$password   = "1110";
$dbname     = "sqli_test";

$conn = @new mysqli($servername, $username, $password, $dbname);

$msg = '';
$success = false;

// DB 연결 실패도 같은 문구 처리
if ($conn && !$conn->connect_errno) {
    $conn->set_charset('utf8mb4');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 수정(뒤 공백/개행만 제거)
        $uid = rtrim($_POST['uid'] ?? '', " \t\n\r\0\x0B");
        $upw = rtrim($_POST['upw'] ?? '', " \t\n\r\0\x0B");

        // 문제 의도상 취약 쿼리 유지
        $sql = "SELECT uid, email FROM user_table WHERE uid='$uid' AND upw='$upw'";
        $result = $conn->query($sql);

        // 컬럼 수 불일치, 문법 에러 등 어떤 에러든 로그인 실패로 통일
        if ($result instanceof mysqli_result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // 1) uid는 admin109/guest만 인정
            $uid_ok = isset($row['uid']) && ($row['uid'] === 'admin109' || $row['uid'] === 'guest');

            // 2) UNION으로 만든 '특징값'만 통과시키는 장치
            //    - 아래는 NULL 또는 빈 문자열('')일 때만 성공으로 간주하는 예시
            //    - 허용 범위를 넓히려면: in_array($email_val, ['', '0', 'N/A'], true) 등으로 조정 가능
            $email_val = $row['email'] ?? null;
            $email_ok = is_null($email_val) || $email_val === '';

            if ($uid_ok && $email_ok) {
                $success = true;
            } else {
                $msg = "로그인 실패! 아이디 또는 비밀번호가 틀렸습니다.";
            }
        } else {
            $msg = "로그인 실패! 아이디 또는 비밀번호가 틀렸습니다.";
        }
    }
} else {
    $msg = "로그인 실패! 아이디 또는 비밀번호가 틀렸습니다.";
}

// 성공 시: 어떤 출력도 하기 전에 리다이렉트 (빈 화면/헤더 에러 방지)
session_start(); // 세션 사용

if ($success) {
    $_SESSION['username'] = $row['uid'];  // <-- 추가

    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/login-box.css">
    <meta charset="UTF-8" />
    <title>SQL Injection 문제 - 로그인</title>
</head>
<body>
    <div class="menu">
        <a href="index.php"><h2>index</h2></a>
        <a href="login.php"><h2>login</h2></a>
        <a href="memo.php"><h2>memo</h2></a>
        <a href="plus.php"><h2>plus</h2></a>
    </div>
    <div class="login-box">
        <h2>로그인 페이지</h2>
        <form method="POST" action="">
            <label>아이디(uid): <input type="text" name="uid" required placeholder="guest"></label><br><br>
            <label>비밀번호(upw): <input type="password" name="upw" required></label><br><br>
            <button type="submit">로그인</button>
        </form>
        <?php if (!empty($msg)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>