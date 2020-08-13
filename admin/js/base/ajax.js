/**
 *	请求json数据
 */
function ajaxJson(url, params = {},fun, type = "get", async = true, charset = "utf-8", timeout = 7000) {
    $.ajax({
        url: url,
        data: params,
        type: type,
        async: async,
        dataType: 'json',
        scriptCharset: charset,
        timeout: timeout,
        beforeSend: function() {

		},
        success: function(data) {
            fun(data);
        },
        error: function(xmlHttpRequest, textStatus, errorThrown) {
			if(textStatus == "timeout"){
				alert("请求超时");
			}
			if(textStatus == "error"){
				alert("请求接口失败");
			}
			console.log("errorUrl:"+url+"error: textStatus:"+textStatus);
			for(name in errorThrown){
				console.log("Error-"+name+":"+errorThrown[name]);
			}
		}
    })
}

/**
 *	jsonp请求
 */
function ajaxWithJsonp(url, params = {},fun, jsoncallBack, async = true, charset = "utf-8", timeout = 10000) {
    $.ajax({
        url: url,
        data: params,
        type: 'get',
        async: async,
        dataType: 'jsonp',
        jsonp: jsoncallBack,
        scriptCharset: charset,
        timeout: timeout,
        beforeSend: function() {

		},
        success: function(data) {
            fun(data);
        },
        error: function(xmlHttpRequest, textStatus, errorThrown) {
			if(textStatus == "timeout"){
				alert("请求超时");
			}
			if(textStatus == "error"){
				alert("请求接口失败");
			}
			console.log("errorUrl:"+url+"error: textStatus:"+textStatus);
			for(name in errorThrown){
				console.log("Error-"+name+":"+errorThrown[name]);
			}
		}
    })
}

/**
 *	获取url参数
 */
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return decodeURI(decodeURI(unescape(r[2])));
    return null;
}