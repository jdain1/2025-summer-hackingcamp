<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        .login-box h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-box button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-box button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <form id="fakeLoginForm">
            <input type="text" id="uid" name="uid" placeholder="User ID" required>
            <input type="password" id="upw" name="upw" placeholder="Password" required>
            <button type="submit">로그인</button>
        </form>
    </div>

    <script>
        // 겉보기에는 로그인처럼 동작하지만,
        // 어떤 입력을 해도 항상 실패 알람
        document.getElementById("fakeLoginForm").addEventListener("submit", function(event) {
            event.preventDefault();
            alert("[beta] 로그인 기능이 작동하지 않습니다.");
        });

        // 숨겨진 실제 경로
        // console.log(atob('aGlkZGVuX2FkbWluX3JlYWxfbG9naW4ucGhw'));
        const realLoginPath = "aGlkZGVuX2FkbWluX3JlYWxfbG9naW4ucGhw"; 

        // salt -> hidden 경로에서 비밀 번호에 이용
        // atob('czBtM19zQGx0XzIwMjUh')  // -> "s0m3_s@lt_2025!"
        const encodedSalt = "czBtM19zQGx0XzIwMjUh";

    </script>
</body>
</html>
