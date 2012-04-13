<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2012 Dominique Feyer (ttree) <dfeyer@ttree.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Abstract View Class
 *
 * @author		Dominique Feyer (ttree) <dfeyer@ttree.ch>
 * @package		TYPO3
 * @subpackage	tx_expose
 *
 */
abstract class Tx_Expose_MVC_View_AbstractView implements Tx_Extbase_MVC_View_ViewInterface {

	/**
	 * @var mixed
	 */
	protected $variable = NULL;

	/**
	 * @var string
	 */
	protected $rootElementName = 'records';

	/**
	 * @var string
	 */
	protected $baseElementName = 'record';

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Dummy method to satisfy the ViewInterface
	 *
	 * @param Tx_Extbase_MVC_Controller_ControllerContext $controllerContext
	 * @return void
	 */
	public function setControllerContext(Tx_Extbase_MVC_Controller_ControllerContext $controllerContext) {
	}

	/**
	 * Dummy method to satisfy the ViewInterface
	 *
	 * @param string $elementName
	 * @param mixed $values
	 * @return Tx_Expose_MVC_View_AbstractView instance of $this to allow chaining
	 * @api
	 */
	public function assign($elementName, $values) {
		if ($elementName === 'settings') {
			// Store settings
			$this->settings = $values;
		} else {
			if ($this->variable == NULL) {
				$this->baseElementName = $elementName;
				$this->variable = $values;
			} else {
				throw new Tx_Expose_Exception_RuntimeException(
					'Your variables is always assigned, you can only assign one variable to respect REST philosophy, please use clearAssignment(), before setting a new variable',
					1334313500
				);
			}
		}

		return $this;
	}

	/**
	 * Clear variable assignment
	 * @return Tx_Expose_MVC_View_AbstractView instance of $this to allow chaining
	 * @api
	 */
	public function clearAssignment() {
		$this->variable = NULL;
		$this->baseElementName = 'record';
	}

	/**
	 * Dummy method to satisfy the ViewInterface
	 *
	 * @param array $values
	 * @return Tx_Expose_MVC_View_AbstractView instance of $this to allow chaining
	 * @api
	 */
	public function assignMultiple(array $values) {
		throw new Tx_Expose_Exception_RuntimeException(
			'You can not assign multiple variables, use assign() method',
			1334313429
		);
	}

	/**
	 * This view can be used in any case.
	 *
	 * @param Tx_Extbase_MVC_Controller_ControllerContext $controllerContext
	 * @return boolean TRUE
	 * @api
	 */
	public function canRender(Tx_Extbase_MVC_Controller_ControllerContext $controllerContext) {
		return TRUE;
	}

	/**
	 * Renders the empty view
	 *
	 * @return string An empty string
	 */
	public function render() {
		return 'Please implement render() method';
	}

	/**
	 * A magic call method.
	 *
	 * Because this empty view is used as a Special Case in situations when no matching
	 * view is available, it must be able to handle method calls which originally were
	 * directed to another type of view. This magic method should prevent PHP from issuing
	 * a fatal error.
	 *
	 * @return void
	 */
	public function __call($methodName, array $arguments) {
	}

	/**
	 * Initializes this view.
	 *
	 * Override this method for initializing your concrete view implementation.
	 *
	 * @return void
	 * @api
	 */
	public function initializeView() {
	}

	/**
	 * Get the value for a give element
	 *
	 * @param object|array $record
	 * @param string $propertyPath
	 * @param array $configuration
	 * @param bool $htmlentities
	 * @return bool|mixed|string
	 */
	protected function getElementValue($record, $propertyPath, array $configuration, $htmlentities = TRUE) {
		$elementValue = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($record, $propertyPath);

		$elementValue = str_replace('’', '\'', $elementValue);

		// Encode entities
		if ($htmlentities) {
			$elementValue = htmlentities($elementValue);
		} else {
			$elementValue = htmlspecialchars($elementValue);
		}

		// Apply defined user function
		if (isset($configuration['userFunc'])) {
			$userObject = t3lib_div::getUserObj($configuration['userFunc']['class']);
			if ($userObject !== FALSE) {
				$methodName = $configuration['userFunc']['method'];
				$parameters = isset($configuration['userFunc']['params']) ? $configuration['userFunc']['params'] : array();
				$elementValue = $userObject->$methodName($elementValue, $parameters);
			}
		}

		if (trim($elementValue) === '') {
			$elementValue = FALSE;
		}

		return $elementValue;
	}

	/**
	 * Returns the settings at path $path, which is separated by ".",
	 * e.g. "pages.uid".
	 * "pages.uid" would return $this->settings['pages']['uid'].
	 *
	 * If the path is invalid or no entry is found, false is returned.
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function getSettingByPath($path) {
		return Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($this->settings, $path);
	}

	/**
	 * Set Root Element name
	 *
	 * @param string $rootElementName
	 */
	public function setRootElementName($rootElementName) {
		$this->rootElementName = $rootElementName;
	}

}

?>