<?php
/** Array of system settings for Mycomponent package
 * @package mycomponent
 * @subpackage build
 */


/* This section is ONLY for new System Settings to be added to
 * The System Settings grid. If you include existing settings,
 * they will be removed on uninstall. Existing setting can be
 * set in a script resolver (see install.script.php).
 */
$settings = array();

$idx = 1;

$settings['imageslim_setting' . $idx]= $modx->newObject('modSystemSetting');
$settings['imageslim_setting' . $idx++]->fromArray(array (
	'key' => 'imageslim.use_resizer',
	'value' => TRUE,
	'xtype' => 'combo-boolean',
	'namespace' => 'imageslim',
	// 'area' => 'Dimensions',
), '', true, true);

return $settings;