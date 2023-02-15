<?php
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/searcher.php';
init_yuploader();
require_logined_session();

header("Content-Type: application/json; charset=UTF-8");

// POSTメソッドじゃなかったらエラー
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logging('this is not POST method');
    $_SESSION['error_str'] = 'POSTではありません。';
    http_response_code(403);
    echo res_json(NULL);
    exit;
}

logging(var_export($_POST, true));

$ress = Searcher::do_search($_POST['store_id']);

logging("==========rmss==========");
logging(var_export($ress, true));
logging("==========rmss==========");

echo res_json($ress);
