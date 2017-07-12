window.onload = function() { hideLoading() };
var urlinf = decodeURI(document.location.href);
var temp = urlinf.split("?")[1].split("&");
var oid;
var openid;
$.each(temp, function(i, v) {
	var tmp=v.split("=");
    if(tmp[0] == "oid"){
        oid = tmp[1];
    } else if(tmp[0] == "openid"){
        openid = tmp[1];
    }
});
$("#return").bind("click", function() {
    if ($("#reason1").val() == "") {
        $("#tip span").html("请选择退货原因");
        $("#tip").fadeIn("slow");
        setTimeout(function() {
            $("#tip").fadeOut("slow");
        }, 2000);
    } else if ($("#reason2").val() == "") {
        $("#tip span").html("请选择物流公司");
        $("#tip").fadeIn("slow");
        setTimeout(function() {
            $("#tip").fadeOut("slow");
        }, 2000);
    } else if ($("#reason3").val() == "") {
        $("#tip span").html("请填写物流单号");
        $("#tip").fadeIn("slow");
        setTimeout(function() {
            $("#tip").fadeOut("slow");
        }, 2000);
        $("#reason3").focus();
    } else if ($("#reason4").val() == "") {
        $("#tip span").html("请填写联系电话");
        $("#tip").fadeIn("slow");
        setTimeout(function() {
            $("#tip").fadeOut("slow");
        }, 2000);
        $("#reason4").focus();
    } else {
        var reason = $("#reason1").val() + ' ' + $("#reason2").val() + ' ' + $("#reason3").val() + ' ' + $("#reason4").val() + ' ' + $("#reason5").val();
        console.log(reason);
        $.ajax({
            url: myurl.sip + 'mall-backend/orders/' + oid + '/return',
            type: 'post',
            dataType: 'json',
            data: {
                reason: reason
            },
        beforeSend: function(XMLHttpRequest){
            showLoading();
        },
            success: function(data, status) {
            	hideLoading();
                $("#tip span").html(data.desc);
                $("#tip").fadeIn("slow");
                setTimeout(function() {
                    $("#tip").fadeOut("slow");
                    window.location.href = myurl.mip + 'mall/myorder-d.html?openid='+openid;
                }, 2000);
            },
            error: function(xhr, str, e) {
                console.log(e.message);
            },
        complete:function(){
            hideLoading();
        }
        });
    }

});
