<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='beforelogin.php';</script>";
    exit;
}

try {
    $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256'));
    $user = (array)$decoded->user;
} catch (Exception $e) {
    echo "<script>alert('유효하지 않은 토큰입니다.'); location.href='beforelogin.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>마이페이지</title>
  <link rel="stylesheet" href="login.css">

<body>
    <!-- 상단 메뉴 (가운데 정렬) -->
    <div style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
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
