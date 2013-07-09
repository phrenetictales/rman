var TrackNumber = 0;

$('#addtrack-dialog').dialog({
	autoOpen: false,
	width: 600,
	height: 300
});
$('#track-add').on('click', function() {
	
	var title = $('#track-title').val();
	var $input = $('<input type="hidden" name="tracks[' + TrackNumber + '][title]" value="' + title + '"/>');
	var artistnames = [];
	
	$.each($('#track-artists').tagit('assignedTags'), function(i, artist_id) {
		
		$input.append(
			'<input type="hidden" name="tracks['+TrackNumber+'][artists][]" value="' + artist_id + '"/>'
		);
		
		for (i in artists) {
			if (artists[i].id == artist_id) {
				artistnames.push(artists[i].label);
				break;
			}
		}
	});
	
	
	$('#tracks').append(
		$('<li/>')
			.html($input)
			.append(artistnames.join(' & ') + ' - ' + title)
	);
	$('#tracks').sortable('refresh');
	
	TrackNumber++;
	
	$('#addtrack-dialog input').val('');
	$('#track-artists').tagit("removeAll");
	
	$('#addtrack-dialog').dialog('close');
});
$('#tracks').sortable();
$('#addtrack').on('click', function() {
	$('#addtrack-dialog').dialog('open');
	return false;
});
$('#track-artists').tagit({
	availableTags: artists,
	allowDuplicates: false,
	fieldName: 'artists'
});
for (var i in default_artists) {
	$('#track-artists').tagit('createTag', default_artists[i]);
}
