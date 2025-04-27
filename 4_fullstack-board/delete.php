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
    echo "<script>alert('유효하지 않은 토큰입니다.'); location.href='beforelogin.php';</script>";
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) die("DB 연결 실패");

$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM board WHERE id = $id"); // 게시글 존재 확인
$post = mysqli_fetch_assoc($result); 

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

// 해당 유저가 쓴 글이 아니라면(수정과 마찬가지로 view에서 해당유저가 쓴글이어야만 버튼이 표시되게끔은 함)
if ($post['writer'] !== $user['id']) { 
    echo "<script>alert('본인 글만 삭제할 수 있습니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

if (mysqli_query($conn, "DELETE FROM board WHERE id = $id")) { // 삭제 실행
    echo "<script>alert('삭제 완료'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
} else {
    echo "<script>alert('삭제 실패'); history.back();</script>";
}
?>
