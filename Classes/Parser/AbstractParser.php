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
 * Abstract Parser
 *
 * @package	tx_expose
 * @author	Dominique Feyer <dfeyer@ttree.ch>
 */
abstract class Tx_Expose_Parser_AbstractParser {

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * @var string
	 */
	protected $rootElementName;

	/**
	 * @var tslib_cObj
	 */
	protected $contentObject;

	public function __construct() {
		$this->contentObject = t3lib_div::makeInstance('tslib_cObj');
	}

	/**
	 * Returns the settings at path $path, which is separated by "."
	 *
	 * @param string $path
	 * @return mixed
	 */
	public function getSettingByPath($path) {
		return Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($this->settings, $path);
	}

	/**
	 * @return int
	 */
	protected function getCurrentRecordCounter() {
		return (int)$this->data['_internal'][$this->rootElementName]['total'];
	}

	/**
	 * Get the web service configuration
	 *
	 * @param string $name
	 * @param array $settings
	 * @return array
	 */
	public function initialize($name, array $settings) {
		$this->rootElementName = $name;
		$this->settings = $settings;

		$configurationPath = $this->getSettingByPath('api.conf.' . $this->rootElementName . '.path');

		$configuration = $this->getSettingByPath($configurationPath);

		if (!is_array($configuration) || !count($configuration)) {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Invalid webservice configuration',
				1334309979
			);
		}

		$this->configuration = $configuration;
	}

}
