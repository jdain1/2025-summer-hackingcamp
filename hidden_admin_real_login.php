<?php
// hidden_admin_real_login.php
session_start();
ini_set('display_errors', 0);
error_reporting(0);

// ===== Server-side parameters =====
$EXPECTED_UID = 'admin109';
$SALT = 's0m3_s@lt_2025!'; // 반드시 login.html의 doubleEncodedSalt를 디코딩한 값과 동일해야 합니다.

// index.php에 flag를 두실 예정이므로 여기서는 flag를 출력하지 않습니다.

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'] ?? '';
    $upw = $_POST['upw'] ?? '';

    // Normalize
    $uid = trim($uid);
    $upw = trim($upw);

    if ($uid === $EXPECTED_UID) {
        // 서버가 기대하는 값: sha256(uid + salt)
        $expected_hash = hash('sha256', $uid . $SALT);

        // 비교: 제출한 upw가 해시(hex)와 동일하면 성공
        // 1e433969c9207f61680432783abf138316323e80b2fba87cb752225561ff8244
        // hex.py 참고해 비밀번호 얻기
        if (hash_equals($expected_hash, $upw)) {
            // 성공: 세션 설정 후 index.php로 리다이렉트
            $_SESSION['username'] = $uid;
            header('Location: index.php');
            exit;
        } else {
            $msg = '로그인 실패: 비밀번호가 틀렸습니다.';
        }
    } else {
        $msg = '로그인 실패: 아이디가 틀렸습니다.';
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Hidden Admin Login</title>
    <link rel="stylesheet" href="css/login-box.css">
</head>
<body>
    <h2>Hidden Admin Login (CTF)</h2>
    <form method="POST" action="">
        <label>uid: <input type="text" name="uid" required></label><br>
        <label>upw (sha256 hex): <input type="text" name="upw" required></label><br>
        <button type="submit">로그인</button>
    </form>
