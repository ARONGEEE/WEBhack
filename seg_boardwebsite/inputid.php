<?php
session_start();
require_once 'dbconfig.php';

$conn = getDBConnection();

if (!isset($_SESSION['temp_id'])) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='beforelogin.php';</script>";
    exit;
}

$id = $_SESSION['temp_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['pass'])) {
        echo "<script>alert('비밀번호를 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $pass = md5($_POST['pass']);
    
    $stmtem = mysqli_prepare($conn, "SELECT * FROM `1111` WHERE id = ?");
    mysqli_stmt_bind_param($stmtem, "s", $id);
    mysqli_stmt_execute($stmtem);
    $resultst = mysqli_stmt_get_result($stmtem);
    $user = mysqli_fetch_assoc($resultst);
    
    if ($user && $user['pass'] === $pass) {
        $_SESSION['user'] = $user;
        unset($_SESSION['temp_id']);
        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }
    
    mysqli_stmt_close($stmtem);
}

mysqli_close($conn);
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
      <h2>로그인</h2>
    </div>
    <div class="form-input">
      <h2>'<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>'으로 로그인</h2>
      <form method="post">
        <div class="form-group">
          <input type="password" name="pass" required maxlength="100" autocomplete="current-password">
          <label>비밀번호</label>
        </div>
        <div class="myform-button">
          <button type="submit" class="myform-btn">로그인</button>
        </div>
      </form>
      <div class="myform-button" style="margin-top: 20px;">
        <a href="beforelogin.php">
          <button class="myform-btn">이전페이지</button>
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>