<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token'])){
    echo "<script>alert('토큰 못받음'); location.href='beforelogin.php';</script>";
    exit;
}

try {
    $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256'));
    $user = (array)$decoded->user;
} catch (Exception $e) {
    echo "<script>alert('시간땡'); location.href='beforelogin.php';</script>";
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "test"); // 데이터베이스 연결
if (!$conn) die("DB 연결 실패");

$id = intval($_GET['id']); // GET 파라미터로 전달된 게시글 ID를 정수로 저장(내 id가아니라 그 글쓴이의 id)

// 해당 게시글을 DB에서 조회
$sql = "SELECT * FROM board WHERE id = $id";
$result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);

if (!$post) { // 게시글이 존재하지 않을 경우(갑자기 중간에 삭제되었다면)
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title><?= $post['title'] ?> - 글 보기</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

  <!-- 상단 탭 -->
  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
  </div>

  <!-- 글쓰기 큰영역 -->
  <div class="myform-area">
    <div class="post-box">
      
      <!-- 글 제목 -->
      <h2><?= $post['title'] ?></h2>

      <!-- 작성자, 작성일 -->
      <div class="meta">
        작성자: <?= $post['writer'] ?> |
        작성일: <?= $post['created_at'] ?>
      </div>

      <!-- 글 내용 -->
      <div class="content">
        <?= $post['content'] ?>
      </div>

      <!-- 본인 글인 경우에만 수정/삭제 버튼 출력 -->
      <?php if ($post['writer'] === $user['id']): ?>
        <div class="myform-button" style="margin-top: 20px; text-align: center; display: flex; justify-content: center; gap: 10px;">
            <!-- 수정 페이지로 이동 -->
            <a href="editwrite.php?id=<?= $post['id'] ?>&token=<?= $_GET['token'] ?>" class="myform-btn">수정하기</a>
            <!-- 삭제 페이지로 이동 (확인창 포함) -->
            <a href="delete.php?id=<?= $post['id'] ?>&token=<?= $_GET['token'] ?>" class="myform-btn" onclick="return confirm('정말 삭제하시겠습니까?');">삭제하기</a>
        </div>
      <?php endif; ?>

    </div>
  </div>

</body>
</html>
