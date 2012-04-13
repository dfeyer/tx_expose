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
 * Access Security by simple token, don't use this kind of
 * security model with SSL on your production server
 *
 * @author		Dominique Feyer (ttree) <dfeyer@ttree.ch>
 * @package		TYPO3
 * @subpackage	tx_expose
 *
 */
final class Tx_Expose_Security_SimpleTokenSecurity implements Tx_Expose_Security_SecurityInterface {

	/**
	 * @var Tx_Extbase_MVC_Request
	 */
	protected $request;

	/**
	 * @var Tx_Expose_Domain_Repository_TokenRepository
	 */
	protected $tokenRepository;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $configuration = array();

	public function __construct() {
		/** @var $objectManager Tx_Extbase_Object_ObjectManager */
		$this->initialize();
	}

	/**
	 * Initialize
	 */
	protected function initialize() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->tokenRepository = $this->objectManager->get('Tx_Expose_Domain_Repository_TokenRepository');
	}

	/**
	 * Get the token value from the current request
	 *
	 * @return string
	 * @throws Tx_Expose_Exception_AccessException|Tx_Expose_Exception_InvalidConfigurationException
	 */
	protected function getTokenArgumentValue() {
		$argumentName = $this->configuration['argumentName'];
		if (trim($argumentName) === '') {
			throw new Tx_Expose_Exception_InvalidConfigurationException(
				'Please provide a valid argumentName for the token hash, check plugin.tx_expose.settings.secure.argumentName',
				1334327623
			);
		}

		// Check security Token
		if ($this->request->hasArgument($argumentName)) {
			$value = t3lib_div::removeXSS($this->request->getArgument($argumentName));
		} else {
			throw new Tx_Expose_Exception_AccessException(
				'Access Not Allowed, no token in URL',
				1334326111
			);
		}

		return $value;
	}

	/**
	 * Validate Access
	 *
	 * @param Tx_Extbase_MVC_Request $request
	 */
	public function validateAccess(Tx_Extbase_MVC_Request $request, array $configuration) {
		$accessAllowed = FALSE;
		$this->request = $request;
		$this->configuration = $configuration;

		$tokenHash = $this->getTokenArgumentValue();
		if (isset($this->configuration['allowedGroups']) && trim($this->configuration['allowedGroups']) !== '') {
			$allowedGroups = t3lib_div::intExplode(',', $this->configuration['allowedGroups']);
		} else {
			throw new Tx_Expose_Exception_AccessException(
				'Access Not Allowed, no group allowed to access this service, check your configuration plugin.tx_expose.secure.configuration.allowedGroups',
				1334326211
			);
		}

		if (!$token = $this->tokenRepository->findOneByHash($tokenHash)) {
			// Avoid brutal force attack
			sleep(10);

			throw new Tx_Expose_Exception_AccessException(
				'Invalid token',
				1334326314
			);
		}

		foreach ($token->getGroups() as $group) {
			if (t3lib_div::inArray($allowedGroups, $group->getUid())) {
				$accessAllowed = TRUE;
				break;
			}
		}

		if (!$accessAllowed) {
			throw new Tx_Expose_Exception_AccessException(
				'Access Not Allowed',
				1334326394
			);
		}

	}
}

?>