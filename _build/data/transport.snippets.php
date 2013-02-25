<?php
/**
 * imageSlim transport snippets
 * Copyright 2013 Jason Grant
 * @author Jason Grant
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
 * Description:  Array of snippet objects for imageSlim package
 * @package imageslim
 * @subpackage build
 */

if ( !function_exists('getSnippetContent') ) {
    function getSnippetContent($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<?php','',$o);
        $o = str_replace('?>','',$o);
        $o = trim($o);
        return $o;
    }
}
$snippets = array();

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'imageSlim',
    'description' => 'The Image Slenderizer. Documentation: https://github.com/oo12/imageSlim',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/imageslim.snippet.php'),
),'',true,true);
$properties = include $sources['data'].'/properties/properties.imageslim.php';
$snippets[1]->setProperties($properties);
unset($properties);

return $snippets;