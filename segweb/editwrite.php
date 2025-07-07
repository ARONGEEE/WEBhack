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

// ID 검증 및 정수 변환
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='board.php?token=" . htmlspecialchars($_GET['token']) . "';</script>";
    exit;
}

$id = intval($_GET['id']);

// Prepared Statement로 게시글 조회
$stmt = mysqli_prepare($conn, "SELECT * FROM board WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . htmlspecialchars($_GET['token']) . "';</script>";
    exit;
}

if ($post['writer'] !== $user['id']) {
    echo "<script>alert('본인 글만 수정할 수 있습니다.'); location.href='board.php?token=" . htmlspecialchars($_GET['token']) . "';</script>";
    exit;
}

// 첨부파일 삭제 처리
if (isset($_POST['delete_file']) && $_POST['delete_file'] == '1') {
    if ($post['filepath'] && file_exists($post['filepath'])) {
        unlink($post['filepath']);
    }
    
    // Prepared Statement로 파일 정보 삭제
    $stmt_delete = mysqli_prepare($conn, "UPDATE board SET filename = '', filepath = '' WHERE id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);
    
    echo json_encode(['success' => true]);
    exit;
}

// 게시글 수정 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_file'])) {
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
    $filename = $post['filename'];  // 기존 파일명
    $filepath = $post['filepath'];  // 기존 파일경로
    
    // 파일 업로드 처리
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $upload_dir = 'filedir/';
        
        // 파일 크기 검증 (10MB 제한)
        if ($_FILES['file']['size'] > 10 * 1024 * 1024) {
            echo "<script>alert('파일 크기는 10MB 이하만 업로드 가능합니다.'); history.back();</script>";
            exit;
        }
        
        // 허용된 파일 확장자 검증
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'];
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "<script>alert('허용되지 않는 파일 형식입니다.'); history.back();</script>";
            exit;
        }
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // 기존 파일 삭제
        if ($filepath && file_exists($filepath)) {
            unlink($filepath);
        }
        
        $filename = basename($_FILES['file']['name']); // basename으로 경로 공격 방지
        $tmp_name = $_FILES['file']['tmp_name'];
        $filepath = $upload_dir . time() . '_' . $filename;
        
        if (!move_uploaded_file($tmp_name, $filepath)) {
            echo "<script>alert('파일 업로드 실패'); history.back();</script>";
            exit;
        }
    }
    
    $stmtem = mysqli_prepare($conn, "UPDATE board SET title = ?, content = ?, filename = ?, filepath = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmtem, "ssssi", $title, $content, $filename, $filepath, $id);
    
    if (mysqli_stmt_execute($stmtem)) {
        mysqli_stmt_close($stmtem);
        echo "<script>alert('수정 완료'); location.href='view.php?id=" . $id . "&token=" . htmlspecialchars($_GET['token']) . "';</script>";
        exit;
    } else {
        mysqli_stmt_close($stmtem);
        echo "<script>alert('수정 실패'); history.back();</script>";
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
    <a href="index.php?token=<?= htmlspecialchars($_GET['token']) ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= htmlspecialchars($_GET['token']) ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= htmlspecialchars($_GET['token']) ?>" class="myform-btn">게시판</a>
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
            <small style="color: #666;">새 파일을 선택하면 기존 파일이 교체됩니다 (최대 10MB, jpg/png/pdf/doc/txt/zip만 허용)</small>
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
        // 파일 크기 검증
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