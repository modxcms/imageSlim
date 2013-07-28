<?php

/**
 * Default properties for the imageSlim snippet
 * @author Jason Grant
 *
 * @package imageslim
 * @subpackage build
 */

$properties = array(
	array(
		'name' => 'fixAspect',
		'desc' => 'prop_is.fixAspect_desc',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => '1',
		'area' => 'Dimensions',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'maxHeight',
		'desc' => 'prop_is.maxHeight_desc',
		'type' => 'integer',
		'options' => '',
		'value' => '',
		'area' => 'Dimensions',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'maxWidth',
		'desc' => 'prop_is.maxWidth_desc',
		'type' => 'integer',
		'options' => '',
		'value' => '',
		'area' => 'Dimensions',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'scale',
		'desc' => 'prop_is.scale_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => '1',
		'area' => 'Dimensions',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'remoteImages',
		'desc' => 'prop_is.remoteImages_desc',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => '0',
		'area' => 'Remote Images',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'remoteTimeout',
		'desc' => 'prop_is.remoteTimeout_desc',
		'type' => 'integer',
		'options' => '',
		'value' => '5',
		'area' => 'Remote Images',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'convertThreshold',
		'desc' => 'prop_is.convertThreshold_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'phpthumbof',
		'desc' => 'prop_is.phpthumbof_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'q',
		'desc' => 'prop_is.q_desc',
		'type' => 'integer',
		'options' => '',
		'value' => '',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'debug',
		'desc' => 'prop_is.debug_desc',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => '0',
		'lexicon' => 'imageslim:default'
		),
	array(
		'name' => 'imgSrc',
		'desc' => 'prop_is.imgSrc_desc',
		'type' => 'textfield',
		'options' => '',
		'value' => 'src',
		'lexicon' => 'imageslim:default'
		)
);

return $properties;

