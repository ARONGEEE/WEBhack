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
if (!$conn) die("DB 연결 실패");

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;  // 페이지 번호 계산
$limit = 10;                       // 페이지당 글 수
$start = ($page - 1) * $limit;     // LIMIT 쿼리 : 시작 위치 계산

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';  // 검색어 escape
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';     // 정렬방식

$where = $search ? "WHERE title LIKE '%$search%'" : '';  // 키워드 탐색 (%키워드%)

$sql_total = "SELECT COUNT(*) AS total FROM board $where"; //총 게시글 수 조회
$total_result = mysqli_fetch_assoc(mysqli_query($conn, $sql_total));
$total_posts = $total_result['total'];
$total_pages = ceil($total_posts / $limit);  // 총 페이지 수

// 게시글 목록 조회 쿼리 실행(제목,작성자,작성날짜(시간까지)
$sql = "SELECT id, title, writer, created_at FROM board $where ORDER BY id $order LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>게시판</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="myform-area">
  <!-- 상단 탭-->
  <div class="myform-button" style="display: flex; justify-content: center; gap: 10px; padding: 20px 0;">
    <a href="index.php?token=<?= $_GET['token'] ?>" class="myform-btn">홈</a>
    <a href="mypage.php?token=<?= $_GET['token'] ?>" class="myform-btn">마이페이지</a>
    <a href="board.php?token=<?= $_GET['token'] ?>" class="myform-btn">게시판</a>
  </div>

  <div class="form-area" style="width: 90%; height: auto;">
    <div class="form-content" style="width: 100%; text-align: center;">
      <h2>게시판 글 목록</h2>
    </div>

    <div class="form-input" style="width: 100%;">
      <!-- 검색 정렬 폼-->
      <div class="search-box">
        <form method="get" action="board.php">
          <input type="hidden" name="token" value="<?= $_GET['token'] ?>">

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

      <!-- 글쓰기 버튼 -->
      <div class="btn-right">
        <a href="write.php?token=<?= $_GET['token'] ?>" class="btn-small-red">글쓰기</a>
      </div>

      <!-- 게시글 목록 -->
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
            <!-- 제목 클릭시 글보기 -->
            <td><a href="view.php?id=<?= $row['id'] ?>&token=<?= $_GET['token'] ?>"><?= htmlspecialchars($row['title']) ?></a></td>
            <td><?= htmlspecialchars($row['writer']) ?></td>
            <td><?= $row['created_at'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <!-- 페이징 -->
      <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="board.php?token=<?= $_GET['token'] ?>&page=<?= $i ?>&search=<?= urlencode($search) ?>&order=<?= strtolower($order) ?>"
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