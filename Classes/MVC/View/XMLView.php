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
 * This view export the current data as XML
 *
 * @author		Dominique Feyer (ttree) <dfeyer@ttree.ch>
 * @package		TYPO3
 * @subpackage	tx_expose
 *
 */
final class Tx_Expose_MVC_View_XMLView implements Tx_Extbase_MVC_View_ViewInterface {

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
	 * @var string
	 */
	protected $version = '1.0';

	/**
	 * @var string
	 */
	protected $encoding = 'UTF-8';

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @var DOMDocument
	 */
	protected $document;

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
	 * @return Tx_Expose_MVC_View_XMLView instance of $this to allow chaining
	 * @api
	 */
	public function assign($elementName, $values) {
		if ($elementName === 'settings') {
			$this->settings = $values;
		} else {
			$this->baseElementName = $elementName;
			$this->variable = $values;
		}

		return $this;
	}

	/**
	 * Dummy method to satisfy the ViewInterface
	 *
	 * @param array $values
	 * @return Tx_Expose_MVC_View_XMLView instance of $this to allow chaining
	 * @api
	 */
	public function assignMultiple(array $values) {

		return $this;
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
		$this->document = new DOMDocument($this->version, $this->encoding);
		$this->document->formatOutput = TRUE;

		$rootElement = $this->document->createElement($this->rootElementName);
		$this->document->appendChild($rootElement);

		$this->renderVariable($rootElement);

		return $this->document->saveXML();
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
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return string
	 * @throws Tx_Fluid_Core_ViewHelper_Exception
	 */
	protected function renderVariable(DOMElement $parentElement) {
		$configurationPath = $this->getSettingByPath('api.conf.' . $this->rootElementName . '.path');
		$configuration = $this->getSettingByPath($configurationPath);

		if (!is_array($configuration) || count($configuration) === 0) {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Invalid webservice configuration',
				1334309979
			);
		}

		foreach ($this->variable as $baseNodeRecord) {
			$rootElement = $this->document->createElement($this->baseElementName);

			if (TRUE == $comment = $this->getSettingByPath('api.conf.' . $this->rootElementName . '.modelComment')) {
				$rootElement->appendChild($this->document->createComment($comment));
			}

			$this->processDomainModel($baseNodeRecord, $configuration, $rootElement);

			$parentElement->appendChild($rootElement);
		}

		return $parentElement;
	}

	/**
	 * Process each domain model
	 *
	 * @param $record
	 * @param array $configuration
	 * @param DOMElement $rootElement
	 * @throws InvalidArgumentException
	 */
	protected function processDomainModel($record, array $configuration, DOMElement $rootElement) {
		foreach ($configuration as $key => $elementConfiguration) {
			$propertyPath = $elementConfiguration['path'];
			$elementName = $elementConfiguration['element'];

			// Create element
			if (trim($elementName) === '') {
				throw new Tx_Expose_Exception_InvalidConfigurationException(
					'Element name can not be empty',
					1334310000
				);
			}
			$element = $this->document->createElement($elementName);

			if (empty($elementConfiguration['type']) || !isset($elementConfiguration['type'])) {
				$elementConfiguration['type'] = 'text';
			}

			switch ($elementConfiguration['type']) {
				case 'text':
					$this->appendTextChild($element, $record, $propertyPath, $elementConfiguration);
					break;
				case 'cdata':
					$this->appendCDATATextChild($element, $record, $propertyPath, $elementConfiguration);

					break;
				case 'relations':
					$this->appendMultipleChildrenNodes($element, $record, $propertyPath, $elementConfiguration);
					break;
				default:
					throw new Tx_Expose_Exception_InvalidConfigurationException(
						sprintf('Invalid element type (%s) configuration', $elementConfiguration['type']),
						1334310013
					);
			}

			// Append element to document
			$rootElement->appendChild($element);
		}
	}

	/**
	 * Append XML text node
	 *
	 * @param DOMElement $element
	 * @param object|array $record
	 * @param string $propertyPath
	 * @param array $elementConfiguration
	 */
	protected function appendTextChild(DOMElement $element, $record, $propertyPath, array $elementConfiguration) {
		if ($elementValue = $this->getElementValue($record, $propertyPath, $elementConfiguration, FALSE)) {
			$element->appendChild($this->document->createTextNode($elementValue));
		}
	}

	/**
	 * Append XML CDATA Text node
	 *
	 * @param DOMElement $element
	 * @param object|array $record
	 * @param string $propertyPath
	 * @param array $elementConfiguration
	 */
	protected function appendCDATATextChild(DOMElement $element, $record, $propertyPath, array $elementConfiguration) {
		if ($elementValue = $this->getElementValue($record, $propertyPath, $elementConfiguration, FALSE)) {
			$element->appendChild($this->document->createCDATASection($elementValue));
		}
	}

	/**
	 * Process a multiple relations
	 *
	 * @param DOMElement $parentElement
	 * @param object|array $record
	 * @param string $propertyPath
	 * @param array $elementConfiguration
	 * @throws Tx_Expose_Exception_InvalidConfigurationException
	 */
	protected function appendMultipleChildrenNodes(DOMElement $parentElement, $record, $propertyPath, array $elementConfiguration) {
		$relations = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($record, $propertyPath);
		if (trim($elementConfiguration['conf']) === '') {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Unable to process relations without configuration',
				1334310033
			);
		}
		$relationConfiguration = $this->getSettingByPath($elementConfiguration['conf']);

		if (!is_array($relationConfiguration) || trim($elementConfiguration['children']) === '') {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Invalid configuration',
				1334310035
			);
		}

		foreach ($relations as $record) {
			$relationRootElement = $this->document->createElement($elementConfiguration['children']);
			$this->processDomainModel($record, $relationConfiguration, $relationRootElement);
			$parentElement->appendChild($relationRootElement);
		}
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
	 * Set XML Root Element name
	 *
	 * @param string $rootElementName
	 */
	public function setRootElementName($rootElementName) {
		$this->rootElementName = $rootElementName;
	}

	/**
	 * Set XML Encoding
	 *
	 * @param string $encoding
	 */
	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}

	/**
	 * Set XML Version
	 *
	 * @param string $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

}

?>