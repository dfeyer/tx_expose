<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_expose_domain_model_token'] = array(
	'ctrl' => $TCA['tx_expose_domain_model_token']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden, hash, groups',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, hash, groups'),
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
		'hash' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:expose/Resources/Private/Language/locallang_db.xml:tx_expose_domain_model_token.hash',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'groups' => array(
			'exclude' => 0,
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:eventival/Resources/Private/Language/locallang_db.xml:tx_expose_domain_model_token.groups',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_expose_domain_model_group',
				'MM' => 'tx_expose_token_group_mm',
				'size' => 10,
				'autoSizeMax' => 30,
				'maxitems' => 9999,
				'multiple' => 0,
				'wizards' => array(
					'_PADDING' => 1,
					'_VERTICAL' => 1,
					'edit' => array(
						'type' => 'popup',
						'title' => 'Edit',
						'script' => 'wizard_edit.php',
						'icon' => 'edit2.gif',
						'popup_onlyOpenIfSelected' => 1,
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new',
						'icon' => 'add.gif',
						'params' => array(
							'table' => 'tx_expose_domain_model_group',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
				),
			),
		),
	),
);
?>