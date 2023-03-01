<?php
require_once __DIR__ . '/lib/functions.php';
init_yuploader();
require_logined_session();

$dbh = db_connect();
$store_st = $dbh->query("SELECT * FROM stores");
$cate_st = $dbh->query("SELECT * FROM categories");
$path_st = $dbh->query("SELECT * FROM all_categories");

$CSSES = [
    '/static/css/index.css'
];

header('Content-Type: text/html; charset=UTF-8');
include(__DIR__ . '/lib/header.php');
include(__DIR__ . '/lib/navbar.php');
include(__DIR__ . '/lib/form.php');
include(__DIR__ . '/lib/form_script.php');
include(__DIR__ . '/lib/funcs_script.php');
include(__DIR__ . '/lib/search_script.php');
include(__DIR__ . '/lib/upload_item_script.php');
include(__DIR__ . '/lib/footer.php');
