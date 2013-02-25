imageSlim 1.0 (beta)
===============

The Image Slenderizer, for MODX.

Tired of clients junking up content areas and richtext TVs with enormous images?  Simply follow these 3 easy steps!

1. Download from the Extras Repository (link coming).
2. Apply a generous helping of imageSlim&trade; to the affected area: ```[[*content:imageSlim]]```
3. Watch that excess page weight melt away.

imageSlim looks through an HTML fragment for \<img\> tags, compares each image's natural size to its desired size and if needed calls phpThumbOf to size it down.

Features
--------

* Runs as a snippet or output filter, so it's fully cacheable
* Targets only the areas you tell it to
* Reads and writes inline styles as well as \<img\> tag width/height attributes
* Offers a variety of paramaters for controlling image size: display size, max width/height, scale (for retina images), or any combination
* Fixes stretched images (optional)
* Processes links to offsite images (optional)
* Resaves pngs and gifs larger than a given file size as jpegs (optional)
* Runs any user-specified phpthumb options on all images in its input

Requirements
------------

* [MODX Revolution](http://modx.com/download/)
* The [phpThumbOf](http://modx.com/extras/package/phpthumbof) extra
* PHP: [DOM Extension](http://www.php.net/manual/en/book.dom.php). imageSlim's installer will tell you whether your server has it. Most do.
* PHP: [allow\_url\_fopen](http://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen) set to On, for imageSlim to be able to process remote images (local images work regardless). The installer will let you know about this one too.

Examples
--------

* As an output filter:<br>```[[*content:imageSlim]]``` or<br>```[[*someRichTextTV:imageSlim=`scale=1.5&maxWidth=800&fixAspect=1`]]```
* As a snippet:<br>```[[imageSlim? &input=`[[*content]]` &remoteImages=`1` &phpthumbofParams=`fltr[]=gray`]]```

Terminology
--------

* _Natural Size_ – The intrisic dimensions of the image.  That is, the pixel dimensions you'd see if you opened up the file in an image editor and checked its size.
* _Display Size_ – The dimensions of the image when it's displayed in a browser. By using the width/height attributes on the \<img\> tag or inline CSS you can make the display size different from the natural size.
* _Aspect Ratio_ – The ratio of the image's width to its height (i.e. width / height).  For example, an image 400px wide and 200px tall has an aspect ratio of 2.


Properties
----------

<table>
<tr><th>Property</th><th>Description</th><th>Default</th></tr>
<tr>
  <td>&amp;input</td>
  <td>The HTML fragment to check<br>
    <b>Required</b> when calling imageSlim as a snippet</td>
  <td></td>
</tr><tr>
  <td>&amp;maxWidth</td>
  <td>Maximum <i>display</i> width of an image. Maximum <i>natural</i> width will be &amp;maxWidth * &amp;scale. See <a href="#an-example-of-image-sizing">this section</a> below if you're confused.<br>Units: pixels</td>
  <td></td>
</tr><tr>
  <td>&amp;maxHeight</td>
  <td>Maximum <i>display</i> height of an image. Maximum <i>natural</i> height will be &amp;maxHeight * &amp;scale. See <a href="#an-example-of-image-sizing">this section</a> below if you're confused.<br>Units: pixels</td>
  <td></td>
</tr><tr>
  <td>&amp;scale</td>
  <td>Allow the natural size of the image to exceed its display size by this factor.<br>Use a value between 1.5 and 2 for retina displays. A scale of 1 will keep the image's natural size the same as its display size.</td>
  <td>1</td>
</tr><tr>
  <td>&amp;convertThreshold</td>
  <td>Convert any other image format (png, gif, bmp, etc) with a file size larger than this value to a jpeg, whether or not it's also being sized down.<br>Units: kilobytes<br>Setting this to 0 will convert all lossless formats to jpeg.</td>
  <td></td>
</tr><tr>
  <td>&amp;fixAspect</td>
  <td>If an image is being stretched, fix it by keeping its display dimensions and zoom cropping the image to display dimensions * &amp;scale.<br>Stretching occurs when its display aspect ratio is different from its natural aspect ratio (i.e. sombody unchecked 'Constrain proportions').</td>
  <td>Yes</td>
</tr><tr>
  <td>&amp;remoteImages</td>
  <td>Allow imageSlim to work with images from other servers.<br>Requires proper settings for allow_url_fopen in PHP and phpthumb_nohotlink_enabled and phpthumb_nohotlink_valid_domains in the MODX system settings (core > phpthumb)<br>Remote images take longer to process than local ones, obviously.</td>
  <td>No</td>
</tr><tr>
  <td>&amp;q</td>
  <td>JPEG quality: 1 (worst) &ndash; 95 (best)</td>
  <td>75</td>
</tr><tr><td>&amp;phpthumbofParams</td>
  <td>An optional string of parameters to pass to phpThumbOf.<br>Be careful with this one though, since phpThumbOf will be run on <i>every</i> image in the input, not just the oversized ones.<br>Certain parameters–w, h, f, q, zc–may be overridden by imageSlim depending on the image and other settings.</td>
  <td></td>
</tr><tr><td>&amp;debug</td>
  <td>Output debug info in an HTML comment.</td>
  <td>No</td>
</tr>
</table>

An Example of Image Sizing
------------------------

If you're finding it hard to grasp how natural size, display size, max size and scale all relate to one another, let's do an example.  Suppose some blissfully unaware user sticks a giant image into Content.  It's got a natural size, say, of 2000px x 1600px.  That's way too big for the page, so he's set the dimensions to be 900px x 720px (hey, at least the aspect ratio is the same for both (1.25) and it won't be stretched).  Done and dusted.  Only why is that image loading so darned slow?  Time for a little imageSlim&trade;.

On the front end, the container [[*content]] is going into is semi-fluid, but you know it's a maximum of 800px wide.  So we'll set &amp;maxWidth to 800.  Then we might as well take advantage of any extra resolution to make sharper images for retina displays; let's set &amp;scale to 1.5.  Apply some imageSlim....

I.S. quickly discovers that monstrous image and jumps into action.  Had no &amp;maxWidth been set, it'd use the indicated display size of 900 x 720.  But in our case that's still too big and imageSlim will change the display width to 800 px (update the inline CSS or width/heigh attributes and everything, nice).  Then it calls phpThumbOf to create an image with a natural width of 1200 pixels (800 x 1.5).  Since the aspect ratios matched to begin with, the height gets scaled proportionally.  The final image has a display size of 800 x 640 and a natural size of 1200 x 960.

Notes
-------

* _CSS_ – imageSlim only works with inline CSS. If you're affecting image sizes somewhere else in your CSS, it won't see that. Also if the dimensions in the inline CSS are given in a unit other than pixels (% or em, for instance) it'll ignore those, since it'd be potentially quite involved to translated those values into pixels.
* _Resizing_ – If imageSlim needs to change the display size of an image, it'll use the same method as the image initially had (either inline CSS or \<img\> tag attributes).
* _Vector graphics_ – imageSlim knows better than to mess with SVG. There's no point!
* _Caching_ - Please don't do this: ```[[!imageSlim? &input=`...`]]``` (that is, call it uncached), especially on an area which might have more than a few images.  I spent a bunch of time making imageSlim as light and efficient as possible, but phpthumb is a lumbering beast. Though phpthumb does cache its output, so after the first run it only has to regenerate images when the filename, or parameters (or resource) changes, or you go to Site > Clear Cache in the Manager.
* _Image formats_ – imageSlim will convert any non-jpegs (gifs, bmps, tiffs, etc) it has to resize into pngs by default.
* _Stretched images_ – if you have &amp;fixAspect on, imageSlim will fix them _if_ it has enough resolution to work with. If the display size exceeds the natural size, it's not going to interpolate images up to fix the aspect ratio.  However if the image is being sized down because of &amp;maxWidth or &amp;maxHeight, imageSlim will still try to fix it, even lowering the scale some if it needs to (just not lower than 1).

[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/62b82f55f4fa8e341951547aad88c15d "githalytics.com")](http://githalytics.com/oo12/imageSlim)