<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: beforelogin.php");
    exit;
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>마이페이지</title>
  <link rel="stylesheet" href="login.css">

<body>
    <div style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php" class="myform-btn">홈</a>
    <a href="mypage.php" class="myform-btn">마이페이지</a>
     </div>

  <div class="myform-area">
    <div class="form-area">
      <div class="form-content">
        <h2>마이페이지</h2>
        <p>개인 정보 확인</p>
      </div>
      <div class="form-input">
        <p><strong>ID:</strong> <?= $user['id'] ?></p>
        <p><strong>Score:</strong> <?= $user['score'] ?></p>
        <p><strong>Location:</strong> <?= $user['address'] ?></p>
        <p><strong>Register date:</strong> <?= $user['date'] ?></p>
      </div>
    </div>
  </div>
</body>
</html>
