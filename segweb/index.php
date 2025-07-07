<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token'])) {
    header("Location: beforelogin.php"); //로그아웃에도 해당
    exit;
}

try {
    $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256')); // 전달된 토큰을 디코딩
    $user = (array)$decoded->user; // 유저 정보 배열로 추출
} catch (Exception $e) { // 토큰이 만료시
    echo "<script>alert('시간땡.'); location.href='beforelogin.php';</script>";
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

  <div class="myform-area">

    <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
      <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
      <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
      <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
    </div>

    <div class="form-area">
      
      <div class="form-content">
        <h2>홈 화면</h2>
        <p>사용자 : <strong><?= htmlspecialchars($user['id']) ?></strong></p>
      </div>

      <div class="form-input">
        <div class="myform-button" style="margin-top: 20px;">
          <!-- 로그아웃 요청 (GET 방식으로 logout=1 전달) -->
          <a href="index.php?logout=1">
            <button class="myform-btn">로그아웃</button>
          </a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>

