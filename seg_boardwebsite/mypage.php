<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='beforelogin.php';</script>";
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
</head>
<body>
  <div class="myform-area">
    <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
      <a href="index.php" class="myform-btn">홈</a>
      <a href="mypage.php" class="myform-btn">마이페이지</a>
      <a href="board.php" class="myform-btn">게시판</a>
    </div>
    <div class="form-area">
      <div class="form-content">
        <h2>마이페이지</h2>
        <p>개인 정보 확인</p>
      </div>
      <div class="form-input">
        <p><strong>ID:</strong> <?= htmlspecialchars($user['id'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Score:</strong> <?= htmlspecialchars($user['score'] ?? '0', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($user['location'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Register date:</strong> <?= htmlspecialchars($user['date_time'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        <div class="myform-button" style="margin-top: 20px;">
          <a href="editmypage.php"><button class="myform-btn">내정보 수정</button></a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>