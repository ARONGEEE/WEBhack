<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token'])) {
    header("Location: beforelogin.php");
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
if (!$conn) {
    die("DB 연결 실패");
}

//글끄기
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $writer = $user['id'];

    $sql = "INSERT INTO board (title, content, writer) VALUES ('$title', '$content', '$writer')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('글 작성 완료'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
        exit;
    } else {
        echo "<script>alert('글 작성 실패'); history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>게시글 작성</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

  <div class="myform-area">
    <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
        <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
        <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
        <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
    </div>
    <div class="form-area">
      <div class="form-content">
        <h2>게시글 작성</h2>
      </div>

      <div class="form-input">
        <form method="post">
          <div class="form-group">
            <input type="text" name="title" required>
            <label>제목</label>
          </div>
          <div class="form-group">
            <textarea name="content" required></textarea>
            <label>내용</label>
          </div>
          <div class="myform-button">
            <button class="myform-btn" type="submit">작성 완료</button>
          </div>
        </form>
      </div>

    </div>
  </div>

</body>
</html>
