<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token']) || !isset($_GET['id'])) {
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
if (!$conn) die("DB 연결 실패");

$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM board WHERE id = $id");
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

// 본인여부
if ($post['writer'] !== $user['id']) {
    echo "<script>alert('본인 글만 수정할 수 있습니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

// 수정
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    $update = "UPDATE board SET title='$title', content='$content' WHERE id = $id";
    if (mysqli_query($conn, $update)) {
        echo "<script>alert('수정 완료'); location.href='view.php?id=$id&token=" . $_GET['token'] . "';</script>";
        exit;
    } else {
        echo "<script>alert('수정 실패'); history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>글 수정</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
  </div>

  <div class="myform-area">
    <div class="form-area">

      <div class="form-content">
        <h2>게시글 수정</h2>
      </div>

      <div class="form-input">
        <form method="post">
          <div class="form-group">
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            <label>제목</label>
          </div>
          <div class="form-group">
            <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
            <label>내용</label>
          </div>
          <div class="myform-button">
            <button class="myform-btn" type="submit">수정 완료</button>
          </div>
        </form>
      </div>

    </div>
  </div>

</body>
</html>
