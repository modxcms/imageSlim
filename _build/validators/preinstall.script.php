<?php
/**
 * imageSlim pre-install script
 *
 * Copyright 2013 Jason Grant <dadima@gmail.com>
 * @author Jason Grant <dadima@gmail.com>
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
 * @package imageSlim
 */
/**
 * Description: Example validator checks for existence of getResources
 * @package imageSlim
 * @subpackage build
 */
/**
 * @package imageSlim
 * Validators execute before the package is installed. If they return
 * false, the package install is aborted. This example checks for
 * the installation of getResources and aborts the install if
 * it is not found.
 */

/* The $modx object is not available here. In its place we
 * use $object->xpdo
 */
$modx =& $object->xpdo;


$modx->log(xPDO::LOG_LEVEL_INFO,'Running pre-install validator');
switch($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:

		$modx->log(xPDO::LOG_LEVEL_INFO,'Checking for dependencies...');
		$success = true;
		if ( $modx->getObject('modSnippet',array('name'=>'phpthumbof')) ) {
			$modx->log(xPDO::LOG_LEVEL_INFO,'MODX: phpthumbof - OK');
		}
		else {
			$modx->log(xPDO::LOG_LEVEL_ERROR,'imageSlim requires the phpthumbof package [ http://modx.com/extras/package/phpthumbof ]. Please install it, then try again.');
			$success = false;
		}
		if (class_exists('DOMDocument')) {
			$modx->log(xPDO::LOG_LEVEL_INFO,'PHP: DOM - OK');
		}
		else {
			$modx->log(xPDO::LOG_LEVEL_ERROR,'imageSlim requires the PHP DOM extension [ http://www.php.net/manual/en/book.dom.php ]');
			$success = false;
		}
		if (function_exists('curl_init')) {
			$modx->log(xPDO::LOG_LEVEL_INFO,'cURL - OK');
		}
		else {
			$modx->log(xPDO::LOG_LEVEL_INFO,'cURL - NOT FOUND  |  imageSlim will skip any remote images');
		}

		break;
   /* These cases must return true or the upgrade/uninstall will be cancelled */
   case xPDOTransport::ACTION_UPGRADE:
		$success = true;
		break;

	case xPDOTransport::ACTION_UNINSTALL:
		$success = true;
		break;
}

return $success;