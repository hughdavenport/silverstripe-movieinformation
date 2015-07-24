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
					$(this.textSelector).on('input', this.input)
					// Do initial search
					this.search();
				},
			input : function()
				{
					clearTimeout(movieinformation.timeout);
					movieinformation.timeout = setTimeout(movieinformation.search, 500);
				},
			search : function()
				{
					var search = $(movieinformation.textSelector).val();
					$(movieinformation.textSelector).parent().next()
					                                         .text(ss.i18n._t('MovieInformationField.SEARCHING'));
					$(movieinformation.chznSelector).hide();
					movieinformation.getMovies(search);
				},
			getMovies : function(search)
				{
					// Stop existing ones!
					if (this.currentRequest) {
						this.currentRequest.abort();
					}
					// Make it get more
					search += '*';
					this.currentSearch = search;
					// Get the URL of the controller
					var base = $('div#URLSegment_RO span').text();
					search = encodeURIComponent(search);
					var api = base + 'getmovies/' + search + '?stage=Stage';
					this.currentRequest = $.get(api, this.updateResults);
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
					// Delay changing back text until roughly when the dropdown
					// appears again (thanks to the chosen library)
					setTimeout(function() {
						$(movieinformation.textSelector).parent().next()
						                                         .text(ss.i18n._t('MovieInformationField.TEXT_DESCRIPTION'));
					}, 400);
				},
		};
		$(movieinformation.textSelector).entwine({
			onmatch: function() {
				movieinformation.init();
			},
		});
	});
})(jQuery);
