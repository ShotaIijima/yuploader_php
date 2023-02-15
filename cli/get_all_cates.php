<?php

require_once __DIR__ . '/../lib/functions.php';
init_yuploader();

$dbh = db_connect();

$rows = $dbh->prepare("SELECT * FROM users WHERE name = ?");
$rows->execute(['tbrnk844']);
$row = $rows->fetch();

$header = [
    'GET /ShoppingWebService/V1/getShopCategory?seller_id=primopasso HTTP/1.1',
    'Host: circus.shopping.yahooapis.jp',
    'Authorization: Bearer ' . $row['access_token']
];

$url = 'https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/getShopCategory?seller_id=primopasso';

// 必要に応じてオプションを追加してください。
$ch = curl_init();
curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
curl_setopt($ch, CURLOPT_URL,            $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$xml = new SimpleXMLElement(file_get_contents(__DIR__ . '/sample.xml'));
foreach ($xml->Result as $value) {
    echo sprintf("CategoryCode: %s, CategoryName: %s\n", $value->CategoryCode, $value->CategoryName);
    $stmt = $dbh->prepare("INSERT INTO categories VALUES (:id, :name)");
    $stmt->bindParam(':id', $value->CategoryCode, PDO::PARAM_INT);
    $stmt->bindParam(':name', $value->CategoryName, PDO::PARAM_STR);
    $res = $stmt->execute();
}
