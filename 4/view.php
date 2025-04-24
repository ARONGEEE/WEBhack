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
$sql = "SELECT * FROM board WHERE id = $id";
$result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($post['title']) ?> - 글 보기</title>
  <link rel="stylesheet" href="login.css">

</head>
<body>

  <!-- 상단 메뉴 -->
  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
  </div>

  <div class="myform-area">
    <div class="post-box">
      <h2><?= htmlspecialchars($post['title']) ?></h2>
      <div class="meta">
        작성자: <?= htmlspecialchars($post['writer']) ?> |
        작성일: <?= $post['created_at'] ?>
      </div>
      <div class="content">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
      </div>

      <?php if ($post['writer'] === $user['id']): ?>
        <div class="myform-button" style="margin-top: 20px; text-align: center; display: flex; justify-content: center; gap: 10px;">
            <a href="editwrite.php?id=<?= $post['id'] ?>&token=<?= $_GET['token'] ?>" class="myform-btn">수정하기</a>
            <a href="delete.php?id=<?= $post['id'] ?>&token=<?= $_GET['token'] ?>" class="myform-btn" onclick="return confirm('정말 삭제하시겠습니까?');">삭제하기</a>
        </div>
        <?php endif; ?>



    </div>
  </div>

</body>
</html>

