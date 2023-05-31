<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/phpQuery-onefile.php';
require_once __DIR__ . '/ebay_searcher.php';
require_once __DIR__ . '/buyma_searcher.php';
require_once __DIR__ . '/etoren_searcher.php';

class Searcher {
    protected $calc_rules = [];
    protected $url = null;
    protected $urls = [];
    protected $doc = null;
    protected $name = null;
    protected $price = null;
    protected $seller_id = null;
    protected $item_code = null;
    protected $edit_item_code = null;
    protected $path = null;
    protected $category = null;
    protected $imgs = null;
    protected $current_img_index = null;
    protected $additional2 = null;
    protected $options = null;
    protected $rmss = [];

    public function get_result() {
        // 対象製品のURLを取得
        $this->get_result_url();
        foreach ($this->urls as $url) {
            $this->make_doc($url);
            $rms = [];
            $rms['price'] = $this->get_price();
            _debug('rms: price: ');
            _debug_obj($rms['price']);
            $rms['item_code'] = $this->get_item_code();
            _debug('rms: item_code: ');
            _debug_obj($rms['item_code']);
            $rms['edit_item_code'] = $rms['item_code'];
            _debug('rms: edit_item_code: ');
            _debug_obj($rms['edit_item_code']);
            $rms['name'] = $this->get_name();
            _debug('rms: name: ');
            _debug_obj($rms['name']);
            $rms['imgs'] = $this->get_images();
            _debug('rms: imgs: ');
            _debug_obj($rms['imgs']);
            if (count($rms['imgs']) === 0) {
                logging("count imgs is zero. : " . $rms['item_code']);
                continue;
            }
            $logow = null;
            $logoh = null;
            if (is_checkbox_on('combine_logo')) {
                $logopath = __DIR__ . "/../static/img/imgicon.png";
                if (($logosize = @getimagesize($logopath)) !== false) {
                    $logow = $logosize[0];
                    $logoh = $logosize[1];
                    $logoim = @imagecreatefromstring(file_get_contents($logopath));
                } else {
                    logging("failed to getimagesize: " . $logopath);
                }
            }
            foreach($rms['imgs'] as $key => $img) {
                if ($key >= 20) continue;
                $dir = __DIR__ . "/../static/dl_imgs/" . $rms['item_code'];
                mkdir($dir);
                $data = file_get_contents($img);
                $pathinfo = pathinfo($img);
                if ($key === 0)
                    $target = $rms['item_code'] . '.' . $pathinfo["extension"];
                else
                    $target = $rms['item_code'] . '_' . $key . '.' . $pathinfo["extension"];
                $filename = $dir . '/' . $target;
                file_put_contents($filename, $data);
                if ($logow != null) {
                    // 画像合成
                    if (($im = @imagecreatefromstring($data)) !== false) {
                        imagecopy($im, $logoim, 10, 10, 0, 0, $logow, $logoh);
                        imagejpeg($im);
                    } else {
                        logging("failed to imagecreatefromstring: " . $filename);
                    }
                }
                // JSONレスポンスに返却する形式
                $res_filepath = sprintf("/static/dl_imgs/%s/%s", $rms['item_code'], $target);
                $rms['imgs'][$key] = $res_filepath;
            }
            $rms['additional2'] = $this->get_additional2();
            _debug('rms: additional2: ');
            _debug_obj($rms['additional2']);
            $rms['options'] = $this->get_options();
            _debug('rms: options: ');
            _debug_obj($rms['options']);
            $this->rmss[] = $rms;
        }
    }

    public function get_calc_rules() {
        $dbh = db_connect();
        $calc_st = $dbh->query("SELECT * FROM calc_rules");
        while($row = $calc_st->fetch(PDO::FETCH_ASSOC)){
            $this->calc_rules[] = $row;
        }
        if (count($this->calc_rules) === 0) {
            logging("ERROR: failed to get calc_rules");
        }
    }

    public function calc_kakaku($original) {
        if (count($this->calc_rules) === 0) {
            $this->get_calc_rules();
        }
        foreach($this->calc_rules as $calc_rule) {
            if ($calc_rule['from_price'] < $original && $original <= $calc_rule['to_price']) {
                return $original * $calc_rule['bai'] + $calc_rule['tasu'];
            }
        }
        return $original;
    }

    protected function make_doc($url) {
        $this->url = $url;
        $html = file_get_contents($url);
        $this->doc = phpQuery::newDocument($html);
    }

    protected function _doc_find($str) {
        $elem = $this->doc->find($str);
        if ($elem) {
            return $elem;
        } else {
            logging('not found ' . $str . ' in ' . $this->url);
            return null;
        }
    }

    protected function _join($str, $elems) {
        $result = "";
        for ($i=0; $i<count($elems); $i++) {
            $result .= $elems[$i]->text();
            if ($i !== (count($elems) - 1)) {
                $result .= $str;
            }
        }
        return $result;
    }

    protected function get_item_code() {
        return $this->item_code_pref . "-" . getItemCodeSuf();
    }

    public static function do_search($store_id) {
        switch ($store_id) {
            case '1':
                $searcher = new EbaySearcher;
                break;
            case '2':
                $searcher = new BuymaSearcher;
                break;
            case '3':
                $searcher = new EtorenSearcher;
                break;
            default:
                logging('do_search unknown store_id: ' . $store_id);
                return null;
        }
        $searcher->get_result();
        return $searcher->rmss;
    }
}
