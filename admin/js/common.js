var myLocalStorage = new storage();
var sidebarStatus = myLocalStorage.get("sidebarStatus"); //1关闭2展开
var id = myLocalStorage.get("id");
var dataLevel = myLocalStorage.get("dataLevel");
var menuArr = [{"name":"首页","dataLevel":1,"id":"shouye","dataOpen":"index.html","icon":"fa fa-book"},
{"name":"题库管理","dataLevel":1,"id":"menu_a","dataOpen":"","icon":"fa fa-dashboard","child":[
  {"name":"基础分类","dataLevel":2,"id":"menu_a_1","dataOpen":"userlist.html","icon":"glyphicon glyphicon-th-list"},
  {"name":"基础试题","dataLevel":2,"id":"menu_a_2","dataOpen":"userlist.html","icon":"glyphicon glyphicon-th-list"},
]},
{"name":"用户管理","dataLevel":1,"id":"menu_b","dataOpen":"userlist.html","icon":"glyphicon glyphicon-th-list"}];

$('#topuser').html(myLocalStorage.get('username'));
var menuHtml = "";
$.each(menuArr,function(k,v){
  if(v.dataLevel == 1 && v.dataOpen != ""){
    menuHtml += '<li id="'+v.id+'"><a name="data-route" dataOpen="'+v.dataOpen+'" dataLevel="'+v.dataLevel+'"><i class="'+v.icon+'"></i><span>'+v.name+'</span></a></li>';
  }else if(v.dataLevel == 1 && v.dataOpen == ""){
    menuHtml += '<li class="treeview">';
    menuHtml += '<a href="#">';
    menuHtml += '<i class="'+v.icon+'"></i>';
    menuHtml += '<span>'+v.name+'</span>';
    menuHtml += '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>';
    menuHtml += '</a>';
    menuHtml += '<ul class="treeview-menu">';
    $.each(v.child,function(kk,vv){
      menuHtml += '<li id="'+vv.id+'">';
      menuHtml += '<a name="data-route" dataOpen="'+vv.dataOpen+'" dataLevel="'+vv.dataLevel+'">';
      menuHtml += '<i class="'+vv.icon+'"></i>'+vv.name+'</a>';
      menuHtml += '</li>';
    })
    menuHtml += '</ul>';
    menuHtml += '</li>';
  }
})

$('.sidebar-menu').append(menuHtml);
             
            
          
        

sidebarStatus == null ? (sidebarStatus = 1) : sidebarStatus;
if (sidebarStatus == 1) {
  $("body").addClass("sidebar-collapse");
} else {
  $("body").removeClass("sidebar-collapse");
}

$('#'+id).addClass("active");
if (dataLevel == 2) {
    $('#'+id)
    .parent()
    .parent()
    .addClass("menu-open active");
}

$("a.sidebar-toggle").on("click", function() {
  if ($("body").hasClass("sidebar-collapse")) {
    myLocalStorage.set("sidebarStatus",2);
  } else {
    myLocalStorage.set("sidebarStatus",1);
  }
});

$('.sidebar-menu a[name="data-route"]').on("click", function() {
  dataLevel = $(this).attr("datalevel");
  id = $(this).parent('li').attr("id");

  var url = $(this).attr("dataOpen");

  myLocalStorage.set("dataLevel",dataLevel);
  myLocalStorage.set("id",id);

  goRedirect(url);
});

function goRedirect(url) {
  window.location.href = url;
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
