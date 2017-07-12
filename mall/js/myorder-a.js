var urlinf = decodeURI(document.location.href);
var temp = urlinf.split("?")[1].split("&");
var openid;
$.each(temp, function(i, v) {
	var tmp=v.split("=");
    if(tmp[0] == "openid"){
        openid = tmp[1];
    }
});

$('.myorderc').attr('href','myorder-c.html?openid='+openid);
$('.myorderd').attr('href','myorder-d.html?openid='+openid);
$('.myordere').attr('href','myorder-e.html?openid='+openid);
$('#url_cancel').attr('value',myurl.mip+'mall/myorder-a.html?openid='+openid);
$('#url_success').attr('value',myurl.mip+'mall/myorder-c.html?openid='+openid);
$('#url_fail').attr('value',myurl.mip+'mall/myorder-a.html?openid='+openid);
$(function() {
    $.ajax({
        url: myurl.sip + "mall-backend/users/wechat/" + openid + "/orders",
        type: 'get',
        dataType: 'json',
        success: function(data, status) {
            if (data.code == 0) {
                for (var i = 0; i < data.data.length; i++) {
                    if (data.data[i].status == 0) {
                        var pricesum = (data.data[i].price * data.data[i].quantity / 100).toFixed(2);
                        $(".fa-credit-card span").attr("class", "hint");
                        $('.container.mycontainer').prepend('<ul class="list-group"><li class="list-group-item clearfix"><span class="gloleft">订单号：' + data.data[i].oid + '</span><span class="gloright glocolor">待付款</span></li><li class="list-group-item clearfix"><a href="#" class="pull-left thumbnail glowidth1 mya1"><img src="images/' + data.data[i].productId + '.jpg" alt="" /></a><h4 class="media-heading myfont1 pull-right"><span class="myleft">' + data.data[i].productName + '</span><span class="myright" >&yen;<em>' + (data.data[i].price / 100).toFixed(2) + '</em></span></h4><p class="glogray myfont1"><span class="myleft">' + data.data[i].productModelName + '</span><span class="myright">&times;<em>' + data.data[i].quantity + '</em></span></p></li><li class="list-group-item clearfix"><em class="glocolor">合计：&yen;</em><span class="glocolor pricesum">' + pricesum + '</span><span class="fixfloat1">fixfloat</span><input value="付款" class="btn btn-sm mybtn1 gloright wcallpay" type="submit" /><input value="取消" class="btn btn-sm btn-default gloright cancel" type="button" /></li></ul>');
                    } else if (data.data[i].status == 1) {
                        $(".fa-hourglass-half span").attr("class", "hint");
                    } else if (data.data[i].status == 2) {
                        $(".fa-truck span").attr("class", "hint");
                    } else if (data.data[i].status == 3) {
                        $(".fa-hourglass-half span").attr("class", "hint");
                    } else if (data.data[i].status == 4) {
                        $(".fa-truck span").attr("class", "hint");
                    }
                }
                $(".cancel").bind("click", function() {
                    var oid = ($(this).parent().prev().prev().children(".gloleft").text()).split("：")[1];
                    $.ajax({
                        type: 'post',
                        url: myurl.sip + 'mall-backend/orders/' + oid + '/cancel',
                        data: null,
                        dataType: 'json',
                        success: function(data, status) {
                            $("#tip span").html(data.desc);
                            $("#tip").fadeIn("slow");
                            setTimeout(function() {
                                $("#tip").fadeOut("slow");
                                window.location.reload();
                            }, 2000)
                        },
                        error: function(xhr, str, e) {
                            console.log(e.message);

                        }
                    });
                });
                $(".wcallpay").bind("click", function() {
                    var oid = ($(this).parent().prev().prev().children(".gloleft").text()).split("：")[1];
                    var paypricesum = $(this).prev().prev().text();
                    $("input[name='orderid']").val(oid);
                    //$("input[name='paymoney']").val(paypricesum);
                    $("input[name='paymoney']").val(0.01);
                });
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
