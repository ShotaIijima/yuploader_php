<?php

require_once __DIR__ . '/../lib/functions.php';
init_yuploader();

$dbh = db_connect();

$xml = new SimpleXMLElement(file_get_contents(__DIR__ . '/sample.xml'));
foreach ($xml->Result as $value) {
    echo sprintf("CategoryCode: %s, CategoryName: %s\n", $value->CategoryCode, $value->CategoryName);
}

/*
$stmt = $dbh->prepare("UPDATE user SET client_id = :client_id, secret = :secret WHERE name = :name");
$stmt->bindParam(':client_id', $_POST["client_id"], PDO::PARAM_STR);
$stmt->bindParam(':secret', $_POST["secret"], PDO::PARAM_STR);
$stmt->bindParam(':name', $_SESSION['user']['name'], PDO::PARAM_STR);
$res = $stmt->execute();
*/
