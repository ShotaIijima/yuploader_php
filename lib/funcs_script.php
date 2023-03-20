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

function set_each_upload_result(item_code, str, color) {
  $('#' + item_code + '_upload_result').css("color", color);
  $('#' + item_code + '_upload_result').text(str);
}

function set_each_success_result(item_code, str) {
  set_each_upload_result(item_code, str, "green");
}

function set_each_error_result(item_code, str) {
  set_each_upload_result(item_code, str, "red");
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

function clear_img(item_code) {
  var imgs = $('#' + item_code).find("img");
  if (imgs.length===1) {
    return;
  }
  for (var i=0; i<imgs.length; i++) {
    if (imgs[i].style.display === 'block') {
      if (i===0) {
        imgs[i].remove();
        imgs[i+1].style.display =  "block";
        break;
      } else {
        imgs[i].remove();
        imgs[i-1].style.display =  "block";
        break;
      }
    }
  }
}

function init_results() {
  var ress = $('#results').children()
  if (ress.length > 2) {
    for(var i=2; i<ress.length; i++) {
      ress[i].remove();
    }
  }
}

function delete_item(item_code) {
  $('#' + item_code).remove();
  var item_codes = JSON.parse(localStorage.getItem('item_codes'));
  item_codes.splice(item_codes.indexOf(item_code), 1);
  localStorage.setItem('item_codes', JSON.stringify(item_codes));
}
