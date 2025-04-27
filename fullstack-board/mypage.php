<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token'])) {
    echo "<script>alert('토큰을 못받음.'); location.href='beforelogin.php';</script>";
    exit;
}

try {
    $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256'));
    $user = (array)$decoded->user;
} catch (Exception $e) {
    echo "<script>alert('시간땡'); location.href='beforelogin.php';</script>";
    exit;
}
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

    <!-- 상단 탭 -->
    <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
      <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
      <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
      <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
    </div>

    <!-- 마이페이지영역 -->
    <div class="form-area">
      <div class="form-content">
        <h2>마이페이지</h2> <!-- 페이지 제목 -->
        <p>개인 정보 확인</p> <!-- 설명 -->
      </div>

      <div class="form-input">
        <!-- 유저 정보 -->
        <p><strong>ID:</strong> <?= $user['id'] ?></p> <!-- ID -->
        <p><strong>Score:</strong> <?= $user['score'] ?></p> <!-- 점수 -->
        <p><strong>Location:</strong> <?= $user['address'] ?></p> <!-- 주소 -->
        <p><strong>Register date:</strong> <?= $user['date'] ?></p> <!-- 가입일 -->

        <!-- 정보 수정 버튼 -->
        <div class="myform-button" style="margin-top: 20px;">
          <a href="editmypage.php?token=<?=($_GET['token']) ?>"><button class="myform-btn">내정보 수정</button></a>
        </div>
      </div>

    </div>
  </div>

</body>
</html>

