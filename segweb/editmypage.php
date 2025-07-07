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
    echo "<script>alert('시간 땡.'); location.href='beforelogin.php';</script>";
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "test"); // DB 연결
if (!$conn) {
    die("DB 연결 실패");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // 사용자가 폼을 제출한 경우
    // 입력값 검증
    if (empty($_POST['pass']) || empty($_POST['newpass']) || empty($_POST['score']) || empty($_POST['location'])) {
        echo "<script>alert('모든 필드를 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    // 점수 범위 검증 (예: 0-100)
    $score = intval($_POST['score']);
    if ($score < 0 || $score > 1000) {
        echo "<script>alert('점수는 0-1000 범위 내에서 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $pass = md5($_POST['pass']); //확인차 입력한 현재 비밀번호 해싱하여 변수에담기
    $newpass = md5($_POST['newpass']); //새로운 비밀번호 해싱하여 변수에 담기
    $location = $_POST['location']; //수정한 주소 변수에담기
    
    if ($pass == $user['pass']) { // 기존 비밀번호 일치 시
        // Prepared Statement로 SQL Injection 방어
        $stmtem = mysqli_prepare($conn, "UPDATE `1111` SET pass = ?, score = ?, location = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmtem, "siss", $newpass, $score, $location, $user['id']);
        
        if (mysqli_stmt_execute($stmtem)) {//성공시
            mysqli_stmt_close($stmtem);
            
            // 수정된 유저정보 불러오기 (Prepared Statement 사용)
            $select_user = mysqli_prepare($conn, "SELECT * FROM `1111` WHERE id = ?");
            mysqli_stmt_bind_param($select_user, "s", $user['id']);
            mysqli_stmt_execute($select_user);
            $resultst = mysqli_stmt_get_result($select_user);
            $user = mysqli_fetch_assoc($resultst); // 유저정보 저장
            mysqli_stmt_close($select_user);
            
            $payload = [ //페이로드에
                'user' => $user, //유저정보 담기
                'exp' => time() + 600 // 시간연장
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            header("Location: mypage.php?token=" . urlencode($jwt));
            exit;
        } else {
            echo "<script>alert('수정실패'); history.back();</script>";
        }
    } else {
        echo "<script>alert('현재 비밀번호가 틀렸습니다.'); history.back();</script>";
        exit;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>내 정보 수정</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="myform-area">
    <div class="form-area">
      <div class="form-content">
        <h2>내 정보 수정</h2>
      </div>
      <div class="form-input">
        <form method="post">
          <div class="form-group">
            <input type="password" name="newpass" required maxlength="100" autocomplete="new-password">
            <label>새로운 비밀번호(변경 원치 않을시 현재비밀번호 입력)</label>
          </div>
          <!-- 현재 비밀번호 입력 (본인 확인용) -->
          <div class="form-group">
            <input type="password" name="pass" required maxlength="100" autocomplete="current-password">
            <label>현재 비밀번호</label>
          </div>
          <div class="form-group">
            <input type="number" name="score" required value="<?= htmlspecialchars($user['score']) ?>" min="0" max="1000">
            <label>점수</label>
          </div>
          <div class="form-group">
            <input type="text" name="location" required value="<?= htmlspecialchars($user['location']) ?>" maxlength="255">
            <label>주소</label>
          </div>
          <div class="myform-button">
            <button class="myform-btn" type="submit">수정하기</button>
          </div>
        </form>
        <div class="myform-button">
          <a href="mypage.php?token=<?= htmlspecialchars($_GET['token']) ?>">
            <button class="myform-btn">마이페이지로</button>
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>