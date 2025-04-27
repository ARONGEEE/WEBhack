<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

$conn = mysqli_connect("localhost", "root", "", "test");

if (!isset($_GET['token'])) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='beforelogin.php';</script>";
    exit;
}

try {
  // 토큰 해석
  $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256'));
  $id = $decoded->id;
} catch (Exception $e) {
  echo "<script>alert('유효하지 않은 토큰입니다.'); location.href='beforelogin.php';</script>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pass = md5($_POST['pass']);

  $result = mysqli_query($conn, "SELECT * FROM `1111` WHERE id='$id'");
  $user= mysqli_fetch_assoc($result);
  if ($user['pass'] === $pass) {
      $payload = [
        'user' => $user,
        'exp' => time() + 600 
      ];
      $jwt = JWT::encode($payload, $key, 'HS256');
      header("Location: index.php?token=$jwt");
      exit;
  } else {
      echo "<script>alert('비밀번호가 틀렸습니다.'); history.back();</script>";
      exit;
  }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>로그인 폼</title>
  <link rel="stylesheet" href="login.css">
  
</head>
<body>
<div class="myform-area">
  <div class="form-area">
    
    <div class="form-content">
      <h2>세번째 과제</h2>
      <p>로그인 인증과정 : 식별or인증 통합or분리 / hash처리 or 원본.</p>
    </div>

    <div class="form-input">
      <h2>'<?= $id ?>'으로 로그인</h2>
      <form method="post" action="inputid.php?token=<?=($_GET['token']) ?>">
        <div class="form-group">
          <input type="password" name="pass" required>
          <label>비밀번호</label>
        </div>
        <div class="myform-button">
          <button type="submit" class="myform-btn">로그인</button>
        </div>
      </form>
        <div class="myform-button" style="margin-top: 20px;">
          <a href="index.php?logout=1"><button class="myform-btn">다른아이디로 로그인</button></a>
        </div>
    </div>
    

  </div>
</div>

</body>
</html>