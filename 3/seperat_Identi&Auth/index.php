<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: beforelogin.php");
    exit;
}

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
  <title>홈</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

  <div style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php" class="myform-btn">홈</a>
    <a href="mypage.php" class="myform-btn">마이페이지</a>
  </div>

  <div class="myform-area">
    <div class="form-area">

      <div class="form-content">
        <h2>홈 화면</h2>
        <p>사용자 : <strong><?= htmlspecialchars($user['id']) ?></strong></p>
      </div>

      <div class="form-input">
        <div class="myform-button" style="margin-top: 20px;">
          <a href="index.php?logout=1"><button class="myform-btn">로그아웃</button></a>
        </div>
      </div>

    </div>
  </div>

</body>
</html>
