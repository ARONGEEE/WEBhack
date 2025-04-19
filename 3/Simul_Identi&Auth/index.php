<?php
session_start();

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: beforelogin.php");
    exit;
}

if (!isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: beforelogin.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "48gozld$13", "test");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $pass = $_POST['pass'];

    $sql = "SELECT * FROM `1111` WHERE id='$id' AND pass='$pass'";
    $result = mysqli_query($conn, $sql) or die("쿼리 오류: " . mysqli_error($conn));

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('로그인 실패'); history.back();</script>";
        exit;
    }
} else {
    $user = $_SESSION['user'];
}
?>

<!-- 홈 화면 -->
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>홈</title>
  <link rel="stylesheet" href="login.css">

</head>
<body>

    <!-- 상단 메뉴 (가운데 정렬) -->
  <div style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php" class="myform-btn">홈</a>
    <a href="mypage.php" class="myform-btn">마이페이지</a>
  </div>


</div>


  <div class="myform-area">
    <div class="form-area">

      <div class="form-content">
        <h2>홈 화면</h2>
        <p>사용자 : <strong><?= $user['id'] ?></strong></p>
      </div>

      <div class="form-input">
        <div class="myform-button" style="margin-top: 20px;">
          <a href="index.php?logout=1"><button class="myform-btn">로그아웃</button></a>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
