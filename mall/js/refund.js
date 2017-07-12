window.onload = function(){hideLoading()};
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

$("#refund").bind("click",function(){
    if($("#reason1").val() == ""){
        $("#tip span").html("请选择退款原因");
        $("#tip").fadeIn("slow");
        setTimeout(function(){
            $("#tip").fadeOut("slow");
        },2000);
    }else{
    	var reason = $("#reason1").val()+$("#reason2").val();
        $.ajax({
        url:myurl.sip + 'mall-backend/orders/'+oid+'/refund',
        type:'post',
        dataType: 'json',
        data:{
            reason:reason
        },
        beforeSend: function(XMLHttpRequest){
            showLoading();
        },
        success:function(data, status){
        	hideLoading();
            $("#tip span").html(data.desc);
            $("#tip").fadeIn("slow");
            setTimeout(function(){
                $("#tip").fadeOut("slow");
                window.location.href = myurl.mip + 'mall/myorder-c.html?openid='+openid;
            },2000);
        },
        error:function(xhr, str, e){
            console.log(e.message);
        },
        complete:function(){
            hideLoading();
        }
    });
    }

});