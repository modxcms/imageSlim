<?php
/**
 * imageSlim
 * Copyright 2013 Jason Grant
 *
 * imageSlim is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * imageSlim is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * imageSlim; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 *
 * @package imageslim
 * @author Jason Grant
 * @version 1.1.0-pl
 */

/**
 * Documentation, bug reports, etc.
 * https://github.com/oo12/imageSlim
 *
 * Variables
 * ---------
 *
 * @var modX $modx
 * @var input $input
 * @var options $options
 *
 *
 * Properties
 * ----------
 *
 * @property float scale
 * @property float conventThreshold
 * @property integer maxWidth
 * @property integer maxHeight
 * @property string phpthumbof
 * @property boolean fixAspect
 * @property boolean remoteImages
 * @property integer remoteTimeout
 * @property integer q
 * @property string imgSrc
 * @property boolean debug
 *
 * See the default properties for a description of each.
 *
 * @package imageslim
 **/

if (empty($input)) { return; }  // if we've got nothing to do, it's quittin' time

if (isset($options)) {  // if we're being called as an output filter, set variables for any options
	parse_str($options);
}

// process our properties
$scale = empty($scale) ? 1 : (float) $scale;
$convertThreshold = isset($convertThreshold) && $convertThreshold !== '' ? (float) $convertThreshold * 1024 : FALSE;
$maxWidth = isset($maxWidth) && $maxWidth !== '' ? (int) $maxWidth: 999999;
$maxHeight = isset($maxHeight) && $maxHeight !== '' ? (int) $maxHeight: 999999;
$phpthumbof = isset($phpthumbof) ? $phpthumbof : '';
$fixAspect = isset($fixAspect) ? (bool) $fixAspect : TRUE;
$remoteImages = isset($remoteImages) ? (bool) $remoteImages && function_exists('curl_init') : FALSE;
$remoteTimeout = isset($remoteTimeout) ? (int) $remoteTimeout : 5;
$q = empty($q) ? '' : (int) $q;
$imgSrc = empty($imgSrc) ? 'src' : $imgSrc;
$debug = isset($debug) ? (bool) $debug : FALSE;

$debug &&   $debugstr = "i m a g e S l i m  [1.1.0-pl]\nimgSrc:$imgSrc  scale:$scale  convertThreshold:" . ($convertThreshold ? $convertThreshold / 1024 . 'KB' : 'none') . "\nmaxWidth:$maxWidth  maxHeight:$maxHeight  q:$q\nfixAspect:$fixAspect  phpthumbof:$phpthumbof\nRemote images:$remoteImages  Timeout:$remoteTimeout  cURL:" . (!function_exists('curl_init') ? 'not ':'') . "installed\n";

$cachePath = MODX_ASSETS_PATH . 'components/imageslim/cache/';
$remoteDomains = FALSE;
$dom = new DOMDocument;
@$dom->loadHTML('<?xml encoding="UTF-8">' . $input);  // load this mother up

$emptynode = $dom->createTextNode('');
foreach (array('iframe', 'video', 'audio') as $tag) {  // prevent certain tags from getting turned into self-closing tags by domDocument
	foreach ($dom->getElementsByTagName($tag) as $node) {
		$node->appendChild($emptynode);
	}
}

