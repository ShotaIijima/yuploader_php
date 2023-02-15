<?php
require_once __DIR__ . '/lib/functions.php';
init_yuploader();
require_logined_session();

$dbh = db_connect();

$brand_st = $dbh->query("SELECT id, path_name FROM brand_codes GROUP BY id, path_name");

header('Content-Type: text/html; charset=UTF-8');
include(__DIR__ . '/lib/header.php');
include(__DIR__ . '/lib/navbar.php');

?>

<h1>ブランドコード一覧</h1>
<hr />
<table class="table">
  <thead>
    <tr>
      <th scope="col">id</th>
      <th scope="col">ブランドコード名</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $brand_st->fetch(PDO::FETCH_ASSOC)){ ?>
    <tr>
      <th scope="row"><?=h($row['id'])?></th>
      <td><?=h($row['path_name'])?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<?php include(__DIR__ . '/lib/footer.php'); ?>