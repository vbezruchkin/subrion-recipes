Ext.onReady(function()
{
	var vUrl = intelli.config.admin_url + '/ajax/manage/' + item + '/';
	
	if ('recipes' == item)
	{
		$("#account").autocomplete({ url: intelli.config.admin_url + '/ajax/manage/recipes/', minChars: 2});
		
		// perform recipes actions
		$("input[name='artist']").blur(function()
		{
			artistname = $("input[name='artist']").val();
			
			$.get(intelli.config.admin_url + '/ajax/manage/albums/',  {artist: artistname}, function(data)
			{
				var options = $("#album");
				var data = eval('(' + data + ')');
				
				// remove items
				$('#album option').each(function(i, option)
				{
					$(option).remove();
				});
				
				options.append($("<option />").val(0).text(' - please select artist first - '));
				
				$.each(data.data, function(item, value)
				{
					options.append($("<option />").val(value.id).text(value.title));
				});
			});

			$("select[name='album']").removeAttr("disabled");
		});
		
		// fill in albums
		artistname = $("input[name='artist']").val();
		if ('' == artistname)
		{
			$("select[name='album']").attr("disabled","disabled");
		}
	}
	
	$("input[name='title'], input[name='title_alias']").each(function()
	{
		$(this).blur(function()
		{
			fillUrlBox();
		});
	});

	function fillUrlBox()
	{
		var title = ('' == $("input[name='title_alias']").val()) ? $("input[name='title']").val() : $("input[name='title_alias']").val();
		var artist = (Ext.get('artist')) ? $("input[name='artist']").val() : '';

		if('' != title)
		{
			$.get(vUrl, {action: 'getalias', title: title, artist: artist}, function(data)
			{
				var data = eval('(' + data + ')');

				if('' != data.data)
				{
					$("#title_url").text(data.data);
					$("#title_box").fadeIn();
				}
				else
				{
					$("#title_box").fadeOut();
				}
			});
		}
		else
		{
			$("#title_box").fadeOut();
		}
	}
});