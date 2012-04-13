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
 * Basic Controller
 *
 * @author		Dominique Feyer (ttree) <dfeyer@ttree.ch>
 * @package		TYPO3
 * @subpackage	tx_expose
 *
 */
abstract class Tx_Expose_MVC_Controller_BasicController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Default View object
	 *
	 * @var string
	 */
	protected $defaultViewObjectName = 'Tx_Expose_MVC_View_XMLView';

	/**
	 * Supported output format
	 *
	 * @var array
	 */
	protected $viewFormatToObjectNameMap = array(
		'xml' => 'Tx_Expose_MVC_View_XMLView',
		'json' => 'Tx_Expose_MVC_View_JSONView'
	);

}

?>