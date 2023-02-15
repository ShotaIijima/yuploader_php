<?php
require_once __DIR__ . '/lib/functions.php';
init_yuploader();
require_logined_session();

$dbh = db_connect();

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logging('POST params: ' . var_export($_POST, true));
    // $ERRORS[] = "sssssssssss";
    try {
        $stmt = $dbh->prepare("UPDATE users SET client_id = :client_id, secret = :secret WHERE name = :name");
        $stmt->bindParam(':client_id', $_POST["client_id"], PDO::PARAM_STR);
        $stmt->bindParam(':secret', $_POST["secret"], PDO::PARAM_STR);
        $stmt->bindParam(':name', $_SESSION['user']['name'], PDO::PARAM_STR);
        $res = $stmt->execute();
        $stmt2 = $dbh->prepare("DELETE FROM calc_rules");
        $res = $stmt2->execute();
        $calc_len = count($_POST["calc_from"]);
        for ($i=0;$i<$calc_len;$i++) {
          if (is_numeric($_POST["calc_from"][$i])) {
            $stmt3 = $dbh->prepare("INSERT INTO calc_rules VALUES (:calc_from, :calc_to, :calc_bai, :calc_tasu)");
            $stmt3->bindParam(':calc_from', $_POST["calc_from"][$i]);
            $stmt3->bindParam(':calc_to', $_POST["calc_to"][$i]);
            $stmt3->bindParam(':calc_bai', $_POST["calc_bai"][$i]);
            $stmt3->bindParam(':calc_tasu', $_POST["calc_tasu"][$i]);
            $res = $stmt3->execute();
          }
        }
        $rows = $dbh->prepare("SELECT * FROM users WHERE name = ?");
        $rows->execute([$_SESSION['user']['name']]);
        $row = $rows->fetch();
        $_SESSION['user'] = $row;
        $INFOS[] = "更新完了しました";
    } catch(Exception $e) {
        logging('exception: ' . $e);
        $ERRORS[] = "エラーが発生しました。管理者にお問い合わせください。";
    }
}

$crule_st = $dbh->query("SELECT * FROM calc_rules ORDER BY from_price ASC");

$CSSES = [
    '/static/css/index.css'
];

header('Content-Type: text/html; charset=UTF-8');
include(__DIR__ . '/lib/header.php');
include(__DIR__ . '/lib/navbar.php');

?>

<form id="upload-form" method="post">
  <div class="form-inline">
    <div class="form-group mr-3">
      <label for="client_id">Client ID</label>
      <input type="text" style="width: 600px" name="client_id" class="form-control" value="<?=$_SESSION['user']['client_id']?>">
    </div>
  </div>
  <div class="form-inline">
    <div class="form-group mr-3">
      <label for="secret">シークレット</label>
      <input type="text" style="width: 500px" name="secret" class="form-control" value="<?=$_SESSION['user']['secret']?>">
    </div>
  </div>
  <label for="calc">金額ルール</label>
  <div style="border: solid grey">
    <?php while($row = $crule_st->fetch(PDO::FETCH_ASSOC)){ ?>
    <div class="form-group mr-3">
      <div class="form-inline">
        <input type="text" name="calc_from[]" class="form-control" value="<?=$row['from_price']?>">
        ～
        <input type="text" name="calc_to[]" class="form-control" value="<?=$row['to_price']?>">
        なら
        <input type="text" name="calc_bai[]" class="form-control" value="<?=$row['bai']?>">
        倍プラス
        <input type="text" name="calc_tasu[]" class="form-control" value="<?=$row['tasu']?>">
      </div>
    </div>
    <br>
    <?php } ?>
    <div class="form-group mr-3">
      <div class="form-inline">
        <input type="text" name="calc_from[]" class="form-control" value="">
        ～
        <input type="text" name="calc_to[]" class="form-control" value="">
        なら
        <input type="text" name="calc_bai[]" class="form-control" value="">
        倍プラス
        <input type="text" name="calc_tasu[]" class="form-control" value="">
      </div>
    </div>
    <br>
    <div class="form-group mr-3">
      <div class="form-inline">
        <input type="text" name="calc_from[]" class="form-control" value="">
        ～
        <input type="text" name="calc_to[]" class="form-control" value="">
        なら
        <input type="text" name="calc_bai[]" class="form-control" value="">
        倍プラス
        <input type="text" name="calc_tasu[]" class="form-control" value="">
      </div>
    </div>
    <br>
  </div>
  <div class="form-inline">
    <div class="form-group mr-3">
      <button type="submit" class="btn btn-primary">更新</button>
    </div>
  </div>
</form>

<?php include(__DIR__ . '/lib/footer.php'); ?>