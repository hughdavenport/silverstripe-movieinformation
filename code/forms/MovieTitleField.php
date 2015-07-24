<?php

/**
 * FormField to select movie titles
 * Composite field consisting of a TextField and a DrodownField that allow a user to enter in
 * a movie title in the text field and the drop down automatically shows a fuzzy loaded listing
 * that matches the text entered so far. Final result is selected from the drop down Composite
 * field consisting of a TextField and a DrodownField that allow a user to enter in a movie title
 * in the text field and the drop down automatically shows a fuzzy loaded listing that matches the
 * text entered so far. Final result is selected from the drop down.
 *
 * Depends on a javascript library that tracks the user change, and also on a controller method
 * to get the listings. Currently this controller uses OMDb API, but could be extended for more.
 *
 * Example model defintion:
 * <code>
 * class MyObject extends DataObject {
 *   static $db = array(
 *     'MovieTitle' => 'Text',
 *   );
 * }
 * </code>
 *
 * Example instantiation:
 * <code>
 * $titleField = MovieTitleField::create('MovieTitle', 'Movie Title', $this->MovieTitle);
 * </code>
 *
 * @see MovieInformation_Controller->getmovies for the controller action to retrieve movies from search
 *
 * @package movieinformation
 */
class MovieTitleField extends FormField
{

	/**
	 * @var $composite_fields Array to store the TextField and DropdownField
	 */
	protected $composite_fields = array();

	/**
	 * Creates a new field.
	 *
	 * @param string $name The internal field name, passed to forms.
	 * @param string $title The human-readable field label.
	 * @param mixed $value The value of the field.
	 */
	public function __construct($name, $title = null, $value = null, $form = null) {
		$field = TextField::create("{$name}[Text]", '');
		$field->setDescription(
			_t('MovieTitleField.TEXT_DESCRIPTION', 'Type in this textbox to update the dropdown below.')
		);
		$this->composite_fields['Text'] = $field;
		$field = DropdownField::create("{$name}[Select]", '', array($value));
		$this->composite_fields['Select'] = $field;
		parent::__construct($name, $title, $value, $form);
	}

	/**
	 * Set the container form.
	 *
	 * @param object $form The form.
	 */
	public function setForm($form) {
		foreach ($this->composite_fields as $type => $field) {
			$field->setForm($form);
		}
		parent::setForm($form);
	}

	/**
	 * Get HTML for the field to display
	 *
	 * @param array $properties Properties array
	 * @return string
	 */
	public function Field($properties = array())
	{
		$module_dir = basename(dirname(dirname(__DIR__)));
		Requirements::javascript($module_dir . '/javascript/MovieTitleField.js');
		Requirements::add_i18n_javascript($module_dir . '/javascript/lang');
		$list = new ArrayList($this->composite_fields);
		return $list->renderWith('MovieTitleField');
	}

	/**
	 * Set the value for this field.
	 * Set the value using the dropdown value, or just a string value when initialized.
	 *
	 * @param mixed $value The value to set
	 */
	public function setValue($value) {
		if(is_array($value) && isset($value['Select'])) {
			$val = $value['Select'];
		} elseif(is_string($value)) {
			$val = $value;
		} else {
			return;
		}
		if (empty($val)) {
			return;
		}
		foreach ($this->composite_fields as $type => $field) {
			$field->setValue($val);
		}
		parent::setValue($val);
	}

}
