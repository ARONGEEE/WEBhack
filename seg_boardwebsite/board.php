<?php
session_start();
require_once 'dbconfig.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='beforelogin.php';</script>";
    exit;
}

$user = $_SESSION['user'];
$conn = getDBConnection();

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

if ($search) {
    $sqlq = "SELECT COUNT(*) AS total FROM board WHERE title LIKE ?";
    $statem = mysqli_prepare($conn, $sqlq);
    $search_param = "%$search%";
    mysqli_stmt_bind_param($statem, "s", $search_param);
    mysqli_stmt_execute($statem);
    $resultst = mysqli_fetch_assoc(mysqli_stmt_get_result($statem));
    $rows = $resultst['total'];
    mysqli_stmt_close($statem);
    
    $sql = "SELECT id, title, writer, created_at, filename FROM board WHERE title LIKE ? ORDER BY id $order LIMIT ?, ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $search_param, $start, $limit);
} else {
    $sqlq = "SELECT COUNT(*) AS total FROM board";
    $resultst = mysqli_fetch_assoc(mysqli_query($conn, $sqlq));
    $rows = $resultst['total'];
    
    $sql = "SELECT id, title, writer, created_at, filename FROM board ORDER BY id $order LIMIT ?, ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $start, $limit);
}

$total_pages = ceil($rows / $limit);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>게시판</title>
  <link rel="stylesheet" href="login.css">
  <link rel="stylesheet" href="board-style.css">
</head>
<body>
<div class="myform-area">
  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php" class="myform-btn">홈</a>
    <a href="mypage.php" class="myform-btn">마이페이지</a>
    <a href="board.php" class="myform-btn">게시판</a>
  </div>

  <div class="form-area" style="width: 90%; height: auto;">
    <div class="form-content" style="width: 100%; text-align: center;">
      <h2>게시판 글 목록</h2>
    </div>

    <div class="form-input" style="width: 100%;">
      <div class="search-box">
        <form method="get" action="board.php">
          <div class="search-top">
            <input type="text" name="search" placeholder="제목 검색" value="<?= htmlspecialchars($search) ?>">
          </div>

          <div class="search-bottom">
            <select name="order">
              <option value="desc" <?= $order === 'DESC' ? 'selected' : '' ?>>최신순</option>
              <option value="asc" <?= $order === 'ASC' ? 'selected' : '' ?>>오래된순</option>
            </select>
            <button type="submit">검색</button>
          </div>
        </form>
      </div>

      <div class="btn-right">
        <a href="write.php" class="btn-small-red">글쓰기</a>
      </div>

      <table>
        <thead>
          <tr>
            <th>제목</th>
            <th>작성자</th>
            <th>작성일</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td>
              <div class="title-with-file">
                <a href="view.php?id=<?= intval($row['id']) ?>"><?= htmlspecialchars($row['title']) ?></a>
                <?php if ($row['filename']): ?>
                  <span class="file-icon" title="첨부파일 있음">📎</span>
                <?php endif; ?>
              </div>
            </td>
            <td><?= htmlspecialchars($row['writer']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="board.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&order=<?= strtolower($order) ?>"
             class="<?= $i == $page ? 'current' : '' ?>">
             <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>