<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Expose Webservice Configuration');

t3lib_extMgm::allowTableOnStandardPages('tx_expose_domain_model_token');
$TCA['tx_expose_domain_model_token'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:expose/Resources/Private/Language/locallang_db.xml:tx_expose_domain_model_token',
		'label' => 'hash',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Token.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_expose_domain_model_token.gif'
	),
);

t3lib_extMgm::allowTableOnStandardPages('tx_expose_domain_model_group');
$TCA['tx_expose_domain_model_group'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:expose/Resources/Private/Language/locallang_db.xml:tx_expose_domain_model_group',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Group.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_expose_domain_model_group.gif'
	),
);


?>
