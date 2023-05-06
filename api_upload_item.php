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

$ys = YahooService::get_instance();

$res1 = $ys->upload_item($datas, $_POST['imgs']);
$res1_xml = simplexml_load_string($res1);
if ($res1_xml->Message) {
    echo res_json([
        'error' => strval($res1_xml->Message)
    ]);
    exit(1);
}

if (strval($res1_xml->Result->Status) === 'NG') {
    $errors = [];
    foreach ($res1_xml->Result->Error as $error) {
        $errors[] = $error->Message[0];
    }
    echo res_json([
        'error' => implode(',', $errors)
    ]);
    exit(2);
}

logging(var_export($res1, true));
logging(var_export($res1_xml, true));

$res2 = $ys->regist_all_images($datas, $_POST['imgs']);
$res2_xml = simplexml_load_string($res2);
if ($res2_xml->Message) {
    echo res_json([
        'error' => strval($res2_xml->Message)
    ]);
    exit(1);
}

if (strval($res2_xml->Result->Status) === 'NG') {
    $errors = [];
    foreach ($res2_xml->Result->Error as $error) {
        $errors[] = $error->Message[0];
    }
    echo res_json([
        'error' => implode(',', $errors)
    ]);
    exit(2);
}

logging(var_export($res2, true));
logging(var_export($res2_xml, true));

$res3 = $ys->set_stock($datas);
$res3_xml = simplexml_load_string($res3);
if ($res3_xml->Error) {
    echo res_json([
        'error' => strval($res2_xml->Error)
    ]);
    exit(1);
}

logging(var_export($res3, true));
logging(var_export($res3_xml, true));

echo res_json(null);

return;
