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
$('.myordere').attr('href','myorder-e.html?openid='+openid);

$(function() {
    $.ajax({
        url: myurl.sip + "mall-backend/users/wechat/" + openid + "/orders",
        type: 'get',
        dataType: 'json',
        success: function(data, status) {
            if (data.code == 0) {
                for (var i = 0; i < data.data.length; i++) {
                    if (data.data[i].status == 2) {
                        var pricesum = (data.data[i].price * data.data[i].quantity / 100).toFixed(2);
                        $(".fa-truck span").attr("class", "hint");
                        $('.container.mycontainer').prepend('<ul class="list-group"><li class="list-group-item clearfix"><span class="gloleft">订单号：' + data.data[i].oid + '</span><span class="gloright glocolor">已发货</span></li><li class="list-group-item clearfix"><a href="#" class="pull-left thumbnail glowidth1 mya1"><img src="images/' + data.data[i].productId + '.jpg" alt="" /></a><h4 class="media-heading myfont1 pull-right"><span class="myleft">' + data.data[i].productName + '</span><span class="myright" >&yen;<em>' + (data.data[i].price / 100).toFixed(2) + '</em></span></h4><p class="glogray myfont1"><span class="myleft">' + data.data[i].productModelName + '</span><span class="myright">&times;<em>' + data.data[i].quantity + '</em></span></p></li><li class="list-group-item clearfix"><p class="glocolor heji">合计：&yen;<span class="glocolor">' + pricesum + '</span></p><p class="sbtn"><a href="return.html?oid=' + data.data[i].oid + '&openid='+openid+'" class="btn btn-sm btn-default">退货</a><a class="btn btn-sm btn-default mymargin wuliu">查看物流</a><a class="btn btn-sm mybtn1 confirm" >确认收货</a></p><div style="display: none;"><ul class="wl" id="' + data.data[i].oid + '"></ul></div></li></ul>');
                    } else if (data.data[i].status == 0) {
                        $(".fa-credit-card span").attr("class", "hint");
                    } else if (data.data[i].status == 1) {
                        $(".fa-hourglass-half span").attr("class", "hint");
                    } else if (data.data[i].status == 3) {
                        $(".fa-hourglass-half span").attr("class", "hint");
                    } else if (data.data[i].status == 4) {
                        var pricesum = (data.data[i].price * data.data[i].quantity / 100).toFixed(2);
                        $(".fa-truck span").attr("class", "hint");
                        $('.container.mycontainer').prepend('<ul class="list-group"><li class="list-group-item clearfix"><span class="gloleft">订单号：' + data.data[i].oid + '</span><span class="gloright glocolor">退货中</span></li><li class="list-group-item clearfix"><a href="#" class="pull-left thumbnail glowidth1 mya1"><img src="images/' + data.data[i].productId + '.jpg" alt="" /></a><h4 class="media-heading myfont1 pull-right"><span class="myleft">' + data.data[i].productName + '</span><span class="myright" >&yen;<em>' + (data.data[i].price / 100).toFixed(2) + '</em></span></h4><p class="glogray myfont1"><span class="myleft">' + data.data[i].productModelName + '</span><span class="myright">&times;<em>' + data.data[i].quantity + '</em></span></p></li><li class="list-group-item clearfix"><em class="glocolor">合计：&yen;</em><span class="glocolor pricesum">' + pricesum + '</span></li></ul>');
                    }
                }
                //查物流
                $(".wuliu").bind("click", function() {
                    var oid = ($(this).parent().parent().prev().prev().children(".gloleft").text()).split("：")[1];
                    $.ajax({
                        type: 'get',
                        url: myurl.sip + 'mall-backend/orders/' + oid + '/delivery',
                        dataType: 'json',
                        success: function(data, status) {
                            if (data.code == 0) {
                                var ddata = JSON.parse(data.data);
                                eval("var tmp=$('#" + oid + "');");
                                var delivery = '';
                                tmp.html("");
                                if (ddata.status == 200) {
                                    $.each(ddata.data, function(i, v) {
                                        delivery += '<li><p>' + v.context + '</p><p>' + v.time + '</p></li>'
                                    });
                                    tmp.append(delivery);
                                } else {
                                    tmp.html("<li><p>暂时没有物流信息</p><p>");
                                }
                            } else {
                                console.log(data.desc);
                            }
                        },
                        error: function(xhr, str, e) {
                            console.log(e.message);
                        }
                    });
                    $(this).parent().next().animate({
                        height: 'toggle',
                    }, 100);
                    // $(this).parent().next().show();
                });
                $(".confirm").bind("click", function() {
                    var oid = ($(this).parent().parent().prev().prev().children(".gloleft").text()).split("：")[1];
                    $.ajax({
                        url: myurl.sip + 'mall-backend/orders/' + oid + '/confirm',
                        type: 'post',
                        dataType: 'json',
                        success: function(data, status) {
                            $("#tip span").html(data.desc);
                            $("#tip").fadeIn("slow");
                            setTimeout(function() {
                                $("#tip").fadeOut("slow");
                                window.location.reload();
                            }, 2000);
                        },
                        error: function(xhr, str, e) {
                            console.log(e.message);
                        }
                    });
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
