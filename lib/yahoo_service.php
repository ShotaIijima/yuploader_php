<?php

require_once __DIR__ . '/functions.php';

class YahooService {
    private static $singleton;
    protected $access_token = null;
    private const BASE_HOST = "circus.shopping.yahooapis.jp";
    private const BASE_URL = "https://" . self::BASE_HOST;
    private const API_PATH = "/ShoppingWebService/V1";
    private const SELLER_ID = "primopasso";

    private function __construct()
    {
        $dbh = db_connect();
        $rows = $dbh->prepare("SELECT * FROM users WHERE name = ?");
        $rows->execute([$_SESSION['user']['name']]);
        $row = $rows->fetch();
        $this->access_token = $row['access_token'];
    }

    public static function get_instance()
    {
        if (!isset(self::$singleton)) {
            self::$singleton = new YahooService();    
        }

        return self::$singleton;
    }

    public function get_cates()
    {
        $path = sprintf("%s/getShopCategory?seller_id=%s", self::API_PATH, self::SELLER_ID);
        $header = [
            sprintf("GET %s HTTP/1.1", $path),
            'Host: ' . self::BASE_HOST,
            'Authorization: Bearer ' . $this->access_token
        ];
        
        $url = self::BASE_URL . $path;
        
        // 必要に応じてオプションを追加してください。
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
