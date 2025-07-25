<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='beforelogin.php';</script>";
    exit;
}

$user = $_SESSION['user'];
$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    $writer = $user['id'];
    $filename = '';
    $filedata = null;
    $filetype = '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        if ($_FILES['file']['size'] > 10 * 1024 * 1024) {
            echo "<script>alert('파일 크기는 10MB 이하만 업로드 가능합니다.'); history.back();</script>";
            exit;
        }
        
        //확장자체크
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'];
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>alert('허용되지 않는 파일 형식입니다.'); history.back();</script>";
            exit;
        }
        
        // MIME 타입체크
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
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO board (title, content, writer, filename, filedata, filetype) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssbs", $title, $content, $writer, $filename, $filedata, $filetype);
    
    if ($filedata !== null) {
        mysqli_stmt_send_long_data($stmt, 4, $filedata);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        echo "<script>alert('글 작성 완료'); location.href='board.php';</script>";
        exit;
    } else {
        mysqli_stmt_close($stmt);
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
        <a href="index.php" class="myform-btn">홈</a>
        <a href="mypage.php" class="myform-btn">마이페이지</a>
        <a href="board.php" class="myform-btn">게시판</a>
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
    document.getElementById('fileInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileInfo').classList.add('active');
      }
    });
    
    function deleteFile() {
      document.getElementById('fileInput').value = '';
      document.getElementById('fileName').textContent = '';
      document.getElementById('fileInfo').classList.remove('active');
    }
  </script>
</body>
</html>