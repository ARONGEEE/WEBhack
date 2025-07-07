<?php
$conn = mysqli_connect("localhost", "root", "", "test"); // MySQL 데이터베이스에 연결 (호스트, 사용자명, 비밀번호, DB 이름)
if (!$conn) {
    die("DB 연결 실패"); // 연결 실패 시 메시지 출력
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // 요청 방식이 POST일때(아래HTML폼 제출시)
    if (empty($_POST['id']) || empty($_POST['pass']) || empty($_POST['score']) || empty($_POST['location'])) {
        echo "<script>alert('모든 필드를 입력해주세요.'); history.back();</script>";
        exit;
    }
    
    $id = $_POST['id']; 
    $pass = $_POST['pass']; 
    $score = $_POST['score']; 
    $location = $_POST['location']; 
    
    // ID 형식 검증 (영어+숫자 / 3-20자)
    if (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $id)) {
        echo "<script>alert('id : 영어/숫자/_ 까지 허용 / 3-20자'); history.back();</script>";
        exit;
    }
    $score = intval($score);
    if ($score < 0 || $score > 100) {
        echo "<script>alert('점수 : 0-100 범위.'); history.back();</script>";
        exit;
    }
    
    $stmtem = mysqli_prepare($conn, "SELECT id FROM `1111` WHERE id = ?");
    mysqli_stmt_bind_param($stmtem, "s", $id);
    mysqli_stmt_execute($stmtem);
    $resultst = mysqli_stmt_get_result($stmtem);
   
    if (mysqli_num_rows($resultst) != 0) { //해당로우가 없는게 아니라면(이미 존재한다면)
        mysqli_stmt_close($stmtem);
        echo "<script>alert('이미 존재하는 아이디입니다.'); history.back();</script>"; //이전 페이지로
        exit;
    }
    mysqli_stmt_close($stmtem);
    
    // 비밀번호 해시처리
    $pass_hashed = md5($pass);
    
    // Prepared Statement로 회원 정보 INSERT
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO `1111` VALUES (NULL, ?, ?, ?, DEFAULT, ?)");
    mysqli_stmt_bind_param($stmt_insert, "siss", $pass_hashed, $score, $id, $location);
    
    if (mysqli_stmt_execute($stmt_insert)) {  // 쿼리 성공 시
        mysqli_stmt_close($stmt_insert);
        echo "<script>alert('회원가입 완료'); location.href='beforelogin.php';</script>"; // 로그인페이지로 이동
    } else { // 쿼리 실패 시
        mysqli_stmt_close($stmt_insert);
        echo "<script>alert('회원가입 실패: 서버 오류'); history.back();</script>"; // 서버 오류
    }
    exit;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>회원가입</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="myform-area">
    <div class="form-area">
      <div class="form-content">
        <h2>회원가입</h2>
        <p>가입 하기</p>
      </div>
      <div class="form-input">
        <form method="post">
          <div class="form-group">
            <input type="text" name="id" required maxlength="20" pattern="[a-zA-Z0-9_]+" title="영문, 숫자, 언더스코어만 사용 가능 (3-20자)">
            <label>아이디</label>
          </div>
          <div class="form-group">
            <input type="password" name="pass" required minlength="4" maxlength="100" autocomplete="new-password">
            <label>비밀번호</label>
          </div>
          <div class="form-group">
            <input type="number" name="score" required min="0" max="1000">
            <label>점수</label>
          </div>
          <div class="form-group">
            <input type="text" name="location" required maxlength="255">
            <label>주소</label>
          </div>
          <div class="myform-button">
            <button class="myform-btn" type="submit">가입하기</button>
          </div>
        </form>
        <div class="myform-button">
          <a href="beforelogin.php">
            <button class="myform-btn">로그인으로</button>
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>