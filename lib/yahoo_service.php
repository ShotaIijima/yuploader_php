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

    public function upload_item($datas, $imgs)
    {
        $path = sprintf("%s/editItem", self::API_PATH);
        $header = [
            sprintf("POST %s HTTP/1.1", $path),
            'Host: ' . self::BASE_HOST,
            'Authorization: Bearer ' . $this->access_token
        ];
        
        $url = self::BASE_URL . $path;
        $param = array(
            'seller_id' => self::SELLER_ID,
            'item_code' => $datas['edit_item_code'],
            'price' => $datas['price'],
            'name' => $datas['name'],
            'path' => $datas['path'],
            'explanation' => $datas['name'] . "\n\n〜Import store NAIA〜\n店長が厳選した海外ブランドのお品を直輸入\n国内では手に入らない限定商品も\n安心の国内発送　検品の上お手元へお送りいたします。\n\n海外から直輸入の為、お届けまでに10-30日程度お時間いただきます。\n\nオススメ セレクト ブランド 今季フェイスブック Facebook Twitter ツイッター  \n海外 直輸入 店長 インスタ　インスタグラム Instagram  トレンド 人気\n流行 商品 レア定番 限定 有名ブロガー ブログ 掲載商品 誕生日 プレゼント ギフト",
            'additional1' => get_additional1(
                strtolower($datas['item_code']),
                count($imgs)
            ),
            'additional2' => sprintf('<div align="center"><div style="width:600">%s</div></div>', $datas['addi2']),
            'additional3' => '<div align="center"><b>お客様へのご案内</b><br><table border="1" bgcolor="#086A87" width="550" height="20"><tbody><tr><td width="150" height="20">サイズについて</td><td width="450" height="20">●海外モデルを取り寄せいたしますので付属品や商品の仕様に事前の通告なしに変更があることがございますことをご了承ください。<br><br>●海外モデルを取り寄せいたしますのでブランドによりサイズ表記に差がございます。ご希望のサイズがございましたらお問い合わせ下さい。<br><br>●ご希望の日本サイズをご注文・お問い合わせ時にお知らせいただけましたらご希望サイズに相当のサイズをお手配させていただきます。</td></tr><tr><td width="150" height="20">納期について</td><td width="380" height="20">●納期に関しましては、海外からの配送になりますので、<font size="2" color="#FF0000"><b>通常10日から30日前後</b></font>頂いております。<br><br>●国内に到着後、きちんと検品のうえお届けさせていただきます。<br><br>●お客様のご注文商品をお取り寄せいたしますので、基本的にはご注文後のキャンセルはできかねますことをご了承くださいませ。<br><br>●海外店舗でも販売しておりますので、売り切れの場合もございます。在庫確認後お返事させていただきます。</td></tr><tr><td width="150" height="20">保障について</td><td width="380" height="20">●海外正規取扱店からの　並行輸入商品の為日本国内での保障ではなく、購入国アメリカ等、海外での保証となっております。<br>●誠に申し訳ございませんが、海外での保障は弊社ではサポートできかねます事をご了承ください。</td></tr><tr><td width="150" height="20">返品・交換について</td><td width="380" height="20">●お客様都合の商品の返品および交換は承っておりません。<br>●弊社に原因のある際の返品商品が壊れている、動かないなど、当方に原因のある際にはすべて当方費用負担で返金または交換をいたします。<br>●商品の到着後、3日間以内に弊社までご連絡のうえ、商品をご返送（着払い）くださいますようお願いいたします。</td></tr></tbody></table></Div>',
            'sp_additional' => 'お客様へのお願い<br><br><br><br>●海外モデルを取り寄せいたしますので付属品や商品の仕様に事前の通告なしに変更があることがございますことをご了承ください。<br><br>●海外モデルを取り寄せいたしますのでブランドによりサイズ表記に差がございます。ご希望のサイズがございましたらお問い合わせ下さい。<br><br>●ご希望の日本サイズをご注文・お問い合わせ時にお知らせいただけましたらご希望サイズに相当のサイズをお手配させていただきます。<br><br>納期について●納期に関しましては、海外からの配送になりますので、<b>通常10日から30日前後</b>頂いております。<br><br>●国内に到着後、きちんと検品のうえお届けさせていただきます。<br><br>●お客様のご注文商品をお取り寄せいたしますので、基本的にはご注文後のキャンセルはできかねますことをご了承くださいませ。<br><br>●海外店舗でも販売しておりますので、売り切れの場合もございます。在庫確認後お返事させていただきます。<br><br>保障について<br><br>●海外正規取扱店からの　並行輸入商品の為日本国内での保障ではなく、購入国アメリカ等、海外での保証となっております。<br>●誠に申し訳ございませんが、海外での保障は弊社ではサポートできかねます事をご了承ください。<br><br>返品・交換について<br><br>●お客様都合の商品の返品および交換は承っておりません。<br>●弊社に原因のある際の返品商品が壊れている、動かないなど、当方に原因のある際にはすべて当方費用負担で返金または交換をいたします。<br>●商品の到着後、3日間以内に弊社までご連絡のうえ、商品をご返送（着払い）くださいますようお願いいたします。<br><br>',
            'options' => get_options($datas['options'])
        );
        
        // 必要に応じてオプションを追加してください。
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $param);
        
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    
    public function set_stock($datas)
    {
        $path = sprintf("%s/setStock", self::API_PATH);
        $header = [
            sprintf("POST %s HTTP/1.1", $path),
            'Host: ' . self::BASE_HOST,
            'Authorization: Bearer ' . $this->access_token
        ];
        
        $url = self::BASE_URL . $path;
        $param = array(
            'seller_id' => self::SELLER_ID,
            'item_code' => $datas['edit_item_code'],
            'quantity' => '+5',
            'allow_overdraft' => '1'
        );
        
        // 必要に応じてオプションを追加してください。
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $param);
        
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function publish($datas)
    {
        $path = sprintf("%s/reservePublish", self::API_PATH);
        $header = [
            sprintf("POST %s HTTP/1.1", $path),
            'Host: ' . self::BASE_HOST,
            'Authorization: Bearer ' . $this->access_token
        ];
        
        $url = self::BASE_URL . $path;
        $param = array(
            'seller_id' => self::SELLER_ID,
            'mode' => '1'
        );
        
        // 必要に応じてオプションを追加してください。
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $param);
        
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function regist_all_images($datas)
    {
        foreach ($datas as $key => $data) {
            $dl_img = __DIR__ . "/../static/dl_imgs/" . $data['item_code'];
            $zip_img = __DIR__ . "/../static/zip_imgs/" . $data['item_code'] . '.zip';
        }
        
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
