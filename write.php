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
    $title = $_POST['title'];
    $content = $_POST['content'];
    $writer = $user['id'];
    $filename = '';
    $filepath = '';

    // 파일 업로드 처리
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $upload_dir = 'uploads/';
        
        // uploads 디렉토리가 없으면 생성
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];
        
        // 취약점: 파일 확장자 검증 없음 (웹쉘 업로드 가능)
        $filepath = $upload_dir . time() . '_' . $filename;
        
        if (move_uploaded_file($tmp_name, $filepath)) {
            // 파일 업로드 성공
        } else {
            echo "<script>alert('파일 업로드 실패'); history.back();</script>";
            exit;
        }
    }

    // 취약점: SQL Injection 가능
    $sql = "INSERT INTO board (title, content, writer, filename, filepath) VALUES ('$title', '$content', '$writer', '$filename', '$filepath')";
    
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
    <!-- 상단 탭 -->
    <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
        <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
        <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
        <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
    </div>

    <!-- 글쓰기 영역 -->
    <div class="form-area">
      <div class="form-content">
        <h2>게시글 작성</h2>
      </div>

      <div class="form-input">
        <!-- enctype 추가 -->
        <form method="post" enctype="multipart/form-data">
          <!-- 제목 입력 -->
          <div class="form-group">
            <input type="text" name="title" required>
            <label>제목</label>
          </div>

          <!-- 내용 입력 -->
          <div class="form-group">
            <textarea name="content" required placeholder="내용을 입력하세요"></textarea>
          </div>

          <!-- 파일 업로드 -->
          <div class="file-upload">
            <label>첨부파일:</label>
            <input type="file" name="file" id="fileInput">
            <small style="color: #666;">최대 10MB까지 업로드 가능</small>
            
            <!-- 선택된 파일 정보 표시 -->
            <div class="file-info" id="fileInfo">
              <span id="fileName"></span>
              <button type="button" class="delete-file" onclick="deleteFile()">삭제</button>
            </div>
          </div>

          <!-- 작성 완료 버튼 -->
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