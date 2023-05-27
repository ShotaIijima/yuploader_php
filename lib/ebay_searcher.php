<?php

#require_once __DIR__ . '/function.php';
require_once __DIR__ . '/searcher.php';

class EbaySearcher extends Searcher {
    protected $urls = [];
    protected $rms = [];
    protected $item_code_pref = "YEB";

    public function get_result_url() {
        if (is_checkbox_on('get_by_search')) {
            if (is_checkbox_on('seller')) {
                $searchresultpage = 'https://www.ebay.com/sch/i.html?_nkw=&_in_kw=1&_ex_kw=&_sacat=0&_udlo=&_udhi=&_ftrt=901&_ftrv=1&_sabdlo=&_sabdhi=&_samilow=&_samihi=&_sadis=15&_stpos=&_sargn=-1%26saslc%3D1&_salic=1&_fss=1&_fsradio=%26LH_SpecificSeller%3D1&_saslop=1&_sasl=' . $_POST['seller'] . '&_sop=12&_dmd=1&_ipg=200&_fosrp=1';
            } else {
                $searchresultpage = $_POST['url'];
            }
            $this->make_doc($searchresultpage);
            // foreach ($this->doc->find('.vip') as $youso) {
            foreach ($this->doc->find('.s-item__link') as $youso) {
                $url = $youso->getAttribute('href');
                $this->urls[] = $url;
            }
        } else {
            $this->urls[] = $_POST['url'];
        }
    }

    public function get_price() {
        $priceEl = $this->_doc_find('.x-price-approx__price .ux-textspans--SECONDARY');
        if ($priceEl == null) {
            logging('failed to find .x-price-approx__price .ux-textspans--SECONDARY');
            return null;
        }
        if (strpos($priceEl->text(), '円') === false) {
            $priceEl = $this->_doc_find('.item-price .display-price');
        }
        if ($priceEl == null) {
            logging('failed to find .item-price .display-price');
            return null;
        }
        if (strpos($priceEl->text(), '円') === false) {
            logging('failed to fetch 円');
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
        $nameEl = $this->_doc_find('.x-item-title__mainTitle span');
        if ($nameEl == null) {
            logging('failed to find nameEl');
            return null;
        }
        $nameStr = preg_replace("/^Details\s+about\s+/", "", $nameEl->text());
        return $nameStr;
    }

    public function get_options() {
        $res = '';
        $arr1 = [];
        $opts1 = $this->_doc_find('#x-msku__select-box-1000 option');
        if ($opts1 == null) {
            logging('failed to find opts1');
            return null;
        }
        $arr2 = [];
        $opts2 = $this->_doc_find('#x-msku__select-box-1001 option');
        foreach($opts1 as $key => $opt) {
            $orig = $this->_doc_find("#x-msku__select-box-1000 option:eq($key)")->text();
            $arr1[] = trim($orig);
        }
        if ($opts2 != null) {
            foreach($opts2 as $key => $opt) {
                $orig = $this->_doc_find("#x-msku__select-box-1001 option:eq($key)")->text();
                $arr2[] = trim($orig);
            }
        }
        if (count($arr1) > 0) {
            $res .= "カラー#";
            $res .= implode(",", $arr1);
            if (count($arr2) > 0) {
                $res .= "|サイズ#";
                $res .= implode(",", $arr2);
            }
        }
        return $res;
    }

    public function get_additional2() {
        $addiELs = $this->_doc_find('.ux-layout-section__item');
        if ($addiELs == null) {
            logging('failed to find addiELs');
            return null;
        }
        $addi = '';
        $addi .= removeAttrsHTMLTag($addiELs[0]->html());
        if (count($addiELs) > 1) {
            $addi .= removeAttrsHTMLTag($addiELs[1]->html());
        }
        return "以下　英文ですが仕入れ先からの説明文となります。<br><br>" . $addi;
    }

    public function get_images() {
        /* $icImgEl = $this->_doc_find(".owl-stage img");
        if ($icImgEl == null) {
            logging('failed to find icImgEl');
            return null;
        }
        $srcs = [];
        $icImg = $icImgEl->getAttribute('src');
        $fileName = splitted_array_last('/', $icImg);
        if (validateImgUrl($icImg)) {
            $srcs[] = $icImg;
        }*/

        $srcs = [];
        $imageEls = $this->_doc_find(".ux-image-filmstrip-carousel-item img");
        // _debug_obj($imageEls);
        if ($imageEls == null)
        {
            logging("failed to find imageEls");
            return $srcs;
        }
        // $first = true;
        foreach ($imageEls as $imageEl)
        {
            /*if ($first)
            {
                $first = false;
                continue;
            }*/
            // $item = $imageEl->find("img");
            $src = $imageEl->getAttribute("src");
            $src = str_replace('s-l64', 's-l500', $src);
            _debug($src);
            /*$tmp2 = explode('/', $src);
            $tmp2[count($tmp2) - 1] = $fileName;
            $newSrc = implode('/', $tmp2);*/
            if (validateImgUrl($src))
            {
                $srcs[] = $src;
            }
        }
        return $srcs;
    }
}
