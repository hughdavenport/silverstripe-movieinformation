(function($) {
	$.entwine(function($) {
		var movieinformation = {
			textSelector   : 'input#Form_EditForm_Title-Text',
			selectSelector : 'select#Form_EditForm_Title-Select',
			chznSelector   : 'div#Form_EditForm_Title-Select_chzn',
			currentSearch  : '',
			currentRequest : undefined,
			init : function()
				{
					this.currentSearch = '';
					// Detect changes
					$(movieinformation.textSelector).on('input', movieinformation.input)
					// Do initial search
					movieinformation.input();
				},
			input : function()
				{
					var search = $(movieinformation.textSelector).val();
					$(movieinformation.textSelector).parent().next()
							.text(ss.i18n._t('MovieInformationField.SEARCHING'));
					$(movieinformation.chznSelector).hide();
					movieinformation.getMovies(search, movieinformation.updateResults);
				},
			getMovies : function(search, callback)
				{
					// Stop existing ones!
					if (movieinformation.currentRequest) {
						movieinformation.currentRequest.abort();
					}
					// Make it get more
					search += '*';
					movieinformation.currentSearch = search;
					// Get the URL of the controller
					var base = $('div#URLSegment_RO span').text();
					search = encodeURIComponent(search);
					var api = base + 'getmovies/' + search + '?stage=Stage';
					movieinformation.currentRequest = $.get(api, callback);
				},
			updateResults : function(json)
				{
					var $select = $(movieinformation.selectSelector);
					var $selectClone = $select.clone();
					$selectClone.empty();
					for(var i in json['results']) {
						if (json['search'] != movieinformation.currentSearch) {
							$selectClone.empty();
							delete $selectClone;
							return;
						}
						var title = json['results'][i];
						title = title.replace('&#039;', "'");
						var $option = $('<option>').val(title)
												   .text(title);
						if (i == 0) {
							$option.attr('selected', 'selected');
						}
						$selectClone.append($option);
					}
					if (json['results'].length == 0) {
						$selectClone.attr('data-placeholder', 'No results');
						$selectClone.append($('<option>'));
					} else {
						$selectClone.removeAttr('data-placeholder');
					}
					$select.replaceWith($selectClone);
					console.log("Searched " + json['search']);
					$(movieinformation.textSelector).parent().next()
							.text(ss.i18n._t('MovieInformationField.TEXT_DESCRIPTION'));
				},
		};
		$(movieinformation.textSelector).entwine({
			onmatch: function() {
				movieinformation.init();
			},
		});
	});
})(jQuery);