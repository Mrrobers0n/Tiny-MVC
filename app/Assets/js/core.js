/***********************************************************
 * CORE
 * Core library for basic Javascript functionalities
 * Only re-usable things should be programmed here
 ***********************************************************/

var Core;

Core = {
	SITE_PATH: 'http://www.robbe-ingelbrecht.be',     // SITE_PATH

	init: function () {
		Core.ArrowUp.init();
	},

	searchBox: {
		fieldID: null,
		minLength: null,
		limit: null,

		loop: null,
		lastKey: null,

		results: null,

		template: null,

		init: function(options) {
			if (options == undefined)
				return;

			// Set settings
			Core.searchBox.fieldID = options.id != undefined ? options.id : null;                    // No ID ? Then null
			Core.searchBox.minLength = options.minLength != undefined ? options.minLength : 3;       // Standard minium 3 chars before search
			Core.searchBox.limit = options.limit != undefined ? options.limit : 10;                  // No limit = standard of 10

			Core.searchBox.remote = options.remote != undefined ? options.remote : null;
			Core.searchBox.template = options.template != undefined ? options.template : null;

			$('#'+options.id).focus(function() {
				Core.searchBox.loop = setInterval(function() {
					Core.searchBox.textFieldChanged();
				}, 250)
			})

			$('#'+options.id).blur(function() {
				clearInterval(Core.searchBox.loop);
				//Core.searchBox.removeResultMenu();
			})
		},

		textFieldChanged: function() {
			var field = $('#'+Core.searchBox.fieldID),
					key = field.val();

			if (key.length >= Core.searchBox.minLength && Core.searchBox.lastKey != key) {
				Core.searchBox.getSearchResults(key);

				var checkResults = setInterval(function() {
					if (Core.searchBox.results != null && Core.searchBox.results.length > 0) {
						Core.searchBox.renderResults(Core.searchBox.results, key);
						clearInterval(checkResults);
					}
					else if (Core.searchBox.results.length == 0) {
						Core.searchBox.removeResultMenu();
						clearInterval(checkResults);
					}
				}, 250);

				Core.searchBox.lastKey = key;
			}
			else if (key.length < Core.searchBox.minLength) {
				Core.searchBox.removeResultMenu();
			}
		},

		getSearchResults: function(key) {
			$.ajax({
				type: 'GET',
				url: Core.SITE_PATH + '/api/search/' + key,
				dataType: 'json',
				success: function(json) {
					Core.searchBox.results = json;
				}
			})
		},

		renderResults: function(results, key) {
			var menu = Core.searchBox.createResultMenu(),
					item = null

			key = key.toLowerCase();

			for(var i in results) {
				if (i == Core.searchBox.limit)
					break;

				var result = (Core.searchBox.template == null) ? results[i][0] : results[i][Core.searchBox.template.primary],
						result_clean = result;

				if (result.toLowerCase().indexOf(key) != -1) {
					var index = result.toLowerCase().indexOf(key),
							temp  = result.substr(0, index);

					temp += '<strong>' + result.substr(index, key.length) + '</strong>';
					temp += result.substr(index+key.length);

					if (Core.searchBox.template != null) {
						results[i][Core.searchBox.template.primary] = temp;
					}
					else
						result = temp;
				}

				if (Core.searchBox.template == null) {
					item = '<div class="result">';
						item += '<a href="javascript:void(0)" onclick="Core.searchBox.insertResultToField(\'' + result_clean +'\')">' + result + '</a>';
					item += '</div>';
				}
				else {
					// Loop trough each value
					item = Core.searchBox.template.html;

					for(var j in Core.searchBox.template.fields) {
						var field = Core.searchBox.template.fields[j],
								index = item.indexOf('_'+field.toUpperCase()+'_'),
								temp = null;

						while (item.indexOf('_'+field.toUpperCase()+'_') >= 0) {
							temp = item.substr(0, index);
							temp += results[i][field];
							temp += item.substr(index+field.length+2);

							item = temp;
						}
					}

					item = item.replace('_RESULT_', result_clean);
				}


				menu.append(item);
			}
		},

		createResultMenu: function() {
			Core.searchBox.removeResultMenu();

			var field = $('#'+Core.searchBox.fieldID),
					x = field.offset().left,
					y = field.offset().top,
					width = field.width();

			y += field.height();
			width += width*0.05;     // Add 5% of field width

			var menu = $('<div/>', {
				id: 'resultMenu',
				html: '',
				style: 'position: absolute;top:'+y+'px;left:'+x+'px;width:' + width+'px;'
			}).appendTo('body');


			return menu;
		},

		removeResultMenu: function() {
			$('#resultMenu').remove();
		},
		insertResultToField: function(result) {
			Core.searchBox.lastKey = result;
			$('#'+Core.searchBox.fieldID).val(result).focus().trigger('change');
			Core.searchBox.removeResultMenu();
		}
	},

	ArrowUp: {
		init: function() {
			this._drawArrow();
			this._setEventHandlers();
			this._toggleArrow();
		},

		_drawArrow: function() {
			let body = document.getElementsByTagName('body')[0]
				,	arrow = document.createElement('div')
				,	arrow_a = document.createElement('a');


			// Onze link met wat eigenschappen en inhoud voorzien
			arrow_a.setAttribute('href', '#');
			arrow_a.setAttribute('title', 'Ga terug naar boven');
			arrow_a.innerHTML = '<i class="icon fa-arrow-up"></i>';

			// Onze link toevoegen aan de container div
			arrow.appendChild(arrow_a);

			// Onze container div een ID geven voor verder gebruiker
			arrow.setAttribute('id', 'arrow_up');
			arrow.style.display = 'none';

			// Onze container toevoegen aan het body-element
			body.appendChild(arrow);
		},

		_toggleArrow: function() {
			let doc_scroll = document.documentElement.scrollTop
				,	win_height = window.innerHeight
				,	arrow = document.getElementById('arrow_up');

			if (doc_scroll > win_height) {
				$(arrow).fadeIn('fast');
			}
			else {
				$(arrow).fadeOut('slow');
			}
		},

		_setEventHandlers: function() {
			// Onze link-element opzoeken
			let arrow_a = document.getElementById('arrow_up').childNodes[0];

			// Een EventListener toevoegen
			arrow_a.addEventListener('click', function(e) {
				// Standaard actie stoppen
				e.preventDefault();

				// Naarboven scrollen aan de hand van een jQuery code
				$("html, body").animate({ scrollTop: 0 }, "slow");
			});

			document.addEventListener('scroll', function() {
				Core.ArrowUp._toggleArrow();
			})
		}
	}
};

$(document).ready(function(e) {
	Core.init();
	//Core.Pagination.init();
});

var GbGenerator = {

	/**
	 * Initialiseert object
	 * Set event handlers
	 * ...
	 */
	init: function() {

		// Change! - button click handler
		$('#button').click(function() {
			GbGenerator.eventHandlers.btnChangeClick();
		});

		//...
	},

	/**
	 * EventHandlers
	 * Alle methoden die worden aangesproken door 'n event
	 */
	eventHandlers: {
		btnChangeClick: function() {
			//...
		},

		btnGenerate: function() {

		}
	},

	/**
	 * Alle methoden/functies om interactie Graphical User Interface uit te voeren
	 */
	Gui: {
		_getElement: function() {
			//...
		},

		_getProperty: function() {
			// ...
		},

		_getPropertyValue: function() {
			// ...
		},

		getUserInput: function() {
			var data = {
				"element": GbGenerator.Gui._getElement(),
				"property": GbGenerator.Gui._getProperty(),
				"value": GbGenerator.Gui._getPropertyValue()
			};

			//...
		}
	}
};