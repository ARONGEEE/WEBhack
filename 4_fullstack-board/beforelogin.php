<?php
require_once 'jwt/vendor/autoload.php'; // JWT 라이브러리 호출 (composer로 설치했었음)
use Firebase\JWT\JWT; //JWT함수클래스 호출 (C++NAMESPACE같은 개념)

// JWT 서명용 비밀 키 정의(학습용 웹페이지라서 config처리 안함)
$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

$conn = mysqli_connect("localhost", "root", "", "test"); // 데이터베이스 연결 (localhost의 test DB 사용)

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //로그인 폼을 제출했을 때
    $result = mysqli_query($conn, "SELECT * FROM `1111` WHERE id='$_POST[id]'"); // 해당 id가 DB에 존재하는지 확인
    $tempid = mysqli_fetch_assoc($result)['id'];

    if ($tempid) {  // id가 존재하면
        $payload = [
          'id' => $tempid,          // 아이디만 담음
          'exp' => time() + 600     // 토큰 만료시간 10분 연장
        ];
        
        $jwt = JWT::encode($payload, $key, 'HS256'); // payload,key,알고리즘을 인코딩하여 토큰생성
        header("Location: inputid.php?token=$jwt"); //다음 페이지에 GET 파라미터로 토큰포함하여 이동
        exit;
    } else { //id가 존재안하면
        echo "<script>alert('존재하지 않는 아이디입니다.'); history.back();</script>"; //이전페이지로
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ko"> <!-- 문서언어 한국어설정 -->
<head>
  <meta charset="UTF-8"> <!-- 문자 인코딩 UTF-8 -->
  <title>로그인 폼</title> <!-- 브라우저 탭 제목 -->
  <link rel="stylesheet" href="login.css"> <!-- 로그인 스타일시트 적용 -->
</head>
<body>

<!-- 전체집합 폼 -->
<div class="myform-area">
  <div class="form-area"> 

    <!-- 상단설명 폼 -->
    <div class="form-content">
      <h2>네번째 과제</h2>
      <p>게시판 구현[글 작성,읽기,리스트 보기,수정,삭제,검색(키워드),정렬(버튼),페이징].</p>
    </div>

    <div class="form-input">
      <h2>로그인</h2>

      <!-- 아이디 입력 폼 (POST 방식으로 전송) -->
      <form method="post">
        <div class="form-group">
          <!-- 아이디 입력 칸 -->
          <input type="text" name="id" required>
          <label>아이디</label>
        </div>

        <!-- 다음 단계(inputid.php로 가는) 버튼 (PHP 상단에서 처리) -->
        <div class="myform-button">
          <button type="submit" class="myform-btn">다음</button>
        </div>
      </form>

      <!-- 회원가입 페이지로 가는 버튼 -->
      <div class="myform-button">
        <a href="register.php">
          <button class="myform-btn">회원가입</button>
        </a>
      </div>
    </div> <!-- .form-input 끝 -->

  </div> <!-- .form-area 끝 -->
</div> <!-- .myform-area 끝 -->

</body>
</html>
