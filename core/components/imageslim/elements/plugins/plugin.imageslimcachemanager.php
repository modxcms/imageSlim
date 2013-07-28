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
 * Handles cache management for imageSlim
 *
 * @var modX $modx
 *
 * @package imageslim
 */

if ($modx->event->name !== 'OnSiteRefresh') {
	return;
}

$cachePath = MODX_ASSETS_PATH . 'components/imageslim/cache/';

if (!is_writable($cachePath)) {  // check that the cache directory is writable
	if (!$modx->cacheManager->writeTree($cachePath)) {
		$modx->log(modX::LOG_LEVEL_ERROR, '[imageSlim] Cache path not writable: ' . $cachePath);
		return;
	}
}

$cache_maxage = $modx->getOption('phpthumb_cache_maxage', NULL, 30) * 86400;
$cache_maxsize = $modx->getOption('phpthumb_cache_maxsize', NULL, 100) * 1048576;
$cache_maxfiles = (int) $modx->getOption('phpthumb_cache_maxfiles', NULL, 10000);
$modx->log(modX::LOG_LEVEL_INFO, 'imageSlimCacheManager: Cleaning imageSlim remote images cache...');
$modx->log(modX::LOG_LEVEL_INFO, ":: Max Age: $cache_maxage seconds || Max Size: $cache_maxsize bytes || Max Files: $cache_maxfiles");

if (!($cache_maxage || $cache_maxsize || $cache_maxfiles)) {
	return;
}

$DeletedKeys = array();
$AllFilesInCacheDirectory = array();
$dirname = rtrim(realpath($cachePath), '/\\');
if ($dirhandle = @opendir($dirname)) {
	while (($file = readdir($dirhandle)) !== FALSE) {
		$fullfilename = $dirname . DIRECTORY_SEPARATOR . $file;
		if (is_file($fullfilename) && preg_match('/(jpe?g|png|gif)$/', $file)) {
			$AllFilesInCacheDirectory[] = $fullfilename;
		}
	}
	closedir($dirhandle);
}
$totalimages = count($AllFilesInCacheDirectory);
$modx->log(modX::LOG_LEVEL_INFO, ":: $totalimages image" . ($totalimages !== 1 ? 's':'') . ' in the cache');

if (empty($AllFilesInCacheDirectory)) {
	return;
}

$CacheDirOldFilesAge  = array();
$CacheDirOldFilesSize = array();
foreach ($AllFilesInCacheDirectory as $fullfilename) {
	$CacheDirOldFilesAge[$fullfilename] = @fileatime($fullfilename);
	if ($CacheDirOldFilesAge[$fullfilename] == 0) {
		$CacheDirOldFilesAge[$fullfilename] = @filemtime($fullfilename);
	}
	$CacheDirOldFilesSize[$fullfilename] = @filesize($fullfilename);
}
$DeletedKeys['zerobyte'] = array();
foreach ($CacheDirOldFilesSize as $fullfilename => $filesize) {
	// purge all zero-size files more than an hour old (to prevent trying to delete just-created and/or in-use files)
	$cutofftime = time() - 3600;
	if (($filesize == 0) && ($CacheDirOldFilesAge[$fullfilename] < $cutofftime)) {
		if (@unlink($fullfilename)) {
			$DeletedKeys['zerobyte'][] = $fullfilename;
			unset($CacheDirOldFilesSize[$fullfilename]);
			unset($CacheDirOldFilesAge[$fullfilename]);
		}
	}
}
$modx->log(modX::LOG_LEVEL_INFO, ':: Purged ' . count($DeletedKeys['zerobyte']) . ' zero-byte images');
asort($CacheDirOldFilesAge);

if ($cache_maxfiles) {
	$TotalCachedFiles = count($CacheDirOldFilesAge);
	$DeletedKeys['maxfiles'] = array();
	foreach ($CacheDirOldFilesAge as $fullfilename => $filedate) {
		if ($TotalCachedFiles > $cache_maxfiles) {
			if (@unlink($fullfilename)) {
				--$TotalCachedFiles;
				$DeletedKeys['maxfiles'][] = $fullfilename;
			}
		} else {  // there are few enough files to keep the rest
			break;
		}
	}
	$modx->log(modX::LOG_LEVEL_INFO, ':: Purged ' . count($DeletedKeys['maxfiles']) . " images based on (cache_maxfiles=$cache_maxfiles)");
	foreach ($DeletedKeys['maxfiles'] as $fullfilename) {
		unset($CacheDirOldFilesAge[$fullfilename]);
		unset($CacheDirOldFilesSize[$fullfilename]);
	}
}

if ($cache_maxage) {
	$mindate = time() - $cache_maxage;
	$DeletedKeys['maxage'] = array();
	foreach ($CacheDirOldFilesAge as $fullfilename => $filedate) {
		if ($filedate) {
			if ($filedate < $mindate) {
				if (@unlink($fullfilename)) {
					$DeletedKeys['maxage'][] = $fullfilename;
				}
			} else {  // the rest of the files are new enough to keep
				break;
			}
		}
	}
	$modx->log(modX::LOG_LEVEL_INFO, ':: Purged ' . count($DeletedKeys['maxage']) . ' images based on (cache_maxage='. $cache_maxage / 86400 .' days)');
	foreach ($DeletedKeys['maxage'] as $fullfilename) {
		unset($CacheDirOldFilesAge[$fullfilename]);
		unset($CacheDirOldFilesSize[$fullfilename]);
	}
}

if ($cache_maxsize) {
	$TotalCachedFileSize = array_sum($CacheDirOldFilesSize);
	$DeletedKeys['maxsize'] = array();
	foreach ($CacheDirOldFilesAge as $fullfilename => $filedate) {
		if ($TotalCachedFileSize > $cache_maxsize) {
			if (@unlink($fullfilename)) {
				$TotalCachedFileSize -= $CacheDirOldFilesSize[$fullfilename];
				$DeletedKeys['maxsize'][] = $fullfilename;
			}
		} else {  // the total filesizes are small enough to keep the rest of the files
			break;
		}
	}
	$modx->log(modX::LOG_LEVEL_INFO, ':: Purged ' . count($DeletedKeys['maxsize']) . ' images based on (cache_maxsize=' . $cache_maxsize / 1048576 . ' MB)');
	foreach ($DeletedKeys['maxsize'] as $fullfilename) {
		unset($CacheDirOldFilesAge[$fullfilename]);
		unset($CacheDirOldFilesSize[$fullfilename]);
	}
}

$totalpurged = 0;
foreach ($DeletedKeys as $key => $value) {
	$totalpurged += count($value);
}
$modx->log(modX::LOG_LEVEL_INFO, ":: Purged $totalpurged images out of $totalimages");