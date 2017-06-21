var Backend = {
	SITE_PATH: 'http://www.futurameteo.eu/backend/',

	API: {
		init: function(type) {
			switch(type) {
				case 'create':
						// If API-type changes
						$('#api_create select[name="type"]').change(function() {
							Backend.API.Create.renderApiOptions($(this).val());
						});

						$('#api_create').submit(function() {
							var api_opts = Backend.API.Create.getApiOptions();
							Backend.API.Create.create(api_opts);
						});
					break;
			}
		},

		Create: {
			getApiOptions: function() {
				var data = [];

				// Alle inputs
				$('#api_type_options input').each(function() {
					var name = $(this).attr('name');

					data[data.length] = {name: name, value: $(this).val()};
				});

				// Alle select
				$('#api_type_options select').each(function() {
					var name = $(this).attr('name');

					data[data.length] = {name: name, value: $(this).val()};
				});

				// Alle textarea's
				$('#api_type_options textarea').each(function() {
					var name = $(this).attr('name');

					data[data.length] = {name: name, value: $(this).val()};
				});

				return data;
			},

			renderApiOptions: function(type) {
				if (type == 'k')
					return;

				$.ajax({
					type: 'POST',
					url: Backend.SITE_PATH + 'apiOptions',
					data: {
						type: type
					},
					dataType: 'html',
					complete: function(xhr) {
						$('#api_type_title').html(type);
						$('#api_type_options').html(xhr.responseText)
					}
				})
			},

			create: function(opts) {
				for(var i=0;i<opts.length;i++) {
					var opt = opts[i];

					$('<input />').attr('type', 'hidden')
							.attr('name', opt.name)
							.attr('value', opt.value)
							.appendTo('#api_create');
				}
			}
		}
	}
};