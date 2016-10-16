$(document).ready(function(){
	Object.size = function(obj) {
	    var size = 0, key;
	    for (key in obj) {
	        if (obj.hasOwnProperty(key)) size++;
	    }
	    return size;
	};

	function asd(temp) {
		var array = $.map(temp, function(value, index) {
		    return [value];
		});
	}

	

	

	var from = $('.loader').attr('data-from');
	var to = $('.loader').attr('data-to');
	$.get('http://api.tracker.k50.ru/api/call/ext/77212604978137/'+from+'/'+to+'?apiKey=e072665d-438d-409a-858f-9f827ee7c9da', function(data){
		console.log(data);
	
		
		$('.loader').animate({opacity:0}, function(){
			$('.loader').css('display','none');
			$('.table').css('display','block');
			$('.table').animate({opacity:1});
		});
		var i = 0;
		for ( datas in data) {
			var minutes = Math.floor(data.valueOf()[datas].duration / 60);
			var seconds = data.valueOf()[datas].duration - minutes * 60;
			if (seconds < 10) {
				seconds = '0'+seconds;
			}
			var tm = minutes+':'+seconds;


			$('.callersdata').prepend('<tr><td>'+data.valueOf()[datas].start_time+'</td><td>'+data.valueOf()[datas].caller_phone+'</td><td>'+data.valueOf()[datas].called_phone+'</td><td>'+tm+'</td><td>'+data.valueOf()[datas].is_matching+'</td><td>'+data.valueOf()[datas].analytics_client_id+'</td><td>'+data.valueOf()[datas].uuid+'</td></tr>');
			i++;
		}
	});
});