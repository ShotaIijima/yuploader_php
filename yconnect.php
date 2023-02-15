<?php

require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/yahoo_service.php';
require("yconnect-php-sdk-master/vendor/autoload.php");
init_yuploader();
require_logined_session();

use YConnect\Constant\OIDConnectDisplay;
use YConnect\Constant\OIDConnectPrompt;
use YConnect\Constant\OIDConnectScope;
use YConnect\Constant\ResponseType;
use YConnect\Credential\ClientCredential;
use YConnect\Exception\ApiException;
use YConnect\Exception\TokenException;
use YConnect\YConnectClient;

// アプリケーションID, シークレット
$client_id     = $_SESSION['user']['client_id'];
$client_secret = $_SESSION['user']['secret'];

// 各パラメータ初期化
// $redirect_uri = "https://store.shopping.yahoo.co.jp/primopasso/";
$redirect_uri = "http://localhost:8080/yconnect.php";

// リクエストとコールバック間の検証用のランダムな文字列を指定してください
$state = "44Oq44Ki5YWF44Gr5L+644Gv44Gq44KL77yB";
// リプレイアタック対策のランダムな文字列を指定してください
$nonce = "5YOV44Go5aWR57SE44GX44GmSUTljqjjgavjgarjgaPjgabjgog=";
// 認可コード横取り攻撃対策文字列を指定してください
$plain_code_challenge = "E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM._~";

$response_type = ResponseType::CODE;
$scope = array(
    OIDConnectScope::OPENID,
    OIDConnectScope::PROFILE,
    OIDConnectScope::EMAIL,
    OIDConnectScope::ADDRESS
);
$display = OIDConnectDisplay::DEFAULT_DISPLAY;
$prompt = array(
    OIDConnectPrompt::DEFAULT_PROMPT
);

// クレデンシャルインスタンス生成
$cred = new ClientCredential($client_id, $client_secret);
// YConnectクライアントインスタンス生成
$client = new YConnectClient($cred);

// デバッグ用ログ出力
$client->enableDebugMode();

try {
    // Authorization Codeを取得
    $code_result = $client->getAuthorizationCode($state);

    if (!$code_result) {

        /*****************************
             Authorization Request
        *****************************/

        // Authorizationエンドポイントにリクエスト
        $client->requestAuth(
            $redirect_uri,
            $state,
            $nonce,
            $response_type,
            $scope,
            $display,
            $prompt,
            36000,
            $plain_code_challenge
        );
    } else {

        /****************************
             Access Token Request
        ****************************/

        // Tokenエンドポイントにリクエスト
        $client->requestAccessToken(
            $redirect_uri,
            $code_result,
            $plain_code_challenge
        );

        echo "<h1>Access Token Request</h1>";
        // アクセストークン, リフレッシュトークン, IDトークンを取得
        echo "ACCESS TOKEN : " . $client->getAccessToken() . "<br/><br/>";
        echo "REFRESH TOKEN: " . $client->getRefreshToken() . "<br/><br/>";
        echo "EXPIRATION   : " . $client->getAccessTokenExpiration() . "<br/><br/>";

        // 保存
        $dbh = db_connect();
        $stmt = $dbh->prepare("UPDATE users SET access_token = :access_token, refresh_token = :refresh_token WHERE name = :name");
        $stmt->bindParam(':access_token', $client->getAccessToken(), PDO::PARAM_STR);
        $stmt->bindParam(':refresh_token', $client->getRefreshToken(), PDO::PARAM_STR);
        $stmt->bindParam(':name', $_SESSION['user']['name'], PDO::PARAM_STR);
        $res = $stmt->execute();
        echo "DB保存結果   : " . $res . "<br/><br/>";

        // カテゴリ更新
        $stmt2 = $dbh->prepare("DELETE FROM categories");
        $res = $stmt2->execute();
        $ys = YahooService::get_instance();
        $cates = $ys->get_cates();
        echo $cates;
        $xml = new SimpleXMLElement($cates);
        echo $xml;
        foreach ($xml->Result as $value) {
            echo sprintf("CategoryCode: %s, CategoryName: %s\n", $value->CategoryCode, $value->CategoryName);
            $stmt = $dbh->prepare("INSERT INTO categories VALUES (:id, :name)");
            $stmt->bindParam(':id', $value->CategoryCode, PDO::PARAM_INT);
            $stmt->bindParam(':name', $value->CategoryName, PDO::PARAM_STR);
            $res = $stmt->execute();
        }

        /*****************************
             Verification ID Token
        *****************************/

        // IDトークンを検証
        $client->verifyIdToken($nonce, $client->getAccessToken());
        echo "ID TOKEN: <br/>";
        echo "<pre>" . print_r($client->getIdToken(), true) . "</pre>";

        /************************
             UserInfo Request
        ************************/

        // UserInfoエンドポイントにリクエスト
        $client->requestUserInfo($client->getAccessToken());
        echo "<h1>UserInfo Request</h1>";
        echo "UserInfo: <br/>";
        // UserInfo情報を取得
        echo "<pre>" . print_r($client->getUserInfo(), true) . "</pre>";
    }
} catch (ApiException $ae) {
    // アクセストークンが有効期限切れであるかチェック
    if ($ae->invalidToken()) {

        /************************************
             Refresh Access Token Request
        ************************************/

        try {
            // 保存していたリフレッシュトークンを指定してください
            $refresh_token = "STORED_REFRESH_TOKEN";

            // Tokenエンドポイントにリクエストしてアクセストークンを更新
            $client->refreshAccessToken($refresh_token);
            echo "<h1>Refresh Access Token Request</h1>";
            echo "ACCESS TOKEN : " . $client->getAccessToken() . "<br/><br/>";
            echo "EXPIRATION   : " . $client->getAccessTokenExpiration();
        } catch (TokenException $te) {
            // リフレッシュトークンが有効期限切れであるかチェック
            if ($te->invalidGrant()) {
                // はじめのAuthorizationエンドポイントリクエストからやり直してください
                echo "<h1>Refresh Token has Expired</h1>";
            }

            echo "<pre>" . print_r($te, true) . "</pre>";
        } catch (Exception $e) {
            echo "<pre>" . print_r($e, true) . "</pre>";
        }
    } elseif ($ae->invalidRequest()) {
        echo "<h1>Invalid Request</h1>";
        echo "<pre>" . print_r($ae, true) . "</pre>";
    } else {
        echo "<h1>Other Error</h1>";
        echo "<pre>" . print_r($ae, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "<pre>" . print_r($e, true) . "</pre>";
}

