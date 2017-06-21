var Game = {
	gameRunTime: null,
	isRunning: false,
	gameSpeed: 125,					// Game speed in MilliSeconds
	points: 0,							// Total game points
	apples: 0,
	mouses: 0,

	/**
	 * Initialises the game
	 */
	init: function(gridID, settings) {
		let check = Game.GameGui.init(gridID);

		this._parseSettings(settings);

		// First make sure we have the container div before continuing
		if (check == true) {
			Game.Grid.init();
			Game.Snake.init();
			Game.Gui.init();
			Game.Edibles.init();
		}
	},

	_parseSettings: function(settings) {
		if (settings.hasOwnProperty('gridSize')) {
			Game.Grid.gridXSize = settings.gridSize;
			Game.Grid.gridYSize = settings.gridSize;
		}
	},

	GameGui: {
		gridDiv: null,

		init: function(gridID) {
			// Set parent div to the property for later use
			this.gridDiv = document.getElementById(gridID);

			// Show error if not found
			if (this.gridDiv == undefined) {
				console.log('ERROR: Element with ID "' + gridID + '" is not found!');
				return false;
			}

			let gameDiv = this._createDiv('grid')
				,	childGridDiv = this._createDiv('grid_table')
				, scoreBoardDiv = this._createDiv('scoreboard')
				, actionButtonsDiv = this._createDiv('game-actions');

			// Append second div to test blabla
			gameDiv.appendChild(childGridDiv);

			// Create scoreboard
			scoreBoardDiv = this._createScoreBoard(scoreBoardDiv);

			// Add scoreboard
			gameDiv.appendChild(scoreBoardDiv);

			let clearfix_div  = document.createElement('div');
			clearfix_div.classList.add('clear');

			// Add clearfix
			gameDiv.appendChild(clearfix_div);

			this.gridDiv.appendChild(gameDiv);

			actionButtonsDiv = this._createActionButtons(actionButtonsDiv);

			this.gridDiv.append(actionButtonsDiv);

			return true;
		},

		/**
		 * Creates a div with the given class name
		 * @param className
		 * @returns {Element}
		 * @private
		 */
		_createDiv: function(className) {
			let div = document.createElement('div');
			div.classList.add(className);

			return div;
		},

		/**
		 * Creates the HTML for the scoreboard
		 * @param parent
		 * @returns {*}
		 * @private
		 */
		_createScoreBoard: function(parent) {
			// Make title (H3)
			let h3 = document.createElement('h3')
				,	h3_2 = document.createElement('h3');

			h3.innerText = 'Legende';
			parent.appendChild(h3);

			let spans = [
				document.createElement('span'),
				document.createElement('span'),
				document.createElement('span')
			];

			// Add texts
			spans[0].innerHTML = 'dd';
			spans[1].innerHTML = 'dd';
			spans[2].innerHTML = 'dd';

			// Add classes
			spans[0].classList.add('l_snake');
			spans[1].classList.add('l_apple');
			spans[2].classList.add('l_mouse');

			// Add breaks
			//spans[0].appendChild(document.createElement('br'));
			//spans[1].appendChild(document.createElement('br'));
			//spans[2].appendChild(document.createElement('br')).appendChild(document.createElement('br'));

			// Add to parent
			parent.appendChild(spans[0]);
			parent.appendChild(document.createTextNode(' = Snake lichaamsdeel'));
			parent.appendChild(document.createElement('br'));

			parent.appendChild(spans[1]);
			parent.appendChild(document.createTextNode(' = Appel (10 punten)'));
			parent.appendChild(document.createElement('br'));

			parent.appendChild(spans[2]);
			parent.appendChild(document.createTextNode(' = Muis (50 punten)'));
			parent.appendChild(document.createElement('br'));
			parent.appendChild(document.createElement('br'));

			h3_2.innerText = 'Scoreboard';
			parent.appendChild(h3_2);

			let span_points = document.createElement('span');
			span_points.classList.add('points');
			span_points.innerHTML = '<span id="total_points">0</span> punten';

			parent.appendChild(span_points);
			parent.appendChild(document.createElement('hr'));

			let span_apples = document.createElement('span');
			span_apples.setAttribute('id', 'apples');
			span_apples.innerText = '0 appels opgegeten';

			parent.appendChild(span_apples);
			parent.appendChild(document.createElement('br'));

			let span_mouses = document.createElement('span');
			span_mouses.setAttribute('id', 'mouses');
			span_mouses.innerText = '0 muizen verslonden';

			parent.appendChild(span_mouses);

			return parent;
		},

		_createActionButtons: function(parent) {
			let btn = document.createElement('button')
				,	btn2 = document.createElement('button');

			btn.setAttribute('type', 'button');
			btn.setAttribute('id', 'start');
			btn.innerHTML = 'Start game';

			btn2.setAttribute('type', 'button');
			btn2.setAttribute('id', 'stop');
			btn2.innerHTML = 'Stop game';

			// Add buttons to parent div
			parent.appendChild(btn);
			parent.appendChild(btn2);

			let radios = [
				document.createElement('input'),
				document.createElement('input'),
				document.createElement('input'),
				document.createElement('input')
			];

			radios[0].setAttribute('type', 'radio');
			radios[0].setAttribute('name', 'difficulty');
			radios[0].setAttribute('value', 'veryhard');

			radios[1].setAttribute('type', 'radio');
			radios[1].setAttribute('name', 'difficulty');
			radios[1].setAttribute('value', 'hard');
			radios[1].setAttribute('checked', 'checked');

			radios[2].setAttribute('type', 'radio');
			radios[2].setAttribute('name', 'difficulty');
			radios[2].setAttribute('value', 'medium');

			radios[3].setAttribute('type', 'radio');
			radios[3].setAttribute('name', 'difficulty');
			radios[3].setAttribute('value', 'easy');

			parent.appendChild(radios[0]);
			parent.appendChild(document.createTextNode('Zeer moeilijk'));

			parent.appendChild(radios[1]);
			parent.appendChild(document.createTextNode('Moeilijk'));

			parent.appendChild(radios[2]);
			parent.appendChild(document.createTextNode('Gemiddeld'));

			parent.appendChild(radios[3]);
			parent.appendChild(document.createTextNode('Gemakkelijk'));

			return parent;
		}
	},

	// Everything from drawing the grid to
	Grid: {
		gridXSize: 20,
		gridYSize: 20,

		init: function() {
			this._drawGrid();
		},

		// Draw's the grid for snake-game
		_drawGrid: function() {
			let grid = document.querySelectorAll('.grid_table')[0]
				,	table = document.createElement('table');

			// Let's draw the grid
			// Use Y-Size to add enough 'rows'
			for(let i=0; i < this.gridYSize; i++) {
				let row = document.createElement('tr');

				for (let j=0; j < this.gridXSize; j++) {
					let column = document.createElement('td');

					column.setAttribute('data-x', i+1);
					column.setAttribute('data-y', j+1);

					row.appendChild(column);
				}

				// Append row to table
				table.appendChild(row);
			}


			grid.appendChild(table);
		},

		fillGridCoordinate: function(x, y, type) {
			let columns = document.querySelectorAll('.grid table tr td');

			// Loop trough every column to find the right one
			for(let i in columns) {
				let column = columns[i];

				// Check if it's a cell
				if (column instanceof HTMLTableCellElement) {
					if (column.getAttribute('data-x') == x && column.getAttribute('data-y') == y) {
						column.classList.add(type);
					}
				}
			}

			//console.log('Grid: filled column at pos x: ' + x + ', y: ' + y);
		},

		addEdible: function(x, y, type) {
			let columns = document.querySelectorAll('.grid table tr td');

			// Loop trough every column to find the right one
			for(let i in columns) {
				let column = columns[i];

				// Check if it's a cell
				if (column instanceof HTMLTableCellElement) {
					if (column.getAttribute('data-x') == x && column.getAttribute('data-y') == y) {

						// Check if it's not a part of the snake or another mouse
						if (!column.classList.contains('snake') && !column.classList.contains('mouse')) {
							column.classList.add(type);
						}
						else {
							return false;
						}
					}
				}
			}
			//console.log('Grid: filled column at pos x: ' + x + ', y: ' + y);
		},

		findGridCell: function(x, y) {
			let columns = document.querySelectorAll('.grid table tr td');

			// Loop trough every column to find the right one
			for(let i in columns) {
				let column = columns[i];

				// Check if it's a cell
				if (column instanceof HTMLTableCellElement) {
					if (column.getAttribute('data-x') == x && column.getAttribute('data-y') == y)
						return column;
				}
			}
		},

		clearGrid: function() {
			let columns = document.querySelectorAll('.grid table tr td');

			// Loop trough every column to find the right one
			for(let i in columns) {
				let column = columns[i];

				// Check if it's a cell
				if (column instanceof HTMLTableCellElement) {
					column.classList.remove('snake');
				}
			}
		}
	},

	Snake: {
		arrSnake: [],
		currentDirection: 'left',

		init: function() {
			this.arrSnake[0] = this.getFixedStartPos();
			this.drawSnake();

		},

		// Get's the starting position for the snake which is random
		getStartPos: function() {
			let x = Math.floor((Math.random() * Game.Grid.gridYSize) + 1 );
			let y = Math.floor((Math.random() * Game.Grid.gridXSize) + 1 );

			return [x, y];
		},

		getFixedStartPos: function() {
			let x = Math.round(Game.Grid.gridYSize/2);
			let y = Math.round(Game.Grid.gridXSize);

			return [x, y];
		},

		// Draw's the full snake
		drawSnake: function() {
			for(var i in this.arrSnake) {
				Game.Grid.fillGridCoordinate(this.arrSnake[i][0], this.arrSnake[i][1], 'snake');
			}
		},

		addSnakePart: function() {
			// Get last column
			let last_column = this.arrSnake[this.arrSnake.length-1];

			// Add new part
			switch(this.currentDirection) {
				case 'left':
					this.arrSnake.push([last_column[0], last_column[1] + 1]);
					break;

				case 'right':
					this.arrSnake.push([last_column[0], last_column[1] - 1]);
					break;

				case 'up':
					this.arrSnake.push([last_column[0] + 1, last_column[1]]);
					break;

				case 'down':
					this.arrSnake.push([last_column[0] - 1, last_column[1]]);
					break;
			}

			//console.table(this.arrSnake);
		},

		reset: function() {
			this.arrSnake = [this.getFixedStartPos()];
			this.drawSnake();
		},

		getSnakeHeadCell: function() {
			return Game.Grid.findGridCell(this.arrSnake[0][0], this.arrSnake[0][1]);
		},

		move: function() {
			let directory = this.currentDirection;

			Game.Grid.clearGrid();

			switch (directory) {
				case 'left':
					this._moveLeft();
					break;
				case 'right':
					this._moveRight();
					break;
				case 'up':
					this._moveUp();
					break;
				case 'down':
					this._moveDown();
					break;
			}

			// Check for GAME-OVER
			if (this.isOutOfBounds() || this.isInSelf()) {
				Game.stop();
				alert('GAME OVER!!!');

				Game.reset();
				return;
			}

			// Check if an edible has been eaten
			Game.Edibles.checkIfEdibleEaten();

			// Update score-board
			Game.ScoreBoard.update();
		},

		_moveLeft: function() {
			this._moveBody('left');
			this.drawSnake();
		},
		_moveRight: function() {
			this._moveBody('right');
			this.drawSnake();
		},
		_moveUp: function() {
			this._moveBody('up');
			this.drawSnake();
		},
		_moveDown: function() {
			this._moveBody('down');
			this.drawSnake();
		},

		_moveBody: function(direction) {
			let first = false;
			let lastPosts = [];

			//lastPos = lastPos.split(',');

			for (let index in this.arrSnake) {
				lastPosts.push(this.arrSnake[index].toString());
			}

			for (let index in lastPosts) {
				lastPosts[index] = lastPosts[index].split(',');
				//console.log(lastPosts[index]);
			}

			// Move first tile
			switch(direction) {
				case 'left': this.arrSnake[0][1] -= 1; break;
				case 'right': this.arrSnake[0][1] += 1; break;
				case 'up': this.arrSnake[0][0] -= 1; break;
				case 'down': this.arrSnake[0][0] += 1; break;
			}

			for (let index in this.arrSnake) {
				if  (first != false) {

				}
					lastPos = lastPosts[index-1];

				if (index > 0) {
					this.arrSnake[index] = lastPos;
				}

				first = true;
			}
		},

		isOutOfBounds: function() {
			let snakeHead = this.getSnakeHeadCell();

			return (snakeHead == undefined);
		},

		isInSelf: function() {
			let snakeHead = this.getSnakeHeadCell();
			let pos = [snakeHead.getAttribute('data-x'), snakeHead.getAttribute('data-y')];

			// Loop throuh snake array to check if one is within himself
			for(let i in this.arrSnake) {
				// Skip the head
				if (i == 0)
					continue;

				let snakePart = this.arrSnake[i];

				if (snakePart[0] == pos[0] && snakePart[1] == pos[1])
					return true;
			}
		},

		isOppositeDirection: function(direction) {
			if (this.currentDirection == 'left' && direction == 'right')
				return true;
			else if (this.currentDirection == 'right' && direction == 'left')
				return true;
			else if (this.currentDirection == 'up' && direction == 'down')
				return true;
			else if (this.currentDirection == 'down' && direction == 'up')
				return true;
		}
	},

	Edibles: {
		currentApple: null,
		currentMouse: null,
		ediblesRuntime: null,

		init: function() {
			this.addApple();
			this.addMouse();
		},

		addApple: function() {
			let check = false;

			// Making sure an apple spawns
			while(check == false) {
				pos = Game.Snake.getStartPos();
				check = Game.Grid.addEdible(pos[0], pos[1], 'apple');
			}

			// Set current apple as apple
			this.currentApple = Game.Grid.findGridCell(pos[0], pos[1]);

		},

		addMouse: function() {
			let time = this._getTimeForNewMouseSpawn();

			this.ediblesRuntime = setInterval(function() {
				let check = false;

				// Making sure an apple spawns
				while(check == false) {
					pos = Game.Snake.getStartPos();
					check = Game.Grid.addEdible(pos[0], pos[1], 'mouse');
				}

				// Set current mouse as mouse
				Game.Edibles.currentMouse = Game.Grid.findGridCell(pos[0], pos[1]);

				clearInterval(Game.Edibles.ediblesRuntime);
			}, time);
		},

		checkIfEdibleEaten: function() {
			let snakeHead = Game.Snake.getSnakeHeadCell();

			// Apple eaten?
			if (snakeHead.classList.contains('apple')) {
				// Add 10 points
				Game.points += 10;

				// Remove and spawn new apple
				Game.Edibles.currentApple.classList.remove('apple');
				Game.Edibles.addApple();

				// Add one body part to snake
				Game.Snake.addSnakePart();

				// Amount of apples eaten for score
				Game.apples += 1;
			}

			// Mouse eaten?
			if (snakeHead.classList.contains('mouse')) {
				// Add 50 points
				Game.points += 50;

				// Remove and spawn new mouse
				Game.Edibles.currentMouse.classList.remove('mouse');
				Game.Edibles.addMouse();

				// Amount of mouses eaten for score
				Game.mouses += 1;
			}
		},

		_setRuneTime: {},

		_getTimeForNewMouseSpawn: function() {
			return time = Math.round((Math.random() * 30000) + 1);
		}
	},

	start: function() {
		if (this.isRunning == false) {
			this.gameRunTime = setInterval(function() {
				Game.Snake.move();
			}, this.gameSpeed);
		}

		this.isRunning = true;
	},

	stop: function() {
		clearInterval(this.gameRunTime);

		this.isRunning = false;
	},

	reset: function() {
		Game.Grid.clearGrid();
		Game.Snake.reset();
		Game.Snake.currentDirection = 'left';

		Game.points = 0;
		Game.mouses = 0;
		Game.apples = 0;
		Game.ScoreBoard.update();
	},

	Gui: {
		init: function() {
			this._setStartStopHandlers();
			this._setArrowKeys();
		},

		_setStartStopHandlers: function() {
			let btn = document.getElementById('start')
				,	btn2 = document.getElementById('stop');

			btn.addEventListener('click', function() {
				Game.Gui.EventHandlers.start();
			});

			btn2.addEventListener('click', function() {
				Game.Gui.EventHandlers.stop();
			});
		},

		_setArrowKeys: function() {
			document.addEventListener('keydown', function(e) {
				if (e.keyCode >= 37 && e.keyCode <= 40)
					e.preventDefault();

				switch(e.keyCode) {
					case 37: Game.Gui.EventHandlers.pushLeft(); break;
					case 39: Game.Gui.EventHandlers.pushRight(); break;
					case 38: Game.Gui.EventHandlers.pushUp(); break;
					case 40: Game.Gui.EventHandlers.pushDown(); break;
				}
			})
		},

		getDifficultyLevel: function() {
			// First get settings
			let radios = document.querySelectorAll('input[type=radio]');
			let difficulty = null;

			for(let i in radios) {
				if (radios[i].checked == true) {
					difficulty = radios[i].value;
					break;
				}
			}

			switch(difficulty) {
				case 'veryhard': return 50; break;
				case 'hard': return 125; break;
				case 'medium': return 300; break;
				case 'easy': return 500; break;
			}
		},

		EventHandlers : {
			start: function() {
				Game.gameSpeed = Game.Gui.getDifficultyLevel();
				Game.start();
			},
			stop: function() {
				Game.stop();
			},

			pushLeft: function() {
				if (!Game.Snake.isOppositeDirection('left'))
					Game.Snake.currentDirection = 'left';
			},
			pushRight: function() {
				if (!Game.Snake.isOppositeDirection('right'))
					Game.Snake.currentDirection = 'right';
			},
			pushUp: function() {
				if (!Game.Snake.isOppositeDirection('up'))
					Game.Snake.currentDirection = 'up';
			},
			pushDown: function() {
				if (!Game.Snake.isOppositeDirection('down'))
					Game.Snake.currentDirection = 'down';
			}
		}
	},

	ScoreBoard: {
		update: function() {
			let score = document.getElementById('total_points');
			let apples = document.getElementById('apples')
				,	mouses = document.getElementById('mouses');

			score.innerHTML = Game.points;

			apples.innerHTML = '<strong>' +  Game.apples + '</strong> appels opgegeten';
			mouses.innerHTML = '<strong>' +  Game.mouses + '</strong> muizen verslonden';
		}
	}
};
