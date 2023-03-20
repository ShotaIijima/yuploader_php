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
  let cate_id_select = $('#upload-form select[name="cate_id"]').clone();
  cate_id_select.appendTo('#results .select-cate_id');
  let path_id_select = $('#upload-form select[name="path_id"]').clone();
  path_id_select.appendTo('#results .select-path_id');
  /*$('upload-form select[name="cate_id"]').select2({
    language: "ja"
  });
  $('upload-form select[name="path_id"]').select2({
    language: "ja"
  });
  $('upload-form select[name="product_cate_id"]').select2({
    language: "ja"
  });
  $('upload-form select[name="brand_code_id"]').select2({
    language: "ja"
  });
  $('results select[name="cate_id"]').select2({
    language: "ja"
  });
  $('results select[name="path_id"]').select2({
    language: "ja"
  });
  $('results select[name="product_cate_id"]').select2({
    language: "ja"
  });
  $('results select[name="brand_code_id"]').select2({
    language: "ja"
  });*/
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
