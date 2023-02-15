<script>

var item_attrs = [
  "price",
  "item_code",
  "edit_item_code",
  "name",
  "imgs",
  "additional2",
  "options"
];

window.addEventListener('load', (event) => {
  $('select[name="cate_id"]').select2({
    language: "ja"
  });
  $('select[name="path_id"]').select2({
    language: "ja"
  });
  $('select[name="product_cate_id"]').select2({
    language: "ja"
  });
  $('select[name="brand_code_id"]').select2({
    language: "ja"
  });
  console.log('ページが完全に読み込まれました');
  // ページ読み込み時に実行したい処理
  var store_id = localStorage.getItem('store_id');
  if (store_id) $('select[name="store_id"]').val(store_id);
  var url = localStorage.getItem('url');
  if (url) $('input[name="url"]').val(url);
  var get_by_search = localStorage.getItem('get_by_search');
  if (get_by_search) $('input[name="get_by_search"]').val(get_by_search);
  var combine_logo = localStorage.getItem('combine_logo');
  if (combine_logo) $('input[name="combine_logo"]').val(combine_logo);
  var seller = localStorage.getItem('seller');
  if (seller) $('input[name="seller"]').val(seller);
  var cate_id = localStorage.getItem('cate_id');
  if (cate_id) $('select[name="cate_id"]').val(cate_id);
  var path_id = localStorage.getItem('path_id');
  if (path_id) $('select[name="path_id"]').val(path_id);
  var product_cate_id = localStorage.getItem('product_cate_id');
  if (product_cate_id) $('select[name="product_cate_id"]').val(product_cate_id);
  var brand_code_id = localStorage.getItem('brand_code_id');
  if (brand_code_id) $('select[name="brand_code_id"]').val(brand_code_id);
});

function set_search_result(str, color) {
  $('#search_result').css("color", color);
  $('#search_result').text(str);
}

function set_error_result(str) {
  set_search_result(str, "red");
}

function set_success_result(str) {
  set_search_result(str, "green");
}

function img_prev(item_code) {
  var imgs = $('#' + item_code).find("img");
  for (var i=0; i<imgs.length; i++) {
    if (imgs[i].style.display === 'block') {
      if (i!==0) {
        imgs[i].style.display = "none";
        imgs[i-1].style.display =  "block";
        break;
      }
    }
  }
}

function img_next(item_code) {
  var imgs = $('#' + item_code).find("img");
  for (var i=0; i<imgs.length; i++) {
    if (imgs[i].style.display === 'block') {
      if (i!==(imgs.length-1)) {
        imgs[i].style.display = "none";
        imgs[i+1].style.display =  "block";
        break;
      }
    }
  }
}

function add_results(data) {
  // if (data[''])
}

function search() {
  localStorage.setItem('store_id', $('select[name="store_id"]').val());
  localStorage.setItem('url', $('input[name="url"]').val());
  localStorage.setItem('get_by_search', $('input[name="get_by_search"]').val());
  localStorage.setItem('combine_logo', $('input[name="combine_logo"]').val());
  localStorage.setItem('seller', $('input[name="seller"]').val());
  localStorage.setItem('cate_id', $('select[name="cate_id"]').val());
  localStorage.setItem('path_id', $('select[name="path_id"]').val());
  localStorage.setItem('product_cate_id', $('select[name="product_cate_id"]').val());
  localStorage.setItem('brand_code_id', $('select[name="brand_code_id"]').val());
  $('#search_loader').css("display", "block");
  $.ajax({
    type: "POST",
    url: "api_search.php",
    data: $("#upload-form").serialize(),
    dataType:'json',
  }).done(function(data, textStatus, jqXHR) {
    // 成功時
    console.log('execute done');
    if (data["redirect_to"]) {
      console.log('redirect_to: ' + data["redirect_to"]);
      window.location.href = data["redirect_to"];
    }
    console.log(data);
    if (data.length > 0) {
      $('#results').css("display", "block");
      set_success_result(data.length + "件取得しました。");
      for (var j=0; j<data.length; j++) {
        add_results(data[j]);
      }
    } else {
      set_error_result("結果は0件でした。");
    }
  }).fail(function(jqXHR, textStatus, errorThrown) {
    // 失敗時
    console.log('execute failed: ' + textStatus);
    console.log(errorThrown);
    set_error_result("検索結果の取得に失敗しました。");
  }).always(function(data, textStatus, jqXHR) {
    // 成功しても失敗しても常に呼び出される
    // 引数は成功時と失敗時で異なる
    $('#search_loader').css("display", "none");
  });
}

function upload_item(item_code) {
  var loader = $('#' + item_code + '_loader');
  loader.style.display = "block";
  $.ajax({
    type: "POST",
    url: "api_upload_item.php",
    data: {
      item_code: item_code
    },
    dataType:'json',
  }).done(function(data, textStatus, jqXHR) {
    // 成功時
    console.log('execute done');
    if (data["redirect_to"]) {
      console.log('redirect_to: ' + data["redirect_to"]);
      window.location.href = data["redirect_to"];
    }
    console.log(data);
    set_success_result(data.length + "件取得しました。");
  }).fail(function(jqXHR, textStatus, errorThrown) {
    // 失敗時
    console.log('execute failed: ' + textStatus);
    console.log(errorThrown);
    set_error_result("検索結果の取得に失敗しました。");
  }).always(function(data, textStatus, jqXHR) {
    // 成功しても失敗しても常に呼び出される
    // 引数は成功時と失敗時で異なる
    loader.style.display = "none";
  });
}
</script>
