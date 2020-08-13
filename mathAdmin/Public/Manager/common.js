var sidebarStatus = GetQueryString("sidebarStatus"); //1关闭2展开
var data_index = GetQueryString("data_index");
var data_level = GetQueryString("data_level");
sidebarStatus == null ? (sidebarStatus = 1) : sidebarStatus;
if (sidebarStatus == 1) {
  $("body").addClass("sidebar-collapse");
} else {
  $("body").removeClass("sidebar-collapse");
}

$('.sidebar-menu a[name="data-route"]')
  .eq(data_index)
  .parent("li")
  .addClass("active");
if (data_level == 2) {
  $('.sidebar-menu a[name="data-route"]')
    .eq(data_index)
    .parent()
    .parent()
    .parent()
    .addClass("menu-open active");
}

$("a.sidebar-toggle").on("click", function() {
  if ($("body").hasClass("sidebar-collapse")) {
    sidebarStatus = 2;
  } else {
    sidebarStatus = 1;
  }
});

$('.sidebar-menu a[name="data-route"]').on("click", function() {
  data_level = $(this).attr("data-level");
  data_index = $(this).attr("data-index");

  var url = $(this).attr("data-open");
  goRedirect(url);
});

function goRedirect(url) {
  if (url.indexOf("?") == -1) {
    url +=
      "?sidebarStatus=" +
      sidebarStatus +
      "&data_index=" +
      data_index +
      "&data_level=" +
      data_level;
  } else {
    url +=
      "&sidebarStatus=" +
      sidebarStatus +
      "&data_index=" +
      data_index +
      "&data_level=" +
      data_level;
  }

  console.log(window.location.hostname + "/mathAdmin/" + url);

  window.location.href =
    "http://" + window.location.hostname + "/mathAdmin/" + url;
}
/**
 * 获取地址栏参数
 * 【注意】传递参数的时候使用encodeURI(encodeURI(content)),取的时候decodeURI(value)
 * @param {[String]} name [参数名称]
 */
function GetQueryString(name) {
  var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
  var r = window.location.search.substr(1).match(reg);
  if (r != null) return decodeURI(unescape(r[2]));
  return null;
}

