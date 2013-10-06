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
 * Adds events to imageSlimCacheManager plugin
 *
 * @package imageslim
 * @subpackage build
 */
$events = array();

$events['OnSiteRefresh']= $modx->newObject('modPluginEvent');
$events['OnSiteRefresh']->fromArray(array(
	'event' => 'OnSiteRefresh',
	'priority' => 1,
	'propertyset' => 0,
),'',true,true);

return $events;