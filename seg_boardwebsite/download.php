<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user'])) {
    die("로그인이 필요합니다.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("잘못된 요청입니다.");
}

$conn = getDBConnection();
$id = intval($_GET['id']);

$stmt = mysqli_prepare($conn, "SELECT filename, filedata, filetype FROM board WHERE id = ? AND filename != ''");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$file = mysqli_fetch_assoc($result);

if (!$file) {
    die("파일을 찾을 수 없습니다.");
}

header('Content-Type: ' . $file['filetype']);
header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
header('Content-Length: ' . strlen($file['filedata']));
header('Cache-Control: no-cache');

echo $file['filedata'];

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;
?>