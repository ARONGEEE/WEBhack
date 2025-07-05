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

$result = mysqli_query($conn, "SELECT * FROM board WHERE id = $id");
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

if ($post['writer'] !== $user['id']) {
    echo "<script>alert('본인 글만 수정할 수 있습니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

// 기존 파일 삭제 요청 처리 (AJAX)
if (isset($_POST['delete_file']) && $_POST['delete_file'] == '1') {
    if ($post['filepath'] && file_exists($post['filepath'])) {
        unlink($post['filepath']);
    }
    
    mysqli_query($conn, "UPDATE board SET filename='', filepath='' WHERE id = $id");
    echo json_encode(['success' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_file'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $filename = $post['filename'];  // 기존 파일명
    $filepath = $post['filepath'];  // 기존 파일경로
    
    // 새 파일 업로드 처리
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $upload_dir = 'uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // 기존 파일이 있으면 삭제
        if ($filepath && file_exists($filepath)) {
            unlink($filepath);
        }
        
        $filename = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];
        $filepath = $upload_dir . time() . '_' . $filename;
        
        if (!move_uploaded_file($tmp_name, $filepath)) {
            echo "<script>alert('파일 업로드 실패'); history.back();</script>";
            exit;
        }
    }
    
    // 취약점: SQL Injection
    $update = "UPDATE board SET title='$title', content='$content', filename='$filename', filepath='$filepath' WHERE id = $id";
    
    if (mysqli_query($conn, $update)) {
        echo "<script>alert('수정 완료'); location.href='view_board.php?id=$id&token=" . $_GET['token'] . "';</script>";
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
  <link rel="stylesheet" href="board-style.css">
</head>
<body>

  <!-- 상단 탭 -->
  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
  </div>

  <!-- 본문 -->
  <div class="myform-area">
    <div class="form-area">

      <!-- 제목 -->
      <div class="form-content">
        <h2>게시글 수정</h2>
      </div>

      <!-- 수정 입력 -->
      <div class="form-input">
        <form method="post" enctype="multipart/form-data">
          <!-- 제목 입력 -->
          <div class="form-group">
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            <label>제목</label>
          </div>

          <!-- 내용 입력 -->
          <div class="form-group">
            <textarea name="content" required placeholder="내용을 입력하세요"><?= htmlspecialchars($post['content']) ?></textarea>
          </div>

          <!-- 파일 업로드 -->
          <div class="file-upload">
            <label>첨부파일:</label>
            
            <!-- 기존 파일이 있는 경우 표시 -->
            <?php if ($post['filename']): ?>
            <div class="existing-file" id="existingFile">
              <span>현재 파일: <?= htmlspecialchars($post['filename']) ?></span>
              <button type="button" class="delete-existing" onclick="deleteExistingFile()">삭제</button>
            </div>
            <?php endif; ?>
            
            <input type="file" name="file" id="fileInput">
            <small style="color: #666;">새 파일을 선택하면 기존 파일이 교체됩니다</small>
            
            <!-- 새로 선택된 파일 정보 표시 -->
            <div class="file-info" id="fileInfo">
              <span id="fileName"></span>
              <button type="button" class="delete-file" onclick="deleteNewFile()">삭제</button>
            </div>
          </div>

          <!-- 제출 버튼 -->
          <div class="myform-button">
            <button class="myform-btn" type="submit">수정 완료</button>
          </div>
        </form>
      </div>

    </div>
  </div>

  <script>
    // 새 파일 선택 시
    document.getElementById('fileInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileInfo').classList.add('active');
      }
    });
    
    // 새로 선택한 파일 삭제
    function deleteNewFile() {
      document.getElementById('fileInput').value = '';
      document.getElementById('fileName').textContent = '';
      document.getElementById('fileInfo').classList.remove('active');
    }
    
    // 기존 파일 삭제 (AJAX)
    function deleteExistingFile() {
      if (confirm('기존 첨부파일을 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('delete_file', '1');
        
        fetch(window.location.href, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('existingFile').remove();
            alert('파일이 삭제되었습니다.');
          }
        })
        .catch(error => {
          alert('파일 삭제 중 오류가 발생했습니다.');
        });
      }
    }
  </script>

</body>
</html>