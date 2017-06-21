<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Robbe
 * Date: 15/06/13
 * Time: 18:36
 * To change this template use File | Settings | File Templates.
 */

class PaginationHelper extends Helper {

	/**
	 * Formats the pagination-div with the given options
	 * Possible options:
	 * 	- url			| url to use (in case of a <a>-tag)
	 * 	- tag						| tag to use (ex. "ul")
	 *	- onclick				| js to execute in case of onclick
	 * 	- title					| Title-attribute
	 *  - prev					| String to use for previous page
	 * 	- next					| String to use for next page
	 *  - first					| String to use for first page
	 *  - last					| String to use for last page
	 *
	 * 	- lastfirst			| bool, use of last/first-buttons
	 * 	- nextprev			|	bool, use of next/prev-buttons
	 * @param $options
	 * @param $pages
	 * @return string
	 */
	public function formatAsHtml($options, $pages)  {
		$html = '';
		$lastfirst = (isset($options['lastfirst']) ? $options['lastfirst'] : Config::PAGINATION_USE_FIRSTLAST);
		$nextprev = (isset($options['nextprev']) ? $options['nextprev'] : Config::PAGINATION_USE_NEXTPREV);
		$current = $this->_getCurrentPage($pages);
		$t_pages = count($pages);

		// If a first-page button should be used, then append it to the html-var
		if ($lastfirst) {
			$text = (isset($options['first']) ? $options['first'] : Config::PAGINATION_DEFAULT_FIRST);
			$html .= $this->_formatTag($options, array('current' => false, 'num' => 1), $text, 'Eerste pagina');
		}

		// If a prev-page button should be used, then append it to the html-var
		if ($nextprev) {
			$text = (isset($options['prev']) ? $options['first'] : Config::PAGINATION_DEFAULT_PREV);
			$num = (($current-1) > 0) ? ($current-1) : 1;
			$html .= $this->_formatTag($options, array('current' => false, 'num' => $num), $text, 'Vorige pagina');
		}

		// If it's the first page
		if ($t_pages == 0) {
			$html .= $this->_formatTag($options, '1');
		}

		for($i=0; $i < $t_pages; $i++) {
			$page = $pages[$i];

			// If the current page is 6 or less
			if ($current <= 6) {
				// Then show the 2 pages before and after the current one
				if ($i >= $current-3 && $i < $current + 2) {
					$html .= $this->_formatTag($options, $page);

					// Add some dots to seperatoe
					if ($i == $current+1) {
						$html .= ' ... ';
					}
				}
			}
			// Otherwise if the counter is smaller then the total pages-3
			elseif ($i < $t_pages-3) {
				// Show the first 3 pages
				if ($i >= 0 && $i < 3) {
					$html .= $this->_formatTag($options, $page);

					// Add dots to seperate
					if ($i == 2) {
						$html .= ' ... ';
					}
				}

				// Show 2 pages before and after the current page
				if ($i >= $current-3 && $i < $current + 2) {
					$html .= $this->_formatTag($options, $page);

					if ($i == ($current+1)) {
						$html .= ' ... ';
					}
				}
			}

			// If there are more than 6 pages, show the 3 last pages
			if ($t_pages > 5) {
				// If the counter is between the 3 last pages and last page
				if ($i >= $t_pages-3 && $i < $t_pages)
					$html .= $this->_formatTag($options, $page);
			}
		}

		// If a next-page button should be used, then append it to the html-var
		if ($nextprev) {
			$text = (isset($options['prev']) ? $options['first'] : Config::PAGINATION_DEFAULT_NEXT);
			$num = (($current+1) < $t_pages) ? ($current+1) : $t_pages;
			$html .= $this->_formatTag($options, array('current' => false, 'num' => $num), $text, 'Volgende pagina');
		}

		// If a last-page button should be used, then append it to the html-var
		if ($lastfirst) {
			$text = (isset($options['first']) ? $options['first'] : Config::PAGINATION_DEFAULT_LAST);
			$html .= $this->_formatTag($options, array('current' => false, 'num' => $t_pages), $text, 'Laatste pagina');
		}

		return $html;
	}

	/**
	 * Formats a tag and returns it
	 *
	 * Extra options:
	 *  -  text          | Text to use instead of a page number
	 * @param $options
	 * @param $page
	 * @param null $text
	 * @param null $title
	 * @return string
	 */
	private function _formatTag($options, $page, $text = null, $title = null) {
		$tag = (isset($options['tag'])) ? $options['tag'] : Config::PAGINATION_DEFAULT_TAG;

		// Opening tag
		$html = "<$tag ";
		// If the tag is an <a>-tag, then we'll add a href-attribute
		if ($tag == 'a') {
			$url = str_replace('_PAGE_', $page['num'], $options['url']);
			$html .= 'href="'.$url.'" ';
			unset($url);
		}

		// If an onclick-string was given, then append it to the tag as well
		if (isset($options['onclick'])) {
			$onclick = str_replace('_PAGE_', $page['num'], $options['onclick']);
			$html .= 'onclick="'.$onclick.'" ';
			unset($onclick);
		}

		// If a title-string was given, then append a title att as well
		if (isset($options['title']) || $title !== null) {
			$title = ($title === null) ? $options['title'] : $title;
			$title = str_replace('_PAGE_', $page['num'], $title);

			$html .= 'title="'.$title.'"';
			unset($title);
		}

		$html.= '>';
		$text = ($text === null) ? $page['num'] : $text ;

		if ($page['current'] === true) {
			$html .= '<strong>'.$text.'</strong>';
		}
		else {
			$html .= $text;
		}

		$html .= "</$tag>&nbsp;";

		return $html;
	}

	/**
	 * Returns the current page
	 *
	 * @param $pages
	 * @return mixed
	 */
	private function _getCurrentPage($pages) {
		foreach($pages as $page) {
			if ($page['current'] === true) {
				return $page['num'];
			}
		}
	}
}