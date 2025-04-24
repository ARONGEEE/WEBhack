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
    echo "<script>alert('시간땡'); location.href='beforelogin.php';</script>";
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) die("DB 연결 실패");

$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM board WHERE id = $id"); // 해당 글 조회
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

if ($post['writer'] !== $user['id']) { //작성자 ID와 현재 로그인된 ID가 다르다면(애초에 뷰에서 버튼이 안뜨게 설정은 함)
    echo "<script>alert('본인 글만 수정할 수 있습니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title =$_POST['title'];
    $content = $_POST['content'];

    $update = "UPDATE board SET title='$title', content='$content' WHERE id = $id"; // DB 업데이트 쿼리
    if (mysqli_query($conn, $update)) {// 성공 시
        //글 보기 페이지로 이동
        echo "<script>alert('수정 완료'); location.href='view.php?id=$id&token=" . $_GET['token'] . "';</script>";
        exit;
    } else {// 실패 시
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

  <!-- 상단 탭 -->
  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
  </div>

  <!-- 본문  -->
  <div class="myform-area">
    <div class="form-area">

      <!-- 제목 -->
      <div class="form-content">
        <h2>게시글 수정</h2>
      </div>

      <!-- 수정 입력 -->
      <div class="form-input">
        <form method="post">
          <!-- 제목 입력(원래 내용에서)-->
          <div class="form-group">
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            <label>제목</label>
          </div>

          <!-- 내용 입력 필드(원래 내용에서)-->
          <div class="form-group">
            <textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
            <label>내용</label>
          </div>

          <!-- 제출 버튼 -->
          <div class="myform-button">
            <button class="myform-btn" type="submit">수정 완료</button>
          </div>
        </form>
      </div>

    </div>
  </div>

</body>
</html>