//去除两边#
String.prototype.trim3 = function() {
  return this.replace(/(^#*)|(#*$)/g, "");
};
//去除两边|
String.prototype.trimGang = function() {
  return this.replace(/(^\|*)|(\|*$)/g, "");
};
String.prototype.trimStr = function(str) {
  return this.replace(eval("/(^" + str + "*)|(" + str + "*$)/g"), "");
};
//替换字符串
String.prototype.replaceStr = function(str) {
  return this.replace(eval("/" + str + "/g"), "");
};

//数组是否重复
function isRepeat(arr) {
  var hash = {};
  for (var i in arr) {
    if (hash[arr[i]]) {
      return true;
    }
    hash[arr[i]] = true;
  }
  return false;
}
//判断是否为数组
function isArray(value) {
  if (typeof Array.isArray === "function") {
    return Array.isArray(value);
  } else {
    return Object.prototype.toString.call(value) === "[object Array]";
  }
}

function getUser() {
  $.ajax({
    type: "get",
    url:
      "http://" +
      window.location.hostname +
      "/mathAdmin/" +
      "Index/getUserName",
    data: {
      ran: Math.random()
    },
    dataType: "json",
    success: function(data) {
      $("#leftUser").html(data.truename);
      $("#topuser").html(data.truename);
      if (data.ifadmin == 0) {
        $("#useraddbutton").hide();
      } else {
        $("#useraddbutton").show();
      }
    }
  });
}

function getGradeName($num) {
  var gradeName = "";
  if ($num == "0001") {
    gradeName = "一年级";
  } else if ($num == "0002") {
    gradeName = "二年级";
  } else if ($num == "0003") {
    gradeName = "三年级";
  } else if ($num == "0004") {
    gradeName = "四年级";
  } else if ($num == "0005") {
    gradeName = "五年级";
  } else if ($num == "0006") {
    gradeName = "六年级";
  } else if ($num == "0007") {
    gradeName = "七年级";
  } else if ($num == "0008") {
    gradeName = "八年级";
  } else if ($num == "0009") {
    gradeName = "九年级";
  }
  return gradeName;
}

function getGradeCode($name) {
  var gradeCode = "";
  if ($name == "一年级") {
    gradeCode = "0001";
  } else if ($name == "二年级") {
    gradeCode = "0002";
  } else if ($name == "三年级") {
    gradeCode = "0003";
  } else if ($name == "四年级") {
    gradeCode = "0004";
  } else if ($name == "五年级") {
    gradeCode = "0005";
  } else if ($name == "六年级") {
    gradeCode = "0006";
  } else if ($name == "七年级") {
    gradeCode = "0007";
  } else if ($name == "八年级") {
    gradeCode = "0008";
  } else if ($name == "九年级") {
    gradeCode = "0009";
  } else {
    gradeCode = "0";
  }
  return gradeCode;
}

function getTermName($num) {
  var termName = "";
  if ($num == "0001") {
    termName = "上学期";
  } else if ($num == "0002") {
    termName = "下学期";
  } else if ($num == "0000") {
    termName = "全一册";
  }
  return termName;
}

function getTermCode($name) {
  var termCode = "";
  if ($name == "上学期") {
    termCode = "0001";
  } else if ($name == "下学期") {
    termCode = "0002";
  } else if ($name == "全一册") {
    termCode = "0000";
  } else {
    termCode = "0";
  }
  return termCode;
}

function getSubjectName($num) {
  var name = "";
  if ($num == "0001") {
    name = "语文";
  } else if ($num == "0002") {
    name = "数学";
  } else if ($num == "0003") {
    name = "英语";
  } else if ($num == "0004") {
    name = "物理";
  } else if ($num == "0005") {
    name = "化学";
  } else if ($num == "0006") {
    name = "音乐";
  } else if ($num == "0007") {
    name = "美术";
  } else if ($num == "0008") {
    name = "科学";
  } else if ($num == "0009") {
    name = "品德";
  } else if ($num == "0010") {
    name = "生物";
  } else if ($num == "0011") {
    name = "地理";
  } else if ($num == "0012") {
    name = "政治";
  } else if ($num == "0013") {
    name = "历史";
  } else if ($num == "0014") {
    name = "信息技术";
  } else if ($num == "0015") {
    name = "通用技术";
  }
  return name;
}

function getSubjectCode($name) {
  var Code = "";
  if ($name == "语文") {
    Code = "0001";
  } else if ($name == "数学") {
    Code = "0002";
  } else if ($name == "英语") {
    Code = "0003";
  } else if ($name == "物理") {
    Code = "0004";
  } else if ($name == "化学") {
    Code = "0005";
  } else if ($name == "音乐") {
    Code = "0006";
  } else if ($name == "美术") {
    Code = "0007";
  } else if ($name == "科学") {
    Code = "0008";
  } else if ($name == "品德") {
    Code = "0009";
  } else if ($name == "生物") {
    Code = "0010";
  } else if ($name == "地理") {
    Code = "0011";
  } else if ($name == "政治") {
    Code = "0012";
  } else if ($name == "历史") {
    Code = "0013";
  } else if ($name == "信息技术") {
    Code = "0014";
  } else if ($name == "通用技术") {
    Code = "0015";
  } else {
    Code = "0";
  }
  return Code;
}

function getGrades() {
  $.ajax({
    type: "get",
    url: "../Index/getGrades",
    dataType: "json",
    success: function(data) {
      var html = "";
      $.each(data, function(k, v) {
        html += '<option value="' + k + '">' + v + "</option>";
      });
      $("#gradeForm").html(html);
    },
    error: function(e) {}
  });
}

function getTerms() {
  $.ajax({
    type: "get",
    url: "../Index/getTerms",
    dataType: "json",
    success: function(data) {
      var html = "";
      $.each(data, function(k, v) {
        html += '<option value="' + k + '">' + v + "</option>";
      });
      $("#termForm").html(html);
    },
    error: function(e) {}
  });
}

function getSubjects() {
  $.ajax({
    type: "get",
    url: "../Index/getSubjects",
    dataType: "json",
    success: function(data) {
      var html = "";
      $.each(data, function(k, v) {
        html += '<option value="' + k + '">' + v + "</option>";
      });
      $("#subjectForm").html(html);
    },
    error: function(e) {}
  });
}
