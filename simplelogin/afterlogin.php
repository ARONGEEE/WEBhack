<?php
$rid = 'areare';
$rpwd = '324324';

$id = $_POST['id'] ?? '';
$pwd = $_POST['pwd'] ?? '';

$check = ($id === $rid && $pwd === $rpwd);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>로그인 결과</title>
  <link rel="stylesheet" href="login.css">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">
</head>
<body>
<div class="myform-area">
  <div class="form-area">
    <div class="form-content" style="width: 100%;">
      <?php if ($check): ?>
        <h2 style="color: white; font-size: 28px; margin-bottom: 20px;">
          (<?= htmlspecialchars($id) ?>)로그인성공
          
        </h2>
      <?php else: ?>
        <h2 style="color: white;">아이디 또는 비밀번호가 틀렸습니다</h2>
        <p><a href="beforelogin.php" style="color:black">다시 로그인</a></p>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
