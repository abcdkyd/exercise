// var wechatInfo = navigator.userAgent.match(/MicroMessenger\/([\d\.]+)/i);
// if( !wechatInfo ) {
//     $("body").html('<img style="width: 100px;margin: 100px auto 0 auto;display: block;" src="images/icon80_smile.2x181c98.png"></br><p style="text-align: center;">请在微信客户端打开链接</p>');
// } else if ( wechatInfo[1] < "5.0" ) {
//     $("body").html('<img style="width: 100px;margin: 100px auto 0 auto;display: block;" src="images/icon80_smile.2x181c98.png"></br><p style="text-align: center;">本活动仅支持微信5.0以上版本</p>');
// }

var urlinf = decodeURI(document.location.href);
var temp = urlinf.split("?")[1].split("&");
var code;
var sid='';
var uid='';
var pid;
var openid;
$.each(temp, function(i, v) {
	var tmp=v.split("=");
    if(tmp[0] == "code"){
        code = tmp[1];
    } else if(tmp[0] == "pid"){
        pid = tmp[1];
    }
});
$(function(){	
	$.ajax({
		url:myurl.sip+"mall-backend/products/"+pid,
		type:'get',
		dataType:'json',
		beforeSend:function (XMLHttpRequest) {
		},
		success:function(data, status) {
			if (data.code == 0) { 
				if(data.data.status == 3){
					var priceView = (data.data.models[0].price/100).toFixed(2);
					$(".mycontainer").prepend('<img class="fullwidth" src="images/'+data.data.id+'.jpg" alt="">')
					$("#pro-name").html(data.data.name);
					$("#pro-name").next().html(data.data.desc);
					$(".price em").html(priceView);
					for(var i=0; i<data.data.models.length; i++){
						if(data.data.models[i].stock !== 0){
							$("#properties").append('<font data-id="'+data.data.models[i].id+'" data-price="'+data.data.models[i].price+'">'+data.data.models[i].name+'<i></i></font>');
							$(".myguige font:first-child").addClass("myactive");
						}
					}
					// 规格选择
					$('.myguige font').bind('click', function() {
						$(this).addClass('myactive').siblings().removeClass('myactive');
						var pricechange = ($(this).data("price")/100).toFixed(2);
						$(".price em").html(pricechange);
					 });
				}else{
					document.location.href = myurl.mip + "mall/sold.html";
				}
			} else {
				console.log(data.desc);
			}
		},
		error:function(xhr, str, e){
			console.log(e.message);
		},
		complete:function(){
			hideLoading();
		}
	});
});
//跳转到支付页
function jumppay(){
	
	$.ajax({
		url:myurl.sip+"mall-backend/wechat/openid/",
		type:'get',
		dataType:'json',
		data:{
			code:code
		},
		success:function(data, status){
			openid = data.openid;
			var quantity = $("#quantity").val();
			var pmid = $(".myactive").data("id");
			var price = $(".myactive").data("price");
			$.ajax({
				url:myurl.sip + 'mall-backend/orders',
				type:'post',
				dataType:'json',
				data:{
					pmid:pmid,
					quantity:quantity,
					price:price,
					note:'请尽快发货',
					name:'我我我',
					phone:'13725899654',
					location:'南海',
					uid:uid,
					openid:openid,
					sid:sid
				},
				beforeSend:function (XMLHttpRequest) {
					showLoading();
				},
				success: function(data, status) {
					if (data.code == 0) {
						var oid = data.data.oid;
						document.location.href = myurl.mip + "mall/pay.html?oid="+oid+"&openid="+openid;
					} else {
						alert(data.desc);
					}
				},
				error: function(xhr, str, e){
					console.log(e.message);
				},
				complete:function(){
					hideLoading();
				}
			});
		},
		error:function(xhr, str, e){
			console.log(e.message);
		}
	});

}

//加的效果
$(".myadd").bind('click',function(){
var n=$(this).prev().val();
var num=parseInt(n)+1;
if(num==0){ return;}
$(this).prev().val(num);
});
//减的效果
$(".mycut").click(function(){
var n=$(this).next().val();
var num=parseInt(n)-1;
if(num==0){ return;}
$(this).next().val(num);
});
//解决ios click 300ms延迟
window.addEventListener('load', function() {
FastClick.attach(document.body);
}, false);
