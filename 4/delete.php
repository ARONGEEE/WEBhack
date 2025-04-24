<?php
require_once 'jwt/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'WhiteshirtnowredmybloodynoseSleepinyoureonyoutippytoesCreepinaroundlikenooneknows';

if (!isset($_GET['token']) || !isset($_GET['id'])) {
    header("Location: beforelogin.php");
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

// 글 확인
$result = mysqli_query($conn, "SELECT * FROM board WHERE id = $id");
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<script>alert('존재하지 않는 글입니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

if ($post['writer'] !== $user['id']) {
    echo "<script>alert('본인 글만 삭제할 수 있습니다.'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
    exit;
}

// 삭제 실행
if (mysqli_query($conn, "DELETE FROM board WHERE id = $id")) {
    echo "<script>alert('삭제 완료'); location.href='board.php?token=" . $_GET['token'] . "';</script>";
} else {
    echo "<script>alert('삭제 실패'); history.back();</script>";
}
