function twoColumnsSelect(columnFromId, columnToId, withGroup) {
	$('#' + columnFromId + ' option:selected').each(function() {
		if(withGroup == true) {
			var optGroupLabel = $(this).parent().attr('label');
			var group = $('#' + columnToId).find("optgroup[label='" + optGroupLabel + "']");
			//$("<option>").val($(this).val()).text($(this).text()).appendTo($(group));
			$(group).append($("<option>").val($(this).val()).text($(this).text()));
		} else {
			$("<option>").val($(this).val()).text($(this).text()).appendTo($('#' + columnToId));
		}
		$(this).remove();
	});
}
