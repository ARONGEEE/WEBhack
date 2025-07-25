<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='beforelogin.php';</script>";
    exit;
}

$user = $_SESSION['user'];
$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['pass']) || empty($_POST['newpass']) || empty($_POST['score']) || empty($_POST['location'])) {
        echo "<script>alert('모든 필드를 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $score = intval($_POST['score']);
    if ($score < 0 || $score > 1000) {
        echo "<script>alert('점수는 0-1000 범위 내에서 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $pass = md5($_POST['pass']);
    $newpass = md5($_POST['newpass']);
    $location = $_POST['location'];
    
    if ($pass == $user['pass']) {
        $stmtem = mysqli_prepare($conn, "UPDATE `1111` SET pass = ?, score = ?, location = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmtem, "siss", $newpass, $score, $location, $user['id']);
        
        if (mysqli_stmt_execute($stmtem)) {
            mysqli_stmt_close($stmtem);
            
            $select_user = mysqli_prepare($conn, "SELECT * FROM `1111` WHERE id = ?");
            mysqli_stmt_bind_param($select_user, "s", $user['id']);
            mysqli_stmt_execute($select_user);
            $resultst = mysqli_stmt_get_result($select_user);
            $user = mysqli_fetch_assoc($resultst);
            mysqli_stmt_close($select_user);
            
            $_SESSION['user'] = $user;
            header("Location: mypage.php");
            exit;
        } else {
            echo "<script>alert('수정실패'); history.back();</script>";
        }
    } else {
        echo "<script>alert('현재 비밀번호가 틀렸습니다.'); history.back();</script>";
        exit;
    }
}

mysqli_close($conn);
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
            <input type="password" name="newpass" required maxlength="100" autocomplete="new-password">
            <label>새로운 비밀번호(변경 원치 않을시 현재비밀번호 입력)</label>
          </div>
          <div class="form-group">
            <input type="password" name="pass" required maxlength="100" autocomplete="current-password">
            <label>현재 비밀번호</label>
          </div>
          <div class="form-group">
            <input type="number" name="score" required value="<?= htmlspecialchars($user['score']) ?>" min="0" max="1000">
            <label>점수</label>
          </div>
          <div class="form-group">
            <input type="text" name="location" required value="<?= htmlspecialchars($user['location']) ?>" maxlength="255">
            <label>주소</label>
          </div>
          <div class="myform-button">
            <button class="myform-btn" type="submit">수정하기</button>
          </div>
        </form>
        <div class="myform-button">
          <a href="mypage.php">
            <button class="myform-btn">마이페이지로</button>
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>