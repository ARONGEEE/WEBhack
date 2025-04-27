<?php
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) echo "DB 연결 실패";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $pass = $_POST['pass'];
    
    
    $score = $_POST['score'];
    $location = $_POST['location'];

    //중복 아이디 체크
    $check = mysqli_query($conn, "SELECT * FROM `1111` WHERE id = '$id'");
    if (mysqli_num_rows($check)!= 0) {
        echo "<script>alert('이미 존재하는 아이디입니다.'); history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO `1111` VALUES (NULL, '$pass', $score, '$id', DEFAULT, '$location')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('회원가입 완료! 로그인 해주세요.'); location.href='beforelogin.php';</script>";
    } else {
        echo "<script>alert('회원가입 실패: 서버 오류'); history.back();</script>";
    }
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>회원가입</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="myform-area">
    <div class="form-area">
      
      <div class="form-content">
        <h2>회원가입</h2>
        <p>회원 정보를 입력하고 가입하세요!</p>
      </div>

      <div class="form-input">
        <form method="post">
          <div class="form-group">
            <input type="text" name="id" required>
            <label>아이디</label>
          </div>
          <div class="form-group">
            <input type="password" name="pass" required>
            <label>비밀번호</label>
          </div>
          <div class="form-group">
            <input type="number" name="score" required>
            <label>점수</label>
          </div>
          <div class="form-group">
            <input type="text" name="location" required>
            <label>주소</label>
          </div>
          <div class="myform-button">
            <button class="myform-btn" type="submit">가입하기</button>
          </div>
        </form>

        <div class="myform-button">
          <a href="beforelogin.php"><button class="myform-btn">로그인으로</button></a>
        </div>
      </div>

    </div>
  </div>
</body>

</html>
