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
 * Query Parser Result
 *
 * @package	tx_expose
 * @author	Dominique Feyer <dfeyer@ttree.ch>
 */
class Tx_Expose_Parser_QueryResultParser
	extends Tx_Expose_Parser_AbstractParser
	implements Tx_Expose_Parser_ParserInterface {

	/**
	 * @param string $name
	 * @param Tx_Extbase_Persistence_QueryResultInterface $records
	 * @param array $settings
	 *
	 * @return array
	 */
	public function parse($name, $records, array $settings) {

		// Validate method arguments
		if (!$records instanceof Tx_Extbase_Persistence_QueryResultInterface) {
			throw new Tx_Expose_Exception_ParserException(
				'Records must be an instance of Tx_Extbase_Persistence_QueryResultInterface',
				1334440287
			);
		}

		// Get configuration
		$this->initialize($name, $settings);

		// Initialize base element
		$this->data['_data'][$this->rootElementName] = array();

		// Internal data
		$this->data['_internal'][$this->rootElementName] = array();
		$this->data['_internal'][$this->rootElementName]['process_time'] = microtime(TRUE);

		// Process the records
		$this->data['_internal'][$this->rootElementName]['total'] = 0;
		foreach ($records as $record) {
			$this->data['_data'][$this->rootElementName][] = $this->processRecord($record, $this->configuration);
			$this->data['_internal'][$this->rootElementName]['total']++;
		}

		// Get full process time
		$this->data['_internal'][$this->rootElementName]['process_time'] = microtime(TRUE) - $this->data['_internal'][$this->rootElementName]['process_time'];

		t3lib_utility_Debug::debug($this->data);
		die();

		return $this->data;
	}

	/**
	 * @param object $record
	 * @param array $configuration
	 * @param null|array $parentRecord
	 * @return array
	 */
	protected function processRecord($record, $configuration, $parentRecord = NULL) {
		$processedRecord = array();

		foreach ($configuration as $_ => $fieldConfiguration) {
			$propertyPath = $fieldConfiguration['path'];
			$fieldName = $this->getFieldName($fieldConfiguration);

			// Create element
			if (trim($fieldName) === '') {
				throw new Tx_Expose_Exception_InvalidConfigurationException(
					'Element name can not be empty',
					1334310000
				);
			}

			// Set default field type
			if (empty($fieldConfiguration['type']) || !isset($fieldConfiguration['type'])) {
				$fieldConfiguration['type'] = 'text';
			}

			switch ($fieldConfiguration['type']) {
				case 'text':
					$value = $this->processField($record, $propertyPath, $fieldConfiguration);
					break;
				case 'relation':
					$value = $this->processSingleRelation($record, $propertyPath, $fieldConfiguration);
					break;
				case 'relations':
					$value = $this->processMultipleRelation($record, $propertyPath, $fieldConfiguration);
					break;
				default:
					throw new Tx_Expose_Exception_InvalidConfigurationException(
						sprintf('Invalid element type (%s) configuration', $fieldConfiguration['type']),
						1334310013
					);
			}

			$processedRecord[$fieldName] = $value;
		}

		return $processedRecord;
	}

	/**
	 * @param object $record
	 * @param string $propertyPath
	 * @param array $fieldConfiguration
	 * @return bool|string
	 */
	protected function processField($record, $propertyPath, array $fieldConfiguration) {
		return $this->getFieldValue($record, $propertyPath, $fieldConfiguration);
	}

	/**
	 * @param object $record
	 * @param string $propertyPath
	 * @param array $fieldConfiguration
	 * @throws Tx_Expose_Exception_InvalidConfigurationException
	 */
	protected function processSingleRelation($record, $propertyPath, array $fieldConfiguration) {
		if (trim($fieldConfiguration['conf']) === '') {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Unable to process relations without configuration',
				1334310033
			);
		}

		$fieldName = $this->getFieldName($fieldConfiguration);

		$relationRecord = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($record, $propertyPath);
		$relationConfiguration = $this->getSettingByPath($fieldConfiguration['conf']);

		if (!is_array($relationConfiguration)) {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Invalid configuration',
				1334310035
			);
		}

		$this->data['_data'][$this->rootElementName][$this->getCurrentRecordCounter()][$fieldName] = $this->processRecord($relationRecord, $relationConfiguration);
	}

	protected function processMultipleRelation($record, $propertyPath, array $fieldConfiguration) {
		$relations = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($record, $propertyPath);
		if (trim($fieldConfiguration['conf']) === '') {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Unable to process relations without configuration',
				1334310033
			);
		}
		$relationConfiguration = $this->getSettingByPath($fieldConfiguration['conf']);

		$fieldName = !empty($fieldConfiguration['element']) ? $fieldConfiguration['element'] : $this->getFieldName($fieldConfiguration);

		if (!is_array($relationConfiguration) || trim($fieldConfiguration['children']) === '') {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Invalid configuration',
				1334310035
			);
		}

		foreach ($relations as $record) {
			/// Todo fix for relation with depth >1
			$relationRecord = array();
			$this->processRecord($record, $relationConfiguration, $relationRecord);
			$this->data['_data'][$this->rootElementName][$this->getCurrentRecordCounter()][$fieldName][] = $relationRecord;
		}
	}

	/**
	 * Get the value for a give element
	 *
	 * @param object|array $record
	 * @param string $propertyPath
	 * @param array $configuration
	 * @param bool $htmlentities
	 * @return string|bool
	 */
	protected function getFieldValue($record, $propertyPath, array $fieldConfiguration) {
		if (!empty($configuration['_typoScriptNodeValue'])) {
			$elementValue = $this->getFieldContentObjectValue($record, $propertyPath, $fieldConfiguration);
		} else {
			$elementValue = $this->getElementRawValue($record, $propertyPath, $fieldConfiguration);
		}

		return $this->processUserFunc($elementValue, $fieldConfiguration);
	}

	/**
	 * Get the value for a give element, build by content object
	 *
	 * @param object|array $record
	 * @param string $propertyPath
	 * @param array $fieldConfiguration
	 * @return string|bool
	 */
	protected function getFieldContentObjectValue($record, $propertyPath, array $fieldConfiguration) {
		$data = Tx_Extbase_Reflection_ObjectAccess::getGettableProperties($record);
		$this->contentObject->start($data);

		// Set current value
		$elementValue = $this->getElementRawValue($record, $propertyPath, $fieldConfiguration);

		// Process user func
		$elementValue = $this->processUserFunc($elementValue, $fieldConfiguration);

		if (trim($elementValue) !== '') {
			$this->contentObject->setCurrentVal($elementValue);
		}

		return $this->contentObject->cObjGetSingle($fieldConfiguration['_typoScriptNodeValue'], $fieldConfiguration);
	}

	/**
	 * Get the value for a give element, based on the object path
	 *
	 * @param object|array $record
	 * @param string $propertyPath
	 * @param array $fieldConfiguration
	 * @param bool $htmlentities
	 * @return string|bool
	 */
	protected function getElementRawValue($record, $propertyPath, array $fieldConfiguration) {
		$elementValue = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($record, $propertyPath);

		// stdWrap support
		if (!empty($fieldConfiguration['stdWrap'])) {
			$data = Tx_Extbase_Reflection_ObjectAccess::getGettableProperties($record);
			$this->contentObject->start($data);
			$elementValue = $this->contentObject->stdWrap($elementValue, $fieldConfiguration['stdWrap']);
		}

		if (trim($elementValue) === '') {
			$elementValue = FALSE;
		}

		return $elementValue;
	}

	/**
	 * @param string $elementValue
	 * @param array $fieldConfiguration
	 * @return string
	 */
	protected function processUserFunc($elementValue, array $fieldConfiguration) {
		// Apply defined user function
		if (!isset($fieldConfiguration['userFunc'])) {
			return $elementValue;
		}

		$userObject = t3lib_div::getUserObj($fieldConfiguration['userFunc']['class']);
		if ($userObject !== FALSE) {
			$methodName = $fieldConfiguration['userFunc']['method'];
			$parameters = isset($fieldConfiguration['userFunc']['params']) ? $fieldConfiguration['userFunc']['params'] : array();
			$elementValue = $userObject->$methodName($elementValue, $parameters);
		}

		return $elementValue;
	}

	/**
	 * @param array $fieldConfiguration
	 */
	protected function getFieldName(array $fieldConfiguration) {
		return $fieldConfiguration['element'] ? : t3lib_div::camelCaseToLowerCaseUnderscored($fieldConfiguration['path']);
	}

}