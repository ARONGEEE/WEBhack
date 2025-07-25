<?php
session_start();
require_once 'dbconfig.php';

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['id'])) {
        echo "<script>alert('아이디를 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $stmtem = mysqli_prepare($conn, "SELECT id FROM `1111` WHERE id = ?");
    mysqli_stmt_bind_param($stmtem, "s", $_POST['id']);
    mysqli_stmt_execute($stmtem);
    $resultst = mysqli_stmt_get_result($stmtem);
    $rows = mysqli_fetch_assoc($resultst);
    
    if ($rows) {
        $_SESSION['temp_id'] = $rows['id'];
        header("Location: inputid.php");
        exit;
    } else {
        echo "<script>alert('존재하지 않는 아이디입니다.'); history.back();</script>";
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
      <form method="post">
        <div class="form-group">
          <input type="text" name="id" required maxlength="50" pattern="[a-zA-Z0-9_]+" title="영문, 숫자, 언더스코어만 사용 가능">
          <label>아이디</label>
        </div>
        <div class="myform-button">
          <button type="submit" class="myform-btn">다음</button>
        </div>
      </form>
      <div class="myform-button">
        <a href="register.php">
          <button class="myform-btn">회원가입</button>
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>