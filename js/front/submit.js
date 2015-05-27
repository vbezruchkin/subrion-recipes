$(function()
{
	$("#lyric_form").ajaxForm(
	{
		data: {ajax: 1},
		dataType: 'xml',
		beforeSubmit: function(formArray, jqForm)
		{
			$(jqForm).block({message: null});
		},
		success: showResponse			
	});
});

// post-submit callback
function showResponse(responseText, statusText)
{
	var error = $("error", responseText).text();
	var msg = $("msg", responseText).text();
	$("#lyric_form").unblock();

	if (error)
	{
		$("#lyric_alert ul").html(msg);
		$("#lyric_alert").show();
	}
	else
	{
		location.href = intelli.config.ia_url;
	}
}