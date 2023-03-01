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
