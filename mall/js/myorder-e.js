var urlinf = decodeURI(document.location.href);
var temp = urlinf.split("?")[1].split("&");
var openid;
$.each(temp, function(i, v) {
	var tmp=v.split("=");
    if(tmp[0] == "openid"){
        openid = tmp[1];
    }
});

$('.myordera').attr('href','myorder-a.html?openid='+openid);
$('.myorderc').attr('href','myorder-c.html?openid='+openid);
$('.myorderd').attr('href','myorder-d.html?openid='+openid);

$(function() {
    $.ajax({
        url: myurl.sip + "mall-backend/users/wechat/" + openid + "/orders",
        type: 'get',
        dataType: 'json',
        success: function(data, status) {
            if (data.code == 0) {
                for (var i = 0; i < data.data.length; i++) {
                    if (data.data[i].status == 0) {
                        $(".fa-credit-card span").attr("class", "hint");
                    } else if (data.data[i].status == 1) {
                        $(".fa-hourglass-half span").attr("class", "hint");
                    } else if (data.data[i].status == 2) {
                        $(".fa-truck span").attr("class", "hint");
                    } else if (data.data[i].status == 3) {
                        $(".fa-hourglass-half span").attr("class", "hint");
                    } else if (data.data[i].status == 4) {
                        $(".fa-truck span").attr("class", "hint");
                    } else if (data.data[i].status == 7) {
                        var pricesum = (data.data[i].price * data.data[i].quantity / 100).toFixed(2);
                        $('.container.mycontainer').prepend('<ul class="list-group"><li class="list-group-item clearfix"><span class="gloleft">订单号：' + data.data[i].oid + '</span><span class="gloright glocolor">交易成功</span></li><li class="list-group-item clearfix"><a href="#" class="pull-left thumbnail glowidth1 mya1"><img src="images/' + data.data[i].productId + '.jpg" alt="" /></a><h4 class="media-heading myfont1 pull-right"><span class="myleft">' + data.data[i].productName + '</span><span class="myright" >&yen;<em>' + (data.data[i].price / 100).toFixed(2) + '</em></span></h4><p class="glogray myfont1"><span class="myleft">' + data.data[i].productModelName + '</span><span class="myright">&times;<em>' + data.data[i].quantity + '</em></span></p></li><li class="list-group-item clearfix"><em class="glocolor">合计：&yen;</em><span class="glocolor pricesum">' + pricesum + '</span></li></ul>');
                    } else if (data.data[i].status == 8) {
                        var pricesum = (data.data[i].price * data.data[i].quantity / 100).toFixed(2);
                        $('.container.mycontainer').prepend('<ul class="list-group"><li class="list-group-item clearfix"><span class="gloleft">订单号：' + data.data[i].oid + '</span><span class="gloright glocolor">已退款</span></li><li class="list-group-item clearfix"><a href="#" class="pull-left thumbnail glowidth1 mya1"><img src="images/' + data.data[i].productId + '.jpg" alt="" /></a><h4 class="media-heading myfont1 pull-right"><span class="myleft">' + data.data[i].productName + '</span><span class="myright" >&yen;<em>' + (data.data[i].price / 100).toFixed(2) + '</em></span></h4><p class="glogray myfont1"><span class="myleft">' + data.data[i].productModelName + '</span><span class="myright">&times;<em>' + data.data[i].quantity + '</em></span></p></li><li class="list-group-item clearfix"><em class="glocolor">合计：&yen;</em><span class="glocolor pricesum">' + pricesum + '</span></li></ul>');
                    }
                }
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