foreach ($dom->getElementsByTagName('img') as $node) {  // for all our images
	$src = $node->getAttribute($imgSrc);
	$file = $size = FALSE;
	if ( preg_match('/^(?:https?:)?\/\/(.+?)\/(.+)/i', $src, $matches) ) {  // if we've got a remote image to work with
		if (!$remoteImages) {
			$debug &&   $debugstr .= "\nsrc:$src\n*** Remote image not allowed. Skipping ***\n";
			continue;
		}
		$file = $cachePath . preg_replace("/[^\w\d\-_\.]/", '-', "{$matches[1]}-{$matches[2]}");
		if (!file_exists($file)) {  // if it's not in our cache, go get it
			$debug &&   $debugstr .= "Retrieving $src\nTarget filename: $file\n";
			$fh = fopen($file, 'wb');
			if (!$fh) {
				$debug &&   $debugstr .= "*** Error ***  Can't write to cache directory $cachePath\n";
				continue;
			}
			$curlFail = FALSE;
			$ch = curl_init($src);
			curl_setopt_array($ch, array(
				CURLOPT_TIMEOUT	=> $remoteTimeout,
				CURLOPT_FILE => $fh,
				CURLOPT_FAILONERROR => TRUE
			));
			curl_exec($ch);
			if (curl_errno($ch)) {
				$debug &&   $debugstr .= 'cURL error: ' . curl_error($ch) . " *** Skipping ***\n";
				$curlFail = TRUE;
			}
			curl_close($ch);
			fclose($fh);
			if ($curlFail) {  // if we didn't get it, skip and don't cache
				unlink($file);
				continue;
			}
		}
		elseif ($debug) { $debugstr .= "Retrieved from cache: $file\n";}
	}
	else {
		$file = MODX_BASE_PATH . rawurldecode(ltrim($src, '/'));  // Fix spaces and other encoded characters in the URL
		$debug &&   $debugstr .= "\nsrc:$file\n";
	}

	$size = @getimagesize($file);  // get the actual size and file type of the image.
	if ($size === FALSE) {  // weed out missing images and formats like svg
		$debug && $debugstr .= "*** Can't get image size. Skipping ***\n";
		continue;
	}

	$type = strtolower( substr($size['mime'], strpos( $size['mime'], '/')+1) );  // extract the image type
	$ar = $size[0] / $size[1];  // calculate our intrinsic aspect ratio

	$w = $wCss = 0;  // initialize some stuff we'll need
	$h = $hCss = 0;
	$updateStyles = $adjustDisplaySize = FALSE;
	parse_str($phpthumbof, $opts);  // add in any user-specified phpthumb parameters

	$styleAttr = $node->getAttribute('style');  // check for width and height in an inline style first
	if ($styleAttr) {
		$styles = array();
		preg_match_all('/([\w-]+)\s*:\s*([^;]+)\s*;?/', $styleAttr, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) { $styles[$match[1]] = $match[2]; }  // bust everything out into an array
		if ( isset($styles['width']) && stripos($styles['width'], 'px') ) {  // if we have a width in pixels
			preg_match('/\d+/', $styles['width'], $matches);
			$wCss = $matches[0];  // get just the value
		}
		if ( isset($styles['height']) && stripos($styles['height'], 'px') ) {  // same deal for height
			preg_match('/\d+/', $styles['height'], $matches);
			$hCss = $matches[0];
		}
	}
	$w = $wCss ? $wCss : $node->getAttribute('width');  // if we don't have a CSS width, get it from the width attribute
	$h = $hCss ? $hCss : $node->getAttribute('height');
	$debug &&   $debugstr .= "w:$w  h:$h  realw:{$size[0]}  realh:{$size[1]}  type:$type\n";

	$aspectNeedsFix = FALSE;  // let's see if we need to fix a stretched image
	if ($fixAspect && $w && $h) {
		$new_ar = $w / $h;
		if ( abs($new_ar - $ar) > 0.01 ) {  // allow a little discrepancy, but nothing crazy
			$aspectNeedsFix = TRUE;
			$ar = $new_ar;
			$maxScale = min($scale, $size[0] / $w, $size[1] / $h);
			if ($maxScale >= 1) {  // if we've got enough resolution to correct it, let's go ahead and set that up
				$opts['w'] = round($w * $maxScale);
				$opts['h'] = round($h * $maxScale);
				$debug &&   $debugstr .= "++ Fixing aspect ratio. w:{$opts['w']}  h:{$opts['h']}  scale:$maxScale  zc:1\n";
			}
			elseif ($debug)  { $debugstr .= "!! Image stretched.  scale:$maxScale\n"; }  // otherwise we might be able to if the image gets sized down below
		}
	}

	$heightPlay = 0;  // used to prevent height resizing on a 1px rounding difference
	if ( $w && ($w > $maxWidth || $size[0] > $w * $scale)  ||  $size[0] > $maxWidth * $scale ) {
		$wMax = $scale * ($w ? ( $w < $maxWidth ? $w : $maxWidth ) : $maxWidth);
		$wMax = $wMax > $size[0] ? $size[0] : $wMax;
		$opts['w'] = $wMax;
		$newH = $size[1] < $wMax/$ar ? $size[1] : $wMax / $ar;
		if ($aspectNeedsFix) {
			$opts['w'] = $wMax < $size[1]*$ar ? $wMax : $size[1] * $ar;  // reduce scale if we need to to fix a stretched image
			$opts['h'] = $newH;
		}
		$size[1] = $newH;
		$heightPlay = 1;
		$debug &&   $debugstr .= "++(W) realw:{$opts['w']}  realh:{$size[1]}\n";
		if ($maxWidth && $w > $maxWidth) {  // if we need to change the display sizing
			$w = $maxWidth;
			$h = round($maxWidth / $ar);
			$adjustDisplaySize = TRUE;  // we'll set the size in a bit..
			$debug && $debugstr .= "++   w:$w  h:$h\n";
		}
	}

	if ( $h && ($h > $maxHeight || $size[1] - $heightPlay > $h * $scale)  ||  $size[1] - $heightPlay > $maxHeight * $scale ) {
		$hMax = $scale * ($h ? ( $h < $maxHeight ? $h : $maxHeight ) : $maxHeight);
		$hMax = $hMax > $size[1] ? $size[1] : $hMax;
		$opts['h'] = $hMax;
		if ($aspectNeedsFix)  {
			$opts['h'] = $hMax < $size[0]/$ar ? $hMax : $size[0] / $ar;  // reduce scale if we need to to fix a stretched image
			$opts['w'] = $opts['h'] * $ar;
		}
		else  { unset($opts['w']); }  // forget about width, since height is our limiting dimension
		$debug &&   $debugstr .= '++(H) ' . (isset($opts['w']) ? $opts['w'] : '') . " realh:{$opts['h']}\n";
		if ($maxHeight && $h > $maxHeight) {
			$h = $maxHeight;
			$w = round($maxHeight * $ar);
			$adjustDisplaySize = TRUE;
			$debug &&   $debugstr .= "++   w:$w  h:$h\n";
		}
	}
	if (isset($opts['w']))  { $opts['w'] = round($opts['w']); }  // round these to integers
	if (isset($opts['h']))  { $opts['h'] = round($opts['h']); }

	if ($adjustDisplaySize) {  // ok, update our display size if we need do
		if ($wCss) {  // if the width was in an inline style (and in px), use that
			$styles['width'] = $w . 'px';
			$updateStyles = TRUE;
		}
		else { $node->setAttribute('width', $w); }

		if ($hCss) {  // same for height
			$styles['height'] = $h . 'px';
			$updateStyles = TRUE;
		}
		else { $node->setAttribute('height', $h); }
	}

	if ($convertThreshold !== FALSE && $type !== 'jpeg') {
		$fsize = filesize($file);
		if ($fsize > $convertThreshold) {  // if we've got a non-jpeg that's too big, convert it to jpeg
			$opts['f'] = 'jpeg';
			$debug &&   $debugstr .= "File size:$fsize  Threshold exceeded; converting to jpeg.\n";
		}
	}

	if (!empty($opts)) {  // have we anything to do for this lovely image?
		if ($aspectNeedsFix)  { $opts['zc'] = 1; }
		if ( !isset($opts['f']) ) {  // if output file type isn't user specified...
			$opts['f'] = ($type === 'jpeg' ? 'jpeg' : 'png');  // if it's a gif or bmp let's just make it a png, shall we?
		}
		if ($q && $opts['f'] === 'jpeg')  { $opts['q'] = $q; }  // add user-specified jpeg quality if it's relevant
		if ($opts['f'] === 'jpeg') { $opts['f'] = 'jpg'; }  // workaround for phpThumbOf issue #53
		$image = array();
		$image['input'] = $file;
		$option_str = '';
		foreach ($opts as $k => $v)  {  // turn our phpthumb options array back into a string
			if (is_array($v)) {  // handle any array options like fltr[]
				foreach($v as $param) { $option_str .= $k . "[]=$param&"; }
			}
			else { $option_str .= "$k=$v&"; }
		}
		$image['options'] = rtrim($option_str, '&');
		$debug &&   $debugstr .= "phpthumbof options: {$image['options']}\n";
		$node->setAttribute( $imgSrc, $modx->runSnippet('phpthumbof', $image) );  // do the business and set the src
		if ($updateStyles) {
			$style = '';
			foreach($styles as $k => $v) { $style .= "$k:$v;"; }  // turn $styles array into an inline style string
			$node->setAttribute('style', $style);
		}
	}
}

$output = str_replace('&#13;', '', substr($dom->saveXML($dom->documentElement), 12, -14) );  // strip off the <body> tags and CR characters that DOM adds (?)
$debug &&   $output = "<!--\n$debugstr-->\n$output";
return $output;