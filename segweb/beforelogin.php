<?php
require_once 'jwt/vendor/autoload.php'; // JWT 라이브러리 호출 (composer로 설치했었음)
use Firebase\JWT\JWT; //JWT함수클래스 호출 (C++NAMESPACE같은 개념)
// JWT 서명용 비밀 키 정의(학습용 웹페이지라서 config처리 안함)
$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';
$conn = mysqli_connect("localhost", "root", "", "test"); // 데이터베이스 연결 (localhost의 test DB 사용)

if (!$conn) {
    die("DB 연결 실패");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //로그인 폼을 제출했을 때
    // 입력값 검증
    if (empty($_POST['id'])) {
        echo "<script>alert('아이디를 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    // Prepared Statement로 SQL Injection 방어
    $stmtem = mysqli_prepare($conn, "SELECT id FROM `1111` WHERE id = ?");
    mysqli_stmt_bind_param($stmtem, "s", $_POST['id']);
    mysqli_stmt_execute($stmtem);
    $resultst = mysqli_stmt_get_result($stmtem);
    $rows = mysqli_fetch_assoc($resultst);
    
    if ($rows) {  // id가 존재하면
        $tempid = $rows['id'];
        $payload = [
          'id' => $tempid,          // 아이디만 담음
          'exp' => time() + 600     // 토큰 만료시간 10분 연장
        ];
       
        $jwt = JWT::encode($payload, $key, 'HS256'); // payload,key,알고리즘을 인코딩하여 토큰생성
        header("Location: inputid.php?token=" . urlencode($jwt)); //다음 페이지에 GET 파라미터로 토큰포함하여 이동
        exit;
    } else { //id가 존재안하면
        echo "<script>alert('존재하지 않는 아이디입니다.'); history.back();</script>"; //이전페이지로
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
      <!-- 아이디 입력 폼 (POST 방식으로 전송) -->
      <form method="post">
        <div class="form-group">
          <input type="text" name="id" required maxlength="50" pattern="[a-zA-Z0-9_]+" title="영문, 숫자, 언더스코어만 사용 가능">
          <label>아이디</label>
        </div>
        <!-- 다음 단계(inputid.php로 가는) 버튼 (PHP 상단에서 처리) -->
        <div class="myform-button">
          <button type="submit" class="myform-btn">다음</button>
        </div>
      </form>
      <div class="myform-button">
        <a href="register.php">
          <button class="myform-btn">회원가입</button>
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>