<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token'])) {
    header("Location: beforelogin.php");
    exit;
}

try {
    // 토큰 해석
    $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256'));
    $user = (array)$decoded->user;  // 객체를 배열로 변환
} catch (Exception $e) {
    echo "<script>alert('유효하지 않은 토큰입니다.'); location.href='beforelogin.php';</script>";
    exit;
}
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
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
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
