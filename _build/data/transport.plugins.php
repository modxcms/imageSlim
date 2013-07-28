<?php
/**
 * imageSlim
 * Copyright 2013 Jason Grant
 *
 * Documentation, bug reports, etc.
 * https://github.com/oo12/imageSlim
 *
 * imageSlim is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * imageSlim is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * imageSlim; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package imageslim
 */
/**
 * Package in plugins
 *
 * @package imageslim
 * @subpackage build
 */

if (! function_exists('getPluginContent')) {
	function getpluginContent($filename) {
		$o = file_get_contents($filename);
		$o = str_replace('<?php','',$o);
		$o = str_replace('?>','',$o);
		$o = trim($o);
		return $o;
	}
}
$plugins = array();

$plugins[1]= $modx->newObject('modplugin');
$plugins[1]->fromArray(array(
	'id' => 1,
	'name' => 'imageSlimCacheManager',
	'description' => 'Handles remote images cache cleaning when clearing the site cache.',
	'plugincode' => getPluginContent($sources['source_core'].'/elements/plugins/plugin.imageslimcachemanager.php'),
),'',true,true);
// $properties = include $sources['data'].'properties/properties.myplugin1.php';
// $plugins[1]->setProperties($properties);
// unset($properties);

$events = include $sources['events'].'events.imageslimcachemanager.php';
if (is_array($events) && !empty($events)) {
	$plugins[1]->addMany($events);
	$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for imageSlimCacheManager.'); flush();
} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for imageSlimCacheManager!');
}
unset($events);

return $plugins;