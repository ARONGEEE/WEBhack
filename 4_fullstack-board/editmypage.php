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
if (!$conn) echo "DB 연결 실패";

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // 사용자가 폼을 제출한 경우
    $pass = md5($_POST['pass']); //확인차 입력한 현재 비밀번호 해싱하여 변수에담기
    $newpass = md5($_POST['newpass']); //새로운 비밀번호 해싱하여 변수에 담기
    $score = $_POST['score']; //수정한 점수 변수에담기
    $location = $_POST['location']; //수정한 주소 변수에담기

    if ($pass == $user['pass']) { // 기존 비밀번호 일치 시
        // 사용자 정보 업데이트 쿼리(수정할 칼럼만 수정내용작성)
        $sql = "UPDATE `1111` SET pass='$newpass', score=$score, address='$location' WHERE id='{$user['id']}'";

        if (mysqli_query($conn, $sql)) {//성공시
            $result = mysqli_query($conn, "SELECT * FROM `1111` WHERE id='$user[id]'");//수정된 유저정보 불러오기
            $user= mysqli_fetch_assoc($result); // 유저정보 저장
            $payload = [ //페이로드에
                'user' => $user, //유저정보 담기
                'exp' => time() + 600 // 시간연장
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            header("Location: mypage.php?token=$jwt");
            exit;
        } else {
            echo "<script>alert('수정실패'); history.back();</script>";
        }
    }else{
        echo "<script>alert('현재 비밀번호가 틀렸습니다.'); history.back();</script>";
        exit;
    }

    exit;
}
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
      <!-- 상단 제목 -->
      <div class="form-content">
        <h2>내 정보 수정</h2>
      </div>

      <!-- 입력 폼 -->
      <div class="form-input">
        <form method="post"> <!-- POST 방식으로 제출 -->

          <!-- 새로운 비밀번호 입력 -->
          <div class="form-group">
            <input type="password" name="newpass" required>
            <label>새로운 비밀번호</label>
          </div>

          <!-- 현재 비밀번호 입력 (본인 확인용) -->
          <div class="form-group">
            <input type="password" name="pass" required>
            <label>현재 비밀번호</label>
          </div>

          <!-- 점수 입력(기존 점수보임) -->
          <div class="form-group">
            <input type="number" name="score" required value="<?= $user['score'] ?>">
            <label>점수</label>
          </div>

          <!-- 주소 입력 (기존 주소보임) -->
          <div class="form-group">
            <input type="text" name="location" required value="<?= $user['address'] ?>">
            <label>주소</label>
          </div>

          <!-- 제출 버튼 -->
          <div class="myform-button">
            <button class="myform-btn" type="submit">수정하기</button>
          </div>

        </form>

        <!-- 마이페이지로 돌아가는 버튼 -->
        <div class="myform-button">
          <a href="mypage.php?token=<?= $_GET['token'] ?>">
            <button class="myform-btn">마이페이지로</button>
          </a>
        </div>

      </div> <!-- .form-input 끝 -->

    </div> <!-- .form-area 끝 -->
  </div> <!-- .myform-area 끝 -->

</body>
</html>

