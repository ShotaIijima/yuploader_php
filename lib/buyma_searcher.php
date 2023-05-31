<?php

#require_once __DIR__ . '/function.php';
require_once __DIR__ . '/searcher.php';

class BuymaSearcher extends Searcher {
    protected $rms = [];
    protected $item_code_pref = "YBU";

    public function get_result_url() {
        if (is_checkbox_on('get_by_search')) {
            if (is_checkbox_on('seller')) {
                $searchresultpage = 'https://www.buyma.com/r/-R120/' . urlencode($_POST['seller']) . '/';
            } else {
                $searchresultpage = $_POST['url'];
            }
            $this->make_doc($searchresultpage);
            foreach ($this->doc->find('.product') as $youso) {
                $item_id = $youso->getAttribute('item-id');
                $this->urls[] = 'https://www.buyma.com/item/' . $item_id . '/';
            }
        } else {
            $this->urls[] = $_POST['url'];
        }
    }

    public function get_price() {
        $priceEl = $this->_doc_find('#abtest_display_pc');
        if ($priceEl == null) {
            logging('failed to find #abtest_display_pc');
            return null;
        }
        $priceStr = preg_replace("/[^0-9]/", "", $priceEl->text());
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
        $nameEl = $this->_doc_find('#item_h1 span');
        if ($nameEl == null) {
            logging('failed to find nameEl');
            return null;
        }
        $nameStr = $nameEl->text();
        return $nameStr;
    }

    public function get_options() {
        $res = '';
        $arr1 = [];
        $opts1 = $this->_doc_find('.item_color_name');
        if ($opts1 == null) {
            logging('failed to find opts1');
            return null;
        }
        $arr2 = [];
        $opts2 = $this->_doc_find('.cse-set__table tr');
        foreach($opts1 as $key => $opt) {
            $str = $this->_doc_find(".item_color_name:eq($key)")->text();
            $str = str_replace(array("\r\n", "\r", "\n"), '', $str);
            $str = str_replace(" ", "", $str);
            if (strlen($str) === 0) continue;
            $arr1[] = $str;
        }
        if ($opts2 != null) {
            foreach($opts2 as $key => $opt) {
                $str = $this->_doc_find(".cse-set__table tr:eq($key) td:eq(0)")->text();
                $str = str_replace(array("\r\n", "\r", "\n"), '', $str);
                $str = str_replace(" ", "", $str);
                if (strlen($str) === 0) continue;
                $arr2[] = $str;
            }
        }
        if (count($arr1) > 0) {
            $res .= "カラー#";
            $res .= implode(",", $arr1);
            if (count($arr2) > 0) {
                $res .= "|";
            }
        }
        if (count($arr2) > 0) {
            $res .= "サイズ#";
            $res .= implode(",", $arr2);
        }
        return $res;
    }

    public function get_additional2() {
        $str1 = '以下　仕入れ先からの説明文となります。<br><br>';
        $elem1 = $this->_doc_find('.cse-set__table-wrap');
        $elem2 = $this->_doc_find('.cse-detail');
        $elem3 = $this->_doc_find('#item_maincol .free_txt');
        if ($elem1 != null) {
            $str2 = str_replace("<a></span>", "", $elem1->html());
            $str2 = str_replace("</a>", "", $str2);
            $str2 = str_replace("<table", "<table border=\"1\" style=\"white-space: nowrap\"", $str2) . "<br><br>";
            if (strlen($str2) > 0) {
                $str1 .= $str2;
            }
        }
        if ($elem2 != null) {
            $str3 = str_replace("<a></span>", "", $elem2->html());
            $str3 = str_replace("</a>", "", $str3);
            $str3 = str_replace("<table", "<table border=\"1\" style=\"white-space: nowrap\"", $str3) . "<br><br>";
            if (strlen($str3) > 0) {
                $str1 .= $str3;
            }
        }
        if ($elem3 != null) {
            if (strlen($elem3->html()) > 0) $str1 .= $elem3->html();
        }

        return $str1;
    }

    public function get_images() {
        $srcs = [];
        $imageEls = $this->_doc_find(".item-main-image");
        if ($imageEls == null)
        {
            logging("failed to find imageEls");
            return $srcs;
        }
        foreach ($imageEls as $imageEl)
        {
            $src = $imageEl->getAttribute("src");
            if (validateImgUrl($src))
            {
                $srcs[] = $src;
            }
        }
        return $srcs;
    }
}
