<?php

########################################################################
# Extension Manager/Repository config file for ext "expose".
#
# Auto generated 13-04-2012 11:36
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Expose Extbase Model in standard Webservices',
	'description' => 'Expose any Extbase domain model as a read only webservice',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.1.0',
	'dependencies' => 'cms,extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dominique Feyer',
	'author_email' => 'dfeyer@ttree.ch',
	'author_company' => 'ttree ltd',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:4:{s:14:"ext_tables.php";s:4:"42f3";s:28:"Classes/MVC/View/XMLView.php";s:4:"3467";s:27:"Configuration/constants.txt";s:4:"ec71";s:23:"Configuration/setup.txt";s:4:"ec71";}',
	'suggests' => array(
	),
);

?>