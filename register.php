<?php
$conn = mysqli_connect("localhost", "root", "", "test"); // MySQL 데이터베이스에 연결 (호스트, 사용자명, 비밀번호, DB 이름)

if (!$conn) echo "DB 연결 실패"; // 연결 실패 시 메시지 출력

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // 요청 방식이 POST일때(아래HTML폼 제출시)
    $id = $_POST['id']; //입력한 ID 변수저장
    $pass = md5($_POST['pass']); //입력한 비밀번호 해시처리해서 변수저장
    $score = $_POST['score']; // 입력한 점수 저장
    $location = $_POST['location']; //입력한 주소(이메일아님) 저장

    $check = mysqli_query($conn, "SELECT * FROM `1111` WHERE id = '$id'"); // 입력한 ID가 DB에 이미 있는지 확인
    
    if (mysqli_num_rows($check) != 0) { //해당로우가 없는게 아니라면(이미 존재한다면) 
        echo "<script>alert('이미 존재하는 아이디입니다.'); history.back();</script>"; //이전 페이지로
        exit;
    }

    // 회원 정보 DB로 인서트 인덱스(서수)는 NULL로 자동 증가(A.I설정했으니),비밀번호,점수,아이디,가입날짜(시간까지),주소
    $sql = "INSERT INTO `1111` VALUES (NULL, '$pass', $score, '$id', DEFAULT, '$location')";

    if (mysqli_query($conn, $sql)) {  // 쿼리 성공 시 
        echo "<script>alert('회원가입 완료'); location.href='beforelogin.php';</script>"; // 로그인페이지로 이동
    } else { // 쿼리 실패 시
        echo "<script>alert('회원가입 실패: 서버 오류'); history.back();</script>"; // 서버 오류
    }
    exit;
}
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

      <!-- 입력 폼 -->
      <div class="form-input">
        <form method="post"> <!-- POST 방식으로 서버에 전달 -->

          <!-- 사용자 ID 입력 -->
          <div class="form-group">
            <input type="text" name="id" required>
            <label>아이디</label>
          </div>

          <!-- 비밀번호 입력 -->
          <div class="form-group">
            <input type="password" name="pass" required>
            <label>비밀번호</label>
          </div>

          <!-- 점수 입력 -->
          <div class="form-group">
            <input type="number" name="score" required>
            <label>점수</label>
          </div>

          <!-- 주소 입력 -->
          <div class="form-group">
            <input type="text" name="location" required>
            <label>주소</label>
          </div>

          <!-- 제출 버튼 -->
          <div class="myform-button">
            <button class="myform-btn" type="submit">가입하기</button>
          </div>
        </form>

        <!-- 로그인 페이지로 이동 버튼 -->
        <div class="myform-button">
          <a href="beforelogin.php">
            <button class="myform-btn">로그인으로</button>
          </a>
        </div>
      </div> <!-- .form-input 끝 -->

    </div> <!-- .form-area 끝 -->
  </div> <!-- .myform-area 끝 -->

</body>
</html>
