function upload_item(item_code) {
  var datas = $('#' + item_code).serializeArray();
  var imgs = $('#' + item_code).find('img');
  var upimgs = [];
  for (var i=0; i<imgs.length; i++) {
    upimgs.push(imgs[i].src);
  }
  $('#' + item_code + '_loader').css("display", "block");
  $.ajax({
    type: "POST",
    url: "api_upload_item.php",
    data: {
      datas: datas,
      imgs: upimgs
    },
    dataType: 'json',
  }).done(function(data, textStatus, jqXHR) {
    // 成功時
    console.log('execute done');
    if (data["redirect_to"]) {
      console.log('redirect_to: ' + data["redirect_to"]);
      window.location.href = data["redirect_to"];
    }
    console.log(data);
    if (data.length > 0) {
      set_each_success_result(item_code + ": しました。");
    } else {
      set_each_error_result("結果は0件でした。");
    }
  }).fail(function(jqXHR, textStatus, errorThrown) {
    // 失敗時
    console.log('execute failed: ' + textStatus);
    console.log(errorThrown);
    set_each_error_result("検索結果の取得に失敗しました。");
  }).always(function(data, textStatus, jqXHR) {
    // 成功しても失敗しても常に呼び出される
    // 引数は成功時と失敗時で異なる
    $('#' + item_code + '_loader').css("display", "none");
  });
}

</script>