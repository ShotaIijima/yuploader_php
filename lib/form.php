<form id="upload-form" method="post">
  <div class="form-inline">
    <div class="form-group mr-3">
      <select name="store_id" aria-label="ストア" required>
        <option value="0" selected>ストアを選んでください</option>
        <?php while($row = $store_st->fetch(PDO::FETCH_ASSOC)){ ?>
        <option value="<?=h($row['id'])?>"><?=h($row['jp'])?></option>
        <?php } ?>
      </select>
    </div>
    <div class="form-group mr-3">
      <label for="url">URL</label>
      <input type="text" name="url" class="form-control">
    </div>
    <div class="form-group mr-3">
      <input type="checkbox" class="form-check-input" name="get_by_search" value="1">
      <label class="form-check-label" for="get_by_search">検索結果画面から取得する</label>
    </div>
    <div class="form-group mr-3">
      <input type="checkbox" class="form-check-input" name="combine_logo" value="1">
      <label class="form-check-label" for="combine_logo">ロゴを合成する</label>
    </div>
  </div>
  <div class="form-inline">
    <div class="form-group mr-3">
      <label for="seller">出品者</label>
      <input type="text" name="seller" class="form-control">
    </div>
    <div class="form-group mr-3">
      <select name="cate_id" aria-label="カテゴリ">
        <option value="0" selected>カテゴリを選んでください</option>
        <?php while($row = $cate_st->fetch(PDO::FETCH_ASSOC)){ ?>
        <option value="<?=h($row['id'])?>"><?=h($row['name'])?></option>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="form-inline">
    <div class="form-group mr-3">
      <select name="path_id" aria-label="パス">
        <option value="0" selected>パスを選んでください</option>
        <?php while($row = $path_st->fetch(PDO::FETCH_ASSOC)){ ?>
        <option value="<?=h($row['name'])?>"><?=h($row['name'])?></option>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="form-inline">
    <div class="form-group mr-3">
      <label for="product_cate_id">プロダクトカテゴリ</label>
      <input type="text" name="product_cate_id" class="form-control">
    </div>
    <div class="form-group mr-3">
      <label for="brand_code_id">ブランドコード</label>
      <input type="text" name="brand_code_id" class="form-control">
    </div>
    <div class="form-group mr-3">
      <button type="button" onClick="search()" class="btn btn-primary">検索</button>
      <div id="search_loader" class="spinner-border" style="display:none" role="status"></div>
    </div>
  </div>
</form>
<p id="search_result"></p>

<div id="results" style="display:none">
  <div class="form-inline items">
    <div class="form-group mr-3">
      <button type="button" style="display:none" onClick="upload_complete_all()" class="btn btn-primary">反映</button>
    </div>
    <div class="form-group mr-3">
      <label for="url">出品間隔</label>
      <input type="text" name="upload_item_interval" id="upload_item_interval" value="10" class="form-control">
    </div>
    <div class="form-group mr-3">
      <button type="button" onClick="upload_item_all()" class="btn btn-primary">すべて出品</button>
      <button type="button" onClick="init_results()" class="btn btn-danger">クリア</button>
      <div id="all_loader" class="spinner-border" style="display:none" role="status"></div>
      <p id="all_result"></p>
    </div>
  </div>
  <form id="TEMPLATEITEMCODE" class="items" method="post" style="display:none">
    <div class="form-inline">
      <div class="form-group mr-3">
        <button class="img_btn img_prev" type="button" onClick="img_prev('TEMPLATEITEMCODE')">＜</button>
        <div id="TEMPLATEITEMCODE_imgs">
          <img class="dl_imgs" style="display:block" src="/static/dl_imgs/TEMPLATEITEMCODE/TEMPLATEITEMCODE_0.jpg" alt="画像がありません">
        </div>
        <button class="img_btn img_next" type="button" onClick="img_next('TEMPLATEITEMCODE')">＞</button>
      </div>
      <div class="form-group mr-3" style="width:200px">
      </div>
      <div class="form-group mr-3">
        <button type="button" onClick="clear_img('TEMPLATEITEMCODE')" class="btn btn-danger img_clear">画像削除</button>
      </div>
      <div class="form-group mr-3">
        <textarea type="textarea" name="addi2" class="form-control" cols="60" rows="15"></textarea>
      </div>
    </div>
    <div class="form-inline">
      <div class="form-group mr-3">
        <label for="seller">商品名</label>
        <input type="text" name="name" class="form-control">
      </div>
      <div class="form-group mr-3">
        <label for="seller">通常販売価格</label>
        <input type="text" name="price" class="form-control">
      </div>
      <div class="form-group mr-3 select-path_id">
      </div>
      <div class="form-group mr-3">
        <label for="item_code">商品コード</label>
        <input type="text" name="edit_item_code" class="form-control">
        <input type="hidden" name="item_code" class="form-control">
      </div>
      <div class="form-group mr-3 select-cate_id">
      </div>
      <div class="form-group mr-3">
        <label for="options">オプション</label>
        <input type="text" name="options" class="form-control">
      </div>
      <div class="form-group mr-3">
        <label for="product_cate_id">プロダクトカテゴリ</label>
        <input type="text" name="product_cate_id" class="form-control">
      </div>
      <div class="form-group mr-3">
        <label for="brand_code_id">ブランドコード</label>
        <input type="text" name="brand_code_id" class="form-control">
      </div>
      <div class="form-group mr-3">
        <button type="button" onClick="upload_item('TEMPLATEITEMCODE')" class="btn btn-primary upitem">出品</button>
        <button type="button" onClick="delete_item('TEMPLATEITEMCODE')" class="btn btn-danger del_item">削除</button>
        <div id="TEMPLATEITEMCODE_loader" class="spinner-border" style="display:none" role="status"></div>
      </div>
      <div class="form-group mr-3">
        <p id="TEMPLATEITEMCODE_upload_result"></p>
      </div>
    </div>
  </form>
</div>
