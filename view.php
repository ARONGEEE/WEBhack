<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token'])) {
    echo "<script>alert('토큰 못받음'); location.href='beforelogin.php';</script>";
    exit;
}

try {
    $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256'));
    $user = (array)$decoded->user;
} catch (Exception $e) {
    echo "<script>alert('시간땡.'); location.href='beforelogin.php';</script>";
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("DB 연결 실패");
}

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

  <!-- 상단 탭 -->
  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
  </div>

  <!-- 본문 영역 -->
  <div class="myform-area">
    <div class="form-area">

      <!-- 왼쪽 영역 -->
      <div class="form-content">
        <h2>게시글 보기</h2>
        <p>작성된 내용을 확인하고 파일이 있다면 다운로드하세요.</p>
      </div>

      <!-- 오른쪽 영역 -->
      <div class="form-input">

        <!-- 제목 -->
        <div class="form-group">
          <input type="text" value="<?= htmlspecialchars($post['title']) ?>" readonly>
          <label>제목</label>
        </div>

        <!-- 내용 -->
        <div class="form-group">
          <textarea readonly><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <!-- 첨부파일 -->
        <?php if ($post['filename']): ?>
          <div class="file-upload">
            <label>첨부파일:</label>
            <div class="existing-file">
              <span><?= htmlspecialchars($post['filename']) ?></span>
              <a href="<?= htmlspecialchars($post['filepath']) ?>" download class="btn-small-red">다운로드</a>
            </div>
          </div>
        <?php endif; ?>

        <!-- 작성자 정보 -->
        <div class="meta" style="margin-top:10px; color:#595959;">
          작성자: <?= htmlspecialchars($post['writer']) ?> | 작성일: <?= htmlspecialchars($post['created_at']) ?>
        </div>

        <!-- 본인 글이면 수정/삭제 버튼 -->
        <?php if ($post['writer'] === $user['id']): ?>
          <div class="myform-button" style="margin-top: 20px; display: flex; justify-content: center; gap: 10px;">
            <a href="editwrite.php?id=<?= $post['id'] ?>&token=<?= $_GET['token'] ?>" class="myform-btn">수정하기</a>
            <a href="delete.php?id=<?= $post['id'] ?>&token=<?= $_GET['token'] ?>" class="myform-btn" onclick="return confirm('정말 삭제하시겠습니까?');">삭제하기</a>
          </div>
        <?php endif; ?>

      </div>

    </div>
  </div>

</body>
</html>
