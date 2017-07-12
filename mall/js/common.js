var myurl = {
	sip:"http://gd.imobilewiki.com:8080/",
	mip:"http://gd.imobilewiki.com/",
	orderurl:"http://gd.imobilewiki.com:8080/mall-backend/users/wechat/test_openid/orders"
}
// function loadData(url, data, type, successCallback, beforeSendcall, completecall) {
// 	$.ajax({
// 		method: type || 'GET',
// 		url: url,
// 		data: data || {},
// 		dataType: 'json',
// 		beforeSend: beforeSendcall || function(xhr) {
//             showLoading();
//         },
// 		success: successCallback || function(data, status) { // success callback
// 			if (data.code == 0) {
// 				console.log(data.desc);
// 			} else {
// 				console.log(data.desc);
// 			}
// 		},
// 		error: function(xhr, str, e) {
// 			$('#response').text(e.message);
// 		},
// 		complete: completecall || function(msg) {
// 			hideLoading();
// 		}
// 	});
// }