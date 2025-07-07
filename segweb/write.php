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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 입력값 검증
    if (empty($_POST['title']) || empty($_POST['content'])) {
        echo "<script>alert('제목과 내용을 모두 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    // 입력값 길이 검증
    if (strlen($_POST['title']) > 200) {
        echo "<script>alert('제목은 200자 이내로 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    if (strlen($_POST['content']) > 5000) {
        echo "<script>alert('내용은 5000자 이내로 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $title = $_POST['title'];
    $content = $_POST['content'];
    $writer = $user['id'];
    $filename = '';
    $filepath = '';

    // 파일 업로드 처리
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $upload_dir = 'filedir/';
        
        // uploads 디렉토리가 없으면 생성(내 서버 내 웹 루트에)
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];

        $filepath = $upload_dir . time() . '_' . $filename;
        
        if (move_uploaded_file($tmp_name, $filepath)) {
            // 파일 업로드 성공
        } else {
            echo "<script>alert('파일 업로드 실패'); history.back();</script>";
            exit;
        }
    }

    $stmtem = mysqli_prepare($conn, "INSERT INTO board (title, content, writer, filename, filepath) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmtem, "sssss", $title, $content, $writer, $filename, $filepath);
    
    if (mysqli_stmt_execute($stmtem)) {
        mysqli_stmt_close($stmtem);
        echo "<script>alert('글 작성 완료'); location.href='board.php?token=" . htmlspecialchars($_GET['token']) . "';</script>";
        exit;
    } else {
        mysqli_stmt_close($stmtem);
        echo "<script>alert('글 작성 실패'); history.back();</script>";
        exit;
    }
}

mysqli_close($conn);
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
        <a href="index.php?token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>" class="myform-btn">홈</a>
        <a href="mypage.php?token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>" class="myform-btn">마이페이지</a>
        <a href="board.php?token=<?= htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8') ?>" class="myform-btn">게시판</a>
    </div>

    <div class="form-area">
      <div class="form-content">
        <h2>게시글 작성</h2>
      </div>

      <div class="form-input">
        <form method="post" enctype="multipart/form-data">
          <div class="form-group">
            <input type="text" name="title" required maxlength="200">
            <label>제목</label>
          </div>

          <div class="form-group">
            <textarea name="content" required placeholder="내용을 입력하세요" maxlength="5000"></textarea>
          </div>

          <div class="file-upload">
            <label>첨부파일:</label>
            <input type="file" name="file" id="fileInput">
            <small style="color: #666;">(하나의 파일만 업로드가능)</small>
            <div class="file-info" id="fileInfo">
              <span id="fileName"></span>
              <button type="button" class="delete-file" onclick="deleteFile()">삭제</button>
            </div>
          </div>

          <div class="myform-button">
            <button class="myform-btn" type="submit">작성 완료</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // 파일 선택 시 파일명 표시
    document.getElementById('fileInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileInfo').classList.add('active');
      }
    });
    
    // 파일 삭제 기능
    function deleteFile() {
      document.getElementById('fileInput').value = '';
      document.getElementById('fileName').textContent = '';
      document.getElementById('fileInfo').classList.remove('active');
    }
  </script>

</body>
</html>