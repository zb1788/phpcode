//判断是否是IE
function isIE()
{
    if(!!window.ActiveXObject || "ActiveXObject" in window)
        return true;
    else
        return false;
}