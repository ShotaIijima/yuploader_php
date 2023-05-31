<?php

#require_once __DIR__ . '/function.php';
require_once __DIR__ . '/searcher.php';

class EtorenSearcher extends Searcher {
    protected $rms = [];
    protected $item_code_pref = "YET";

    public function get_result_url() {
        if (is_checkbox_on('get_by_search')) {
            $this->make_doc($_POST['url']);
            foreach ($this->doc->find('.lt-image') as $key => $value) {
                $url = $value->getAttribute('href');
                $this->urls[] = $url;
            }
        } else {
            $this->urls[] = $_POST['url'];
        }
    }

    public function get_price() {
        $priceEl = $this->_doc_find('.product_page_price .price-new');
        if ($priceEl == null) {
            logging('failed to find .price-new');
            return null;
        }
        $priceStr = preg_replace("/[^0-9]/", "", $priceEl[0]->text());
        $price = intval($priceStr);
        if ($price === 0) {
            logging('price is 0 :' . $priceStr);
            return null;
        }
        _debug_obj($priceStr);
        $priceCalced = $this->calc_kakaku($price);
        if ($priceCalced <= 0) {
            logging('price is lower than 0. :' . $priceCalced);
            return null;
        }
        return $priceCalced;
    }

    public function get_name() {
        $nameEl = $this->_doc_find('.product-page-title');
        if ($nameEl == null) {
            logging('failed to find nameEl');
            return null;
        }
        return $nameEl->text();
    }

    public function get_options() {
        $res = '';
        $arr1 = [];
        $opts1 = $this->_doc_find('.variation-product option');
        if ($opts1 == null) {
            logging('failed to find opts1');
            return null;
        }
        foreach($opts1 as $key => $opt) {
            $origin = $this->_doc_find(".variation-product option:eq($key)")->text();
            $tmp = trim($origin);
            $tmp = str_replace([
                ",",
                "text-warning",
                "text-success",
                "text-error"
            ], [
                ".",
                ""
            ], $tmp);
            $arr1[] = $tmp;
        }
        if (count($arr1) > 0) {
            $res .= "バリエーション#";
            $res .= implode(",", $arr1);
        }
        return $res;
    }

    public function get_additional2() {
        $addiELs = $this->_doc_find('.tab-specs table');
        if ($addiELs == null) {
            logging('failed to find addiELs');
            return null;
        }
        $addiELs2 = $this->_doc_find('.content-desc');
        if ($addiELs2 == null) {
            logging('failed to find addiELs2');
            return null;
        }
        $addi1 = removeAttrsHTMLTag($addiELs[0]->html());
        $addi2 = removeAttrsHTMLTag($addiELs2[0]->html());
        $res = "以下　英文ですが仕入れ先からの説明文となります。<br><br>";
        if (strlen($addi1) > 0) {
            $res .= $addi1;
        }
        if (strlen($addi2) > 0) {
            $res .= '<br><br>' . $addi1;
        }
        return $res;
    }

    public function get_images() {
        $srcs = [];
        $imageEls = $this->_doc_find("#large_product_image");
        if ($imageEls == null)
        {
            logging("failed to find imageEls");
            return $srcs;
        }
        foreach($imageEls as $imageEl) {
            $srcs[] = $imageEl->getAttribute("src");
        }
        return $srcs;
    }
}
