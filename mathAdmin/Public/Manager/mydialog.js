var Modal = function(options) {
  var reg = new RegExp("\\[([^\\[\\]]*?)\\]", "igm");
  var options = options;
  var modalDom = $("#" + options.id);
  //   var modalHtml = modalDom.html();

  var _alert = function() {
    //重置modal
    // modalDom.html(modalHtml);
    modalDom.find(".cancel").hide();
    _dialog();
  };

  var _confirm = function() {
    //重置modal
    // modalDom.html(modalHtml);
    modalDom.find(".cancel").show();
    _dialog();
    return {
      on: function(callback) {
        if (callback && callback instanceof Function) {
          modalDom.find(".ok").click(function() {
            callback(true);
          });
          modalDom.find(".cancel").click(function() {
            callback(false);
          });
        }
      }
    };
  };

  var _dialog = function() {
    var ops = {
      msg: "提示内容",
      title: "操作提示",
      btnok: "确定",
      btncl: "取消"
    };
    $.extend(ops, options);

    // var html = modalDom.html().replace(reg, function(node, key) {
    //   return {
    //     Title: ops.title,
    //     Message: ops.msg,
    //     BtnOk: ops.btnok,
    //     BtnCancel: ops.btncl
    //   }[key];
    // });

    $("#mtitle").html(ops.title);
    $("#msg").html(ops.msg);
    $("#BtnOk").html(ops.btnok);
    $("#BtnCancel").html(ops.btncl);

    var html = modalDom.html();

    modalDom.html(html);
    modalDom.modal({
      // backdrop :'static'
    });
  };

  return {
    alert: _alert,
    confirm: _confirm
  };
};
