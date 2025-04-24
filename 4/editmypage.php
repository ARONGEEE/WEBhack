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

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) echo "DB 연결 실패";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = md5($_POST['pass']);
    $newpass = md5($_POST['newpass']);
    
    
    $score = $_POST['score'];
    $location = $_POST['location'];

    if($pass==$user['pass']){
        $sql = "UPDATE `1111` SET pass='$newpass', score=$score, address= '$location' WHERE id='$user[id]'";
        if (mysqli_query($conn, $sql)) {
            $result = mysqli_query($conn, "SELECT * FROM `1111` WHERE id='$user[id]'");
            $user= mysqli_fetch_assoc($result);
            $payload = [
                'user' => $user,
                'exp' => time() + 600 // 짧은시간
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            header("Location: mypage.php?token=$jwt");
            exit;
        } else {
            echo "<script>alert('수정실패'); history.back();</script>";
        }
    }else{
        echo "<script>alert('현재 비밀번호가 틀렸습니다.'); history.back();</script>";
        exit;
    }

    exit;
}


?>




<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>내 정보 수정</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="myform-area">
    <div class="form-area">
      
      <div class="form-content">
        <h2>내 정보 수정</h2>
      </div>

      <div class="form-input">
        <form method="post">
          <div class="form-group">
            <input type="password" name="newpass" required>
            <label>새로운 비밀번호</label>
          </div>
          <div class="form-group">
            <input type="password" name="pass" required>
            <label>현재 비밀번호</label>
          </div>
          <div class="form-group">
            <input type="number" name="score" required value="<?= $user['score'] ?>">
            <label>점수</label>
          </div>
          <div class="form-group">
            <input type="text" name="location" required value="<?= $user['address'] ?>">
            <label>주소</label>
          </div>
          <div class="myform-button">
            <button class="myform-btn" type="submit">수정하기</button>
          </div>
        </form>

        <div class="myform-button">
          <a href="mypage.php?token=<?= $_GET['token'] ?>"><button class="myform-btn">마이페이지로</button></a>
        </div>
      </div>

    </div>
  </div>
</body>

</html>
