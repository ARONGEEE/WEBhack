<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='beforelogin.php';</script>";
    exit;
}

$user = $_SESSION['user'];
$conn = getDBConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board.php';</script>";
    exit;
}

$id = intval($_GET['id']);

$stmt = mysqli_prepare($conn, "SELECT * FROM board WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php';</script>";
    exit;
}

if ($post['writer'] !== $user['id']) {
    echo "<script>alert('본인 글만 수정할 수 있습니다.'); location.href='board.php';</script>";
    exit;
}

// 첨부파일 삭제 처리
if (isset($_POST['delete_file']) && $_POST['delete_file'] == '1') {
    $stmt_delete = mysqli_prepare($conn, "UPDATE board SET filename = '', filedata = NULL, filetype = '' WHERE id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);
    
    echo json_encode(['success' => true]);
    exit;
}

// 게시글 수정 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_file'])) {
    if (empty($_POST['title']) || empty($_POST['content'])) {
        echo "<script>alert('제목과 내용을 모두 입력해주세요.'); history.back();</script>";
        exit;
    }
    
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
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        // 파일 크기 검증
        if ($_FILES['file']['size'] > 10 * 1024 * 1024) {
            echo "<script>alert('파일 크기는 10MB 이하만 업로드 가능합니다.'); history.back();</script>";
            exit;
        }
        
        // 확장자 검증
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'];
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>alert('허용되지 않는 파일 형식입니다.'); history.back();</script>";
            exit;
        }
        
        // MIME 타입 검증
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detected_type = $finfo->file($_FILES['file']['tmp_name']);
        
        $allowed_types = [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain', 'application/zip'
        ];
        
        if (!in_array($detected_type, $allowed_types)) {
            echo "<script>alert('허용되지 않는 파일 형식입니다.'); history.back();</script>";
            exit;
        }
        
        $filename = basename($_FILES['file']['name']);
        $filedata = file_get_contents($_FILES['file']['tmp_name']);
        $filetype = $detected_type;
        
        // 파일 데이터 업데이트
        $stmt = mysqli_prepare($conn, "UPDATE board SET title = ?, content = ?, filename = ?, filedata = ?, filetype = ? WHERE id = ?");
        // 's'tring, 's'tring, 's'tring, 'b'lob, 's'tring, 'i'nteger
        mysqli_stmt_bind_param($stmt, "sssbsi", $title, $content, $filename, $filedata, $filetype, $id);
        
        // 파일 데이터가 있을 때만 send_long_data 실행
        if ($filedata !== null) {
            mysqli_stmt_send_long_data($stmt, 3, $filedata);
        }
    } else {
        // 파일 변경 없을 때
        $stmt = mysqli_prepare($conn, "UPDATE board SET title = ?, content = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $title, $content, $id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        echo "<script>alert('수정 완료'); location.href='view.php?id=" . $id . "';</script>";
        exit;
    } else {
        mysqli_stmt_close($stmt);
        echo "<script>alert('수정 실패: " . mysqli_error($conn) . "'); history.back();</script>";
        exit;
    }
}

mysqli_close($conn);
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
  <div class="myform-area">
    <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php" class="myform-btn">홈</a>
    <a href="mypage.php" class="myform-btn">마이페이지</a>
    <a href="board.php" class="myform-btn">게시판</a>
  </div>
    <div class="form-area">
      <div class="form-content">
        <h2>게시글 수정</h2>
      </div>

      <div class="form-input">
        <form method="post" enctype="multipart/form-data">
          <div class="form-group">
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required maxlength="200">
            <label>제목</label>
          </div>

          <div class="form-group">
            <textarea name="content" required placeholder="내용을 입력하세요" maxlength="5000"><?= htmlspecialchars($post['content']) ?></textarea>
          </div>

          <div class="file-upload">
            <label>첨부파일:</label>
            <?php if ($post['filename']): ?>
            <div class="existing-file" id="existingFile">
              <span>현재 파일: <?= htmlspecialchars($post['filename']) ?></span>
              <button type="button" class="delete-existing" onclick="deleteExistingFile()">삭제</button>
            </div>
            <?php endif; ?>
            <input type="file" name="file" id="fileInput" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip">
            <small style="color: #666;">새 파일을 선택하면 기존 파일이 교체됩니다 (최대 10MB)</small>
            <div class="file-info" id="fileInfo">
              <span id="fileName"></span>
              <button type="button" class="delete-file" onclick="deleteNewFile()">삭제</button>
            </div>
          </div>

          <div class="myform-button">
            <button class="myform-btn" type="submit">수정 완료</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('fileInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        if (file.size > 10 * 1024 * 1024) {
          alert('파일 크기 초과.');
          this.value = '';
          return;
        }
        
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileInfo').classList.add('active');
      }
    });
    
    function deleteNewFile() {
      document.getElementById('fileInput').value = '';
      document.getElementById('fileName').textContent = '';
      document.getElementById('fileInfo').classList.remove('active');
    }
    
    function deleteExistingFile() {
      if (confirm('기존 첨부파일을 삭제?')) {
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
            alert('파일 삭제 완료.');
          }
        })
        .catch(error => {
          alert('파일 삭제 중 오류발생.');
        });
      }
    }
  </script>
</body>
</html>