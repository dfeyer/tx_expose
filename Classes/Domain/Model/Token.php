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
 * Resume
 *
 * This class ...
 *
 * @package	tx_expose
 * @author	Dominique Feyer <dfeyer@ttree.ch>
 */
class Tx_Expose_Domain_Model_Token extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Security Hash
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $hash;

	/**
	 * externalIdentifier
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Expose_Domain_Model_Group>
	 */
	protected $groups;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->groups = new Tx_Extbase_Persistence_ObjectStorage();
	}

	/**
	 * @param \Tx_Extbase_Persistence_ObjectStorage $groups
	 */
	public function setGroups($groups) {
		$this->groups = $groups;
	}

	/**
	 * @return \Tx_Extbase_Persistence_ObjectStorage
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @param string $hash
	 */
	public function setHash($hash) {
		$this->hash = $hash;
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return $this->hash;
	}

}
?>