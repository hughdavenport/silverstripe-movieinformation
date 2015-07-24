<?php

/**
 * Controller for the movie information page.
 * Uses the OMDb API to search for movies, and to get information about a saved page.
 *
 * @package silverstripe
 * @subpackage movieinformation
 */
class MovieInformation_Controller extends Page_Controller {

	private static $allowed_actions = array (
		"getmovies",
	);

	/**
	 * Search for a movie
	 * Uses OMDb API to search for a movie, and returns a JSON response with a selection of
	 * titles that match the search term.
	 *
	 * @param SS_HTTPRequest $request The request object
	 * @return SS_HTTPResponse A json object containing search results
	 */
	public function getmovies(SS_HTTPRequest $request) {
		$search = $request->allParams()['ID'];
		$api = new RestfulService(
			'http://www.omdbapi.com/'
		);

		$api->setQueryString(array(
			'r'    => 'xml',   // RestfulService only supports XML
			'type' => 'movie', // Only grab movies
			's'    => $search, // User supplied search
			'v'    => 1,       // Should put in for futureproof
		));

		$results = $api->request();
		$results = $api->getAttributes($results->getBody(), 'Movie');
		$titles = array();
		if($results) {
			foreach($results as $result) {
				$titles[] = $result->getField('Title');
			}
		}
		$response = array(
			'search'  => $search,
			'results' => $titles,
		);
		$this->response->setBody(json_encode($response));
		$this->response->addHeader('Content-type', 'application/json');
		return $this->response;
	}

}
