<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key; // 디코드할때 검증에 사용하는 key클래스호출
$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';
$conn = mysqli_connect("localhost", "root", "", "test");

if (!$conn) {
    die("DB 연결 실패");
}

if (!isset($_GET['token'])) {// 토큰을 GET 파라미터로 받지 못했다면
    echo "<script>alert('토큰 못받음'); location.href='beforelogin.php';</script>";
    exit;
}

try {
    $decoded = JWT::decode($_GET['token'], new Key($key, 'HS256')); // 전달된 토큰을 decode하여
    $id = $decoded->id; //id 추출
} catch (Exception $e) { // 토큰만료(exp에 추가한 시간이 지나면)된 경우 로그인 페이지로 이동
    echo "<script>alert('시간떙'); location.href='beforelogin.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // 로그인 폼을 제출한 경우
    // 입력값 검증
    if (empty($_POST['pass'])) {
        echo "<script>alert('비밀번호를 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $pass = md5($_POST['pass']); // 입력한 비밀번호 md5 해싱
    
    // Prepared Statement로 SQL Injection 방어
    $stmtem = mysqli_prepare($conn, "SELECT * FROM `1111` WHERE id = ?");
    mysqli_stmt_bind_param($stmtem, "s", $id);
    mysqli_stmt_execute($stmtem);
    $resultst = mysqli_stmt_get_result($stmtem);
    $user = mysqli_fetch_assoc($resultst); //유저 정보 저장
    
    if ($user && $user['pass'] === $pass) { // 사용자 존재 여부 확인 후 비밀번호 일치 시
        $payload = [ //payload에
            'user' => $user, // 사용자 전체정보 담기
            'exp' => time() + 600 // 만료시간 연장
        ];
       
        $jwt = JWT::encode($payload, $key, 'HS256'); // 최종 토큰 생성
        header("Location: index.php?token=" . urlencode($jwt)); // index.php로 이동하면서 토큰 전달
        exit;
    } else {// 비밀번호가 다를경우 또는 사용자가 없을 경우
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>"; //이전 페이지로
        exit;
    }
    
    mysqli_stmt_close($stmtem);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>로그인 폼</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="myform-area">
  <div class="form-area">
   
    <div class="form-content">
      <h2>로그인</h2>
    </div>
    <div class="form-input">
      <h2>'<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>'으로 로그인</h2> <!--XSS방지-->
      <form method="post" action="inputid.php?token=<?= htmlspecialchars($_GET['token']) ?>">
        <div class="form-group">
          <input type="password" name="pass" required maxlength="100" autocomplete="current-password">
          <label>비밀번호</label>
        </div>
        <div class="myform-button">
          <button type="submit" class="myform-btn">로그인</button>
        </div>
      </form>
      <!-- 이전 페이지(로그아웃)로 돌아가는 버튼 -->
      <div class="myform-button" style="margin-top: 20px;">
        <a href="index.php?logout=1">
          <button class="myform-btn">이전페이지</button>
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>