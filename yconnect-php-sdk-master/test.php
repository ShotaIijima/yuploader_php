<?php

require("vendor/autoload.php");

use YConnect\Credential\ClientCredential;
use YConnect\YConnectClient;

// アプリケーションID, シークレット
$client_id     = "dj00aiZpPXQwTU1uN05FMnZWMiZzPWNvbnN1bWVyc2VjcmV0Jng9MmI-";
$client_secret = "WHX3zwzuOMuXrFywIz9eT4Y0oCA8SARclNfK8IAl";
$redirect_uri  = "http://localhost:8080";

$cred = new ClientCredential( $client_id, $client_secret );
$client = new YConnectClient( $cred );

try {
    // Authorization Codeを取得
    $code_result = $client->getAuthorizationCode( $state );

    // Tokenエンドポイントにリクエスト
    $client->requestAccessToken( $redirect_uri, $code_result );

    // アクセストークン, リフレッシュトークンを取得
    $access_token  = $client->getAccessToken();
    $refresh_token = $client->getRefreshToken();

} catch ( TokenException $e ) {
    // 再度ログインして認可コードを発行してください
}
