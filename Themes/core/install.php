<?php
/*
	Package manifest file for Additional Instant Messengers

	Author - hcfwesker (http://www.simplemachines.org/community/index.php?action=profile;u=244295)
	License - http://creativecommons.org/licenses/by-sa/3.0/ CC BY-SA 3.0
	
	Version - 1.0.1
*/

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$ssi = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc;

if(!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$column_array = array(
	'column1' => array(
		'name' => 'skype', 
		'type' => 'varchar',
		'size' => '45',
		'null' => false,
		'default' => '',
	),
	'column2' => array(
		'name' => 'gtalk', 
		'type' => 'varchar',
		'size' => '45',
		'null' => false,
		'default' => '',
	),
);

foreach ($column_array as $key => $data)
{
	$smcFunc['db_add_column'](
		'{db_prefix}members',
		$data,
		array(),
		'update',
		'fatal'
	);
}

?>