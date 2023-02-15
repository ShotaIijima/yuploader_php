<?php
require_once __DIR__ . '/lib/functions.php';
init_yuploader();
require_logined_session();

$dbh = db_connect();

$pcate_st = $dbh->query("SELECT * FROM product_cates");

header('Content-Type: text/html; charset=UTF-8');
include(__DIR__ . '/lib/header.php');
include(__DIR__ . '/lib/navbar.php');

?>

<h1>プロダクトカテゴリ一覧</h1>
<hr />
<table class="table">
  <thead>
    <tr>
      <th scope="col">id</th>
      <th scope="col">プロダクトカテゴリ名</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $pcate_st->fetch(PDO::FETCH_ASSOC)){ ?>
    <tr>
      <th scope="row"><?=h($row['id'])?></th>
      <td><?=h($row['name'])?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<?php include(__DIR__ . '/lib/footer.php'); ?>