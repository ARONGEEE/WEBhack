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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board.php?token=" . htmlspecialchars($_GET['token']) . "';</script>";
    exit;
}

$id = intval($_GET['id']);
$stmtem = mysqli_prepare($conn, "SELECT * FROM board WHERE id = ?");
mysqli_stmt_bind_param($stmtem, "i", $id);
mysqli_stmt_execute($stmtem);
$result = mysqli_stmt_get_result($stmtem);
$post = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmtem);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . htmlspecialchars($_GET['token']) . "';</script>";
    exit;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($post['title']) ?> - 글 보기</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  
  <div class="myform-area">
    <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>" class="myform-btn">게시판</a>
  </div>
    <div class="form-area">
      <div class="form-content">
        <h2>게시글 보기</h2>
        <p>작성된 내용을 확인하고 파일이 있다면 다운로드하세요.</p>
      </div>
      <div class="form-input">
        <div class="form-group">
          <input type="text" value="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>" readonly>
        </div>
        <div class="form-group">
          <textarea readonly><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
        <?php if ($post['filename']): ?>
          <div class="file-upload">
            <label>첨부파일:</label>
            <div class="existing-file">
              <span><?= htmlspecialchars($post['filename'], ENT_QUOTES, 'UTF-8') ?></span>
              <a href="<?= htmlspecialchars($post['filepath'], ENT_QUOTES, 'UTF-8') ?>" download class="btn-small-red">다운로드</a>
            </div>
          </div>
        <?php endif; ?>
        <div class="meta" style="margin-top:10px; color:#595959;">
          작성자: <?= htmlspecialchars($post['writer'], ENT_QUOTES, 'UTF-8') ?> | 작성일: <?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <!-- 본인 글이면 수정/삭제 버튼 -->
        <?php if ($post['writer'] === $user['id']): ?>
            <div class="myform-button" style="margin-top: 10px; display: flex; justify-content: center; gap: 10px;">
              <a href="editwrite.php?id=<?= $post['id'] ?>&token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>"
                class="myform-btn" style="padding: 12px 15px; height: auto; line-height: normal;">수정하기</a>
              <a href="delete.php?id=<?= $post['id'] ?>&token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>"
                class="myform-btn" style="padding: 12px 15px; height: auto; line-height: normal;"
                onclick="return confirm('정말 삭제?');">삭제하기</a>
            </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>