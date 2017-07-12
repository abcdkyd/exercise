var urlinf = decodeURI(document.location.href);
var temp = urlinf.split("?")[1].split("&");
var openid;
var oid;
$.each(temp, function(i, v) {
	var tmp=v.split("=");
    if(tmp[0] == "oid"){
        oid = tmp[1];
    } else if(tmp[0] == "openid"){
        openid = tmp[1];
		$('#url_cancel').attr('value',myurl.mip+'mall/myorder-a.html?openid='+openid);
		$('#url_success').attr('value',myurl.mip+'mall/myorder-c.html?openid='+openid);
		$('#url_fail').attr('value',myurl.mip+'mall/myorder-a.html?openid='+openid);
    }
});
$(function() {
    $.ajax({
        url: myurl.sip + 'mall-backend/orders/' + oid,
        type: 'get',
        dataType: 'json',
        success: function(data, status) {
            if (data.code == 0) {
                var pricesum = (data.data.price * data.data.quantity / 100).toFixed(2);
                $("#media-heading").append("<span class='infleft'>" + data.data.productName + "</span><span class='infright' >&yen;<em>" + (data.data.price / 100).toFixed(2) + "</em></span>");
                $("#descr").append("<span class='infleft '>" + data.data.productModelName + "</span><span class='infright myresult'>&times;" + data.data.quantity + "</span>");
                $("#receivername").html(data.data.receiverName);
                $("#receivercontact").html(data.data.receiverContact);
                $("#receiveraddress").html(data.data.receiverAddress);
                $("#pricesum").html(pricesum);
                $("input[name='orderid']").val(oid);
                //$("input[name='paymoney']").val(data.data.price * data.data.quantity / 100);
                $("input[name='paymoney']").val(0.01);
            } else {
                console.log(data.desc);
            }
        },
        error: function(xhr, str, e) {
            console.log(e.message);
        },
        complete: function() {
            hideLoading();
        }
    });
});

