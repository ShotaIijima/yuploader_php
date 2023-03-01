function add_results(data) {
  let item_node = $('#TEMPLATEITEMCODE')
  .clone()
  .attr('id', data['item_code']);
  item_node.find('.img_prev')
  .attr('onClick', 'img_prev(\'' + data['item_code'] + '\')');
  item_node.find('.img_next')
  .attr('onClick', 'img_next(\'' + data['item_code'] + '\')');
  item_node.find('.img_clear')
  .attr('onClick', 'clear_img(\'' + data['item_code'] + '\')');
  if (data['price'] != null) {
    item_node.find('input[name="price"]')
    .val(data['price']);
  }
  if (data['additional2'] != null) {
    item_node.find('textarea[name="addi2"]')
    .val(data['additional2']);
  }
  if (data['name'] != null) {
    item_node.find('input[name="name"]')
    .val(data['name']);
  }
  if (data['options'] != null) {
    item_node.find('input[name="options"]')
    .val(data['options']);
  }
  if (data['edit_item_code'] != null) {
    item_node.find('input[name="edit_item_code"]')
    .val(data['edit_item_code']);
  }
  if (data['item_code'] != null) {
    item_node.find('input[name="item_code"]')
    .val(data['item_code']);
  }
  item_node.find('select[name="cate_id"]')
  .val($('select[name="cate_id"]').val());
  item_node.find('select[name="path_id"]')
  .val($('select[name="path_id"]').val());
  item_node.find('input[name="product_cate_id"]')
  .val($('input[name="product_cate_id"]').val());
  item_node.find('input[name="brand_code_id"]')
  .val($('input[name="brand_code_id"]').val());
  item_node.find('.upitem')
  .attr('onClick', 'upload_item(\'' + data['item_code'] + '\')');
  item_node.find('#TEMPLATEITEMCODE_loader')
  .attr('id', data['item_code'] + '_loader');
  item_node.find('#TEMPLATEITEMCODE_imgs')
  .attr('id', data['item_code'] + '_imgs');
  item_node.find('#TEMPLATEITEMCODE_upload_result')
  .attr('id', data['item_code'] + '_upload_result');
  if (data['imgs'].length > 0) {
    item_node.find('.dl_imgs').attr('src', data['imgs'][0]);
    if (data['imgs'].length > 1) {
      for(var j=1; j<data['imgs'].length; j++) {
        var item = item_node.find('img:nth-child(1)').clone();
        item.attr('src', data['imgs'][j])
        .css("display", "none");
        item_node.find('#' + data['item_code'] + '_imgs')
        .append(item);
      }
    }
  }
  item_node.css("display", "block")
           .appendTo('#results');
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
  init_results();
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
