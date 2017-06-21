<?php

// Error Reporting
error_reporting(E_ALL);


class Config {

	/*****************************************
	 * PROJECT
	 *****************************************/

	// De template die moet worden gebruikt, standaard 'Default'
	const TEMPLATE = 'Default';

	// Is het project in ontwikkeling ?
	const DEVELOPMENT = true;

	// De basis url v/h project
	const SITE_PATH = 'http://www.';

	// Which of version of the project is this ?
	const PROJECT_VERSION = '1.0.0';

	// Tiny MVC version
	const FRAMEWORK_VERSION = '0.7.2';

	/*****************************************
	 * DATABASE
	 *****************************************/

	// The database's host
	const DATABASE_HOST = '';

	// The database user's username
	const DATABASE_USERNAME = '';

	// The database user's password
	const DATABASE_PASSWORD = '';

	// The database's name
	const DATABASE_NAME = '';

	// Salt key for hashing
	const SALT = '~=<T~I~N~Y~=~M~V~C~=~F~R~A~M~E~W~O~R~K>=~R0bb3~-~1nG3LbR3C4hTs><[]$';

	/*****************************************
	 * Caching
	 *****************************************/
	// Path to Cache folder starting with the app dir
	const CACHE_PATH = 'Cache';

	// Standard interval between caching if none is given
	const CACHE_ST_INTERVAL = 1800;

	/*****************************************
	 * Session
	 *****************************************/

	// Session prefix, doesn't matter for use of sessions as long as the SessionComponent and helper are used for handling session stuff
	const SESSION_PREFIX = 'tmvc_';

	// Default amount to show a flash if no optional amount is given
	const FLASH_DEFAULT_SHOWTIMES = 1;

	// Default class to use for parent div of a flash message
	const FLASH_DEFAULT_CLASS = 'alert';

	/*****************************************
	 * Pagination
	 *****************************************/

	// Default results per page if none are given
	const PAGINATION_DEFAULT_RESULTS_PER_PAGE = 10;

	// Default tag to use for the page-buttons
	const PAGINATION_DEFAULT_TAG = 'a';

	// Default settings to use first/last-buttons if none are given
	const PAGINATION_USE_FIRSTLAST = true;

	// Default settings to use next/prev-buttons if none are given
	const PAGINATION_USE_NEXTPREV = true;

	// Default next/previous text to use for buttons
	const PAGINATION_DEFAULT_NEXT = '>';
	const PAGINATION_DEFAULT_PREV = '<';

	// Default first/last text to use for buttons
	const PAGINATION_DEFAULT_FIRST = '<<';
	const PAGINATION_DEFAULT_LAST = '>>';

	// Range of page numbers with respect to the current page
	const PAGINATION_DEFAULT_RANGE = 3;
}