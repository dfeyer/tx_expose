<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_expose_domain_model_group'] = array(
	'ctrl' => $TCA['tx_expose_domain_model_group']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden, name',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, name'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:expose/Resources/Private/Language/locallang_db.xml:tx_expose_domain_model_group.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		)
	),
);
?>