<?php

/**
 * Page to display movie information
 * Page that displays movie information retrieved from the OMDb API
 *
 * @package silverstripe
 * @subpackage movieinformation
 */
class MovieInformation extends Page {

	/**
	 * {@inheritdoc}
	 */
	public function getCMSFields() {
		$self =& $this;
		$this->beforeUpdateCMSFields(function ($fields) use ($self) {
			$titleField = MovieTitleField::create('Title',
			                                      _t('MovieInformation.Title'),
			                                      $this->Title);
			$fields->insertBefore($titleField, 'URLSegment');
			// Create read only URL segment. Needed for the javascript to call back to the controller
			$urlSegmentField = ReadonlyField::create('URLSegment_RO',
			                                         _t('SiteTree.URLSegment'),
			                                         $this->PreviewLink());
			$fields->insertAfter($urlSegmentField, 'Title');
			// Hide the fields we don't want
			$fields->push(HiddenField::create('URLSegment'));
			$fields->push(HiddenField::create('MenuTitle'));
			$fields->push(HiddenField::create('Content'));
		});
		$fields = parent::getCMSFields();
		return $fields;
	}

	protected function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->MenuTItle = $this->Title;
		$this->URLSegment = $this->generateURLSegment($this->Title);
		$count = 2;
		while(!$this->validURLSegment()) {
			$this->URLSegment = preg_replace('/-[0-9]+$/', null, $this->URLSegment) . '-' . $count;
			$count++;
		}
		$this->syncLinkTracking();
	}

	public function canPublish($member = null) {
		return $this->isValid() !== false;
	}

	/**
	 * Test whether this movie is valid.
	 * Connects to OMDb API to see whether a movie with a title the same as this page exists.
	 *
	 * @return boolean
	 */
	public function isValid() {
		$api = new RestfulService(
			'http://www.omdbapi.com/'
		);

		$api->setQueryString(array(
			'r'        => 'xml',        // RestfulService only supports XML
			'type'     => 'movie',      // Only grab movies
			't'        => $this->Title, // User supplied search
			'plot'     => 'full',       // Get short in second query
			'tomatoes' => 'true',       // Get rotten tomatoes ratings
			'v'        => 1,            // Should put in for futureproof
		));

		$results = $api->request();
		$body = $results->getBody();
		if(!strstr($body, "<")) {
			// An error with the xml
			return false;
		}
		if($api->getAttribute($body, '' , 'root', 'response') === 'False') {
			// server said no!
			return false;
		}
		if($api->getAttributes($body, 'movie')->count() == 0) {
			// server SHOULD have said no
			return false;
		}

		// We should be good, return the body to save a query ;)
		return $body;
	}
}

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
