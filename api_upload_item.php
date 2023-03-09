<?php
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/yahoo_service.php';
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

$datas = [];

foreach ($_POST['datas'] as &$data) {
    if (in_array($data['name'], ['price'], true)) {
        $data['value'] = intval($data['value']);
    }
    $datas[$data['name']] = $data['value'];
}

logging(var_export($datas, true));
echo res_json(null);

return;

$ys = YahooService::get_instance();
$res1 = $ys->upload_item($datas, $_POST['imgs']);

echo res_json(null);

return;
