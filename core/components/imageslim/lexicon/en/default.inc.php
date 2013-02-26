<?php
/**
 * imageSlim
 *
 *
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
 * imageSlim; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package imageslim
 */
/**
 * Default Lexicon Topic
 *
 * @package imageslim
 * @subpackage lexicon
 */

//properties
$_lang['prop_is.convertThreshold_desc'] = 'Convert any other image format (png, gif, bmp, etc) with a file size larger than this value to a jpeg, whether or not it&rsquo;s also being sized down.<br>Units: kilobytes<br>Setting this to 0 will convert all lossless formats to jpeg.';
$_lang['prop_is.fixAspect_desc'] = 'If an image is being stretched, fix it by keeping its display dimensions and zoom cropping the image to display dimensions * &amp;scale.<br>Stretching occurs when its display aspect ratio is different from its natural aspect ratio.';
$_lang['prop_is.maxHeight_desc'] = 'Maximum display height of an image.<br>Units: pixels';
$_lang['prop_is.maxWidth_desc'] = 'Maximum display width of an image.<br>Units: pixels';
$_lang['prop_is.phpthumbof_desc'] = 'An optional string of parameters to pass to phpThumbOf.<br>Be careful with this one though, since phpThumbOf will be run on <b>every</b> image in the input, not just the oversized ones.<br>Certain parameters&mdash;w, h, f, q, zc&mdash;may be overridden by imageSlim depending on the image and other settings.';
$_lang['prop_is.q_desc'] = 'JPEG quality: 1 (worst) &ndash; 95 (best)<br><b>Default:</b> 75';
$_lang['prop_is.remoteImages_desc'] = 'Allow imageSlim to work with images from other servers.<br>Requires proper settings for allow_url_fopen in PHP and phpthumb_nohotlink_enabled and phpthumb_nohotlink_valid_domains in the MODX system settings (core > phpthumb)<br>Remote images take longer to process than local ones, obviously.';
$_lang['prop_is.scale_desc'] = 'Allow the natural size of the image to exceed its display size by this factor.<br>Use a value between 1.5 and 2 for retina displays. A scale of 1 will keep the image&rsquo;s natural size the same as its display size.';
$_lang['prop_is.debug_desc'] = 'Output debug info in an HTML comment';