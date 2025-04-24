<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key; // 디코드할때 검증에 사용하는 key클래스호출

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

$conn = mysqli_connect("localhost", "root", "", "test");

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
  $pass = md5($_POST['pass']); // 입력한 비밀번호 md5 해싱

  $result = mysqli_query($conn, "SELECT * FROM `1111` WHERE id='$id'"); // 해당 id에 해당하는 유저 정보 불러오기
  $user = mysqli_fetch_assoc($result); //유저 정보 저장

  if ($user['pass'] === $pass) { // 비밀번호 일치 시
      $payload = [ //payload에
        'user' => $user, // 사용자 전체정보 담기
        'exp' => time() + 600 // 만료시간 연장
      ];
      
      $jwt = JWT::encode($payload, $key, 'HS256'); // 최종 토큰 생성
      header("Location: index.php?token=$jwt"); // index.php로 이동하면서 토큰 전달
      exit;
  } else {// 비밀번호가 다를경우
      echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>"; //이전 페이지로
      exit;
  }
}
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
      <h2>네번째 과제</h2>
      <p>게시판 구현[글 작성,읽기,리스트 보기,수정,삭제,검색(키워드),정렬(버튼),페이징].</p>
    </div>

    <!-- 입력 영역 -->
    <div class="form-input">
      <!-- 로그인 중인 아이디 표시 (이전 페이지에서 입력받은 ID) -->
      <h2>'<?= $id ?>'으로 로그인</h2>

      <!-- 비밀번호 입력 -->
      <form method="post" action="inputid.php?token=<?=($_GET['token']) ?>">
        <div class="form-group">
          <!-- 비밀번호 입력 칸 -->
          <input type="password" name="pass" required>
          <label>비밀번호</label>
        </div>

        <!-- 로그인 버튼 -->
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

