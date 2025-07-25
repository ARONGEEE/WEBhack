<?php
function getDBConnection() {
    $conn = mysqli_connect("localhost", "root", "", "test");
    
    if (!$conn) {
        die("DB 연결 실패: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8");
    
    return $conn;
}
?>