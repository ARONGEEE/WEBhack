<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='beforelogin.php';</script>";
    exit;
}

$user = $_SESSION['user'];
$conn = getDBConnection();

$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM board WHERE id = $id");
$post = mysqli_fetch_assoc($result); 

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php';</script>";
    exit;
}

if ($post['writer'] !== $user['id']) { 
    echo "<script>alert('본인 글만 삭제할 수 있습니다.'); location.href='board.php';</script>";
    exit;
}

if (mysqli_query($conn, "DELETE FROM board WHERE id = $id")) {
    echo "<script>alert('삭제 완료'); location.href='board.php';</script>";
} else {
    echo "<script>alert('삭제 실패'); history.back();</script>";
}

mysqli_close($conn);
?>