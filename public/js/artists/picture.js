$('#picture').fileupload({
	dataType: 'json',
	done: function(ev, data) {
		$.each(data.result, function(index, file) {
			$('#picture')
				.siblings('img')
				.attr('src', '/pictures/display/' + file.storename);
			$('#picture')
				.siblings('input[name="picture_id"]')
				.val(file.id);
		});
	},
	progressall: function(e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('#progress .bar').css('width', progress + '%');
	}
});
