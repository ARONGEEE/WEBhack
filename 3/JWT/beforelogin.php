<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT; //c++ namespace std;느낌

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

$conn = mysqli_connect("localhost", "root", "", "test");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_POST['id'] = mysqli_real_escape_string($conn, $_POST['id']);

    $result = mysqli_query($conn, "SELECT * FROM `1111` WHERE id='$_POST[id]'");
    $tempid = mysqli_fetch_assoc($result)['id'];
    if ($tempid) {
        $payload = [
          'id' => $tempid,
          'exp' => time() + 600 // 짧은시간
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        header("Location: inputid.php?token=$jwt");
        exit;
    } else {
        echo "<script>alert('존재하지 않는 아이디입니다.'); history.back();</script>";
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
      <h2>로그인</h2>
      <form method="post">
        <div class="form-group">
          <input type="text" name="id" required>
          <label>아이디</label>
        </div>
        <div class="myform-button">
          <button type="submit" class="myform-btn">다음</button>
        </div>
      </form>
      <div class="myform-button">
        <a href="register.php"><button class="myform-btn">회원가입</button></a>
      </div>
    </div>

  </div>
</div>

</body>
</html>
