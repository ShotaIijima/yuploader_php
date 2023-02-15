<?php
require_once __DIR__ . '/lib/functions.php';
init_yuploader();
require_unlogined_session();

$dbh = db_connect();

// 事前に生成したユーザごとのパスワードハッシュの配列
$hashes = [
    'tbrnk844' => '$2y$10$SGEUl5kpa6jpGJ5/CuqeSu7UE9rr.IOZZ5VtNQ5MAMSPE.zdxXG9i',
];

// ユーザから受け取ったユーザ名とパスワード
$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

$rows = $dbh->prepare("SELECT * FROM users WHERE name = ?");
$rows->execute([$username]);
$row = $rows->fetch();

// POSTメソッドのときのみ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        validate_token(filter_input(INPUT_POST, 'token')) &&
        password_verify($password, $row['password'])
    ) {
        // 認証が成功したとき
        // セッションIDの追跡を防ぐ
        session_regenerate_id(true);
        // ユーザ名をセット
        $_SESSION['user'] = $row;
        // ログイン完了後に / に遷移
        header('Location: /');
        exit;
    }
    // 認証が失敗したとき
    // 「403 Forbidden」
    http_response_code(403);
}

header('Content-Type: text/html; charset=UTF-8');
$CSSES = [
    '/static/css/login.css'
];
include(__DIR__ . '/lib/header.php');
?>

<form method="post" class="form-signin">
  <h1 class="h3 mb-3 font-weight-normal">YUploader</h1>
  <label for="inputUsername" class="sr-only">ユーザ名</label>
  <input type="text" name="username" id="inputEmail" class="form-control" required autofocus>
  <label for="inputPassword" class="sr-only">パスワード</label>
  <input type="password" name="password" id="inputPassword" class="form-control" required>
  <input type="hidden" name="token" value="<?=h(generate_token())?>">
  <button class="btn btn-lg btn-primary btn-block" type="submit">ログイン</button>
  <?php if (http_response_code() === 403): ?>
  <p style="color: red;">ユーザ名またはパスワードが違います</p>
  <?php endif; ?>
  <p class="mt-5 mb-3 text-muted">shiijima&copy; 2017-<?=date('Y')?></p>
</form>

<?php include(__DIR__ . '/lib/footer.php'); ?>