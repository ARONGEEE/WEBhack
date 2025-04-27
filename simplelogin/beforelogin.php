<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>로그인 폼</title>
  <link rel="stylesheet" href="login.css">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">
</head>
<body>

<div class="myform-area">
  <div class="form-area">
    
    <div class="form-content">
      <h2>첫번째 과제</h2>
      <p>DB없이 간단한 로그인 페이지 만들어보기.</p>
    </div>

    <div class="form-input">
      <h2>로그인</h2>
      <form method="post" action="afterlogin.php">
        <div class="form-group">
          <input type="text" name="id">
          <label>아이디</label>
        </div>
        <div class="form-group">
          <input type="password" name="pwd">
          <label>비밀번호</label>
        </div>
        <div class="myform-button">
          <button type="submit" class="myform-btn">로그인</button>
        </div>
      </form>
    </div>

  </div>
</div>

</body>
</html>
