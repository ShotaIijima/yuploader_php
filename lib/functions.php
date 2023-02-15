<?php

$DBH = null;

$SUFFS = [
    "jpg",
    "gif",
    "jpe",
    "jpeg"
];

function init_yuploader()
{
    define("YUPLOADER_APP_HOME", __DIR__ . '/..');
    define("YUPLOADER_LOG_FILE", YUPLOADER_APP_HOME . '/log/yuploader' . date('Ymd') . '.log');
    define("YUPLOADER_DEBUG", true);
    // define("YUPLOADER_DEBUG", false);
}

/**
 * ログイン状態によってリダイレクトを行うsession_startのラッパー関数
 * 初回時または失敗時にはヘッダを送信してexitする
 */
function require_unlogined_session()
{
    // セッション開始
    @session_start();
    // ログインしていれば / に遷移
    if (isset($_SESSION['username'])) {
        header('Location: /');
        exit;
    }
}

function require_logined_session()
{
    // セッション開始
    @session_start();
    // ログインしていなければ /login.php に遷移
    if (!isset($_SESSION['user'])) {
        if (str_starts_with($_SERVER["REQUEST_URI"], '/api_')) {
            echo json_encode([
                "redirect_to" => "/login.php"
            ]);
        } else {
            header('Location: /login.php', true, 302);
        }
        exit;
    }
}

/**
 * CSRFトークンの生成
 *
 * @return string トークン
 */
function generate_token()
{
    // セッションIDからハッシュを生成
    return hash('sha256', session_id());
}

/**
 * CSRFトークンの検証
 *
 * @param string $token
 * @return bool 検証結果
 */
function validate_token($token)
{
    // 送信されてきた$tokenがこちらで生成したハッシュと一致するか検証
    return $token === generate_token();
}

/**
 * htmlspecialcharsのラッパー関数
 *
 * @param string $str
 * @return string
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}


function logging($str)
{
    $bt = debug_backtrace()[0];
    $content = date('[Y-m-d H:i:s] ') . session_id() . ' ' . $bt['file'] . '(' . $bt['line'] . ') ' . $str;
    file_put_contents(YUPLOADER_LOG_FILE, $content . "\n", FILE_APPEND);
}

function _debug($str) {
    if (YUPLOADER_DEBUG) logging($str);
}

function _debug_obj($obj) {
    if (YUPLOADER_DEBUG) logging(var_export($obj, true));
}

function db_connect()
{
    global $DBH;
    // DBへ接続
    try {
        if ($DBH == null) {
          include(__DIR__ . '/../conf/db.php');
          $DBH = new PDO($DBCONF['dsn'], $DBCONF['user'], $DBCONF['pass']);
        }
    } catch(PDOException $e) {
        print("データベースの接続に失敗しました".$e->getMessage());
        die();
    } finally {
        return $DBH;
    }
}

function is_checkbox_on($str)
{
    return key_exists($str, $_POST) && $_POST[$str] === '1';
}

function removeAttrsHTMLTag($str)
{
    $parsed = str_replace(array("\r\n", "\r", "\n"), '', $str);
    $parsed = preg_replace("/\s+/", "", $parsed);
    $parsed = preg_replace("/<[^>]*?>/", " ", $parsed);
    $parsed = preg_replace("/<![^>]*?>/", "", $parsed);
    $parsed = preg_replace("/\s+/", " ", $parsed);
    $parsed = preg_replace("/-->/", "", $parsed);
    return $parsed;
}

function removeAttrs($str)
{
    $parsed = str_replace(array("\r\n", "\r", "\n"), '', $str);
    $parsed = preg_replace("/\s{1,1}\w+=\".*\"/", "", $parsed);
    return $parsed;
}

function validateImgUrl($url)
{
    $sfx = splitted_array_last('.', $url);
    $sfx = mb_strtolower($sfx);
    global $SUFFS;
    foreach ($SUFFS as $suff)
    {
        if ($sfx === $suff)
            return true;
    }
    logging("ValidateImgUrl NG: suff is " . $sfx);
    return false;
}

function splitted_array_last($sep, $str)
{
    $arr = explode($sep, $str);
    return $arr[count($arr) - 1];
}

function getItemCodeSuf()
{
    return date('Ymdhis') .  '-' . makeRandStr(4);
}

function makeRandStr($length) {
    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
    $r_str = null;
    for ($i = 0; $i < $length; $i++) {
        $r_str .= $str[mt_rand(0, count($str) - 1)];
    }
    return $r_str;
}

function res_json($arr) {
    $resarr = $arr == NULL ? [] : $arr;
    return json_encode($resarr);
}
