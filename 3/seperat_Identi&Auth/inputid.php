<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "test");

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: beforelogin.php");
    exit;
}

if (!isset($_SESSION['id'])) {
    header("Location: beforelogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['pass'];

    $result = mysqli_query($conn, "SELECT * FROM `1111` WHERE id='{$_SESSION['id']}'");
    $user= mysqli_fetch_assoc($result);
    if ($user['pass'] === $pass) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
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
      <h2>'<?= $_SESSION['id'] ?>'으로 로그인</h2>
      <form method="post" action="inputid.php">
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