imageSlim 1.1.3-pl
===============

The Image Slenderizer, for MODX.

Tired of clients junking up content areas and richtext TVs with enormous images?  Simply follow these 3 easy steps!

1. Download from the [Extras Repository](http://modx.com/extras/package/imageslim).
2. Apply a generous helping of imageSlim&trade; to the affected area: ```[[*content:imageSlim]]```
3. Watch that excess page weight melt away.

imageSlim looks through an HTML fragment for \<img\> tags, compares each image's natural size to its desired size and if needed calls phpThumbOf to size it down.

Features
--------

* Runs as a snippet or output filter, so it's fully cacheable
* Targets only the areas you tell it to
* Reads and writes inline CSS as well as \<img\> tag width/height attributes (works fine with both [TinyMCE](http://modx.com/extras/package/tinymce) and [CKEditor](http://modx.com/extras/package/ckeditor))
* Offers a variety of paramaters for controlling image size: display size, max width/height, scale (for retina images), or any combination
* Fixes stretched images (optional)
* Processes links to offsite images (optional)
* Resaves pngs and gifs larger than a given file size as jpegs (optional)
* Runs any user-specified phpthumb options on all images in its input

Requirements
------------

* Either [pThumb](http://modx.com/extras/package/pthumb) or phpThumbOf. I recommend pThumb since it’s fully compatible with phpThumbOf, _much_ faster, and eliminates some [potential problems](#phpthumbof-notes) caused by phpThumbOf bugs.
* PHP: [DOM Extension](http://www.php.net/manual/en/book.dom.php). imageSlim’s installer will tell you whether your server has it. Most do.
* cURL (for remote images). If you can download imageSlim via the Package Manager, you have cURL.

Examples
--------

* As an output filter:<br>```[[*content:imageSlim]]``` or<br>```[[*someRichTextTV:imageSlim=`scale=1.5&maxWidth=800&fixAspect=1`]]```
* As a snippet:<br>```[[imageSlim? &input=`[[*content]]` &remoteImages=`1` &phpthumbof=`fltr[]=gray`]]```

(Note that imageSlim expects some chunk of HTML as its input, like content created with TinyMCE or CKEditor; you shouldn't give it simply a path to an image. So as a very terse example, ```[[imageSlim? &input=`<img src="http://example.com/foo.jpg" />` &remoteImages=`1` &phpthumbof=`w=400`]]``` would work but ```<img src="[[imageSlim? &input=`http://example.com/foo.jpg` &remoteImages=`1` &phpthumbof=`w=400`]]" />``` wouldn't do what you want.)

Terminology
--------

* _Natural Size_ – The intrisic dimensions of the image.  That is, the pixel dimensions you'd see if you opened up the file in an image editor and checked its size.
* _Display Size_ – The dimensions of the image when it's displayed in a browser. By using the width/height attributes on the \<img\> tag or inline CSS you can make the display size different from the natural size.
* _Aspect Ratio_ – The ratio of the image's width to its height (i.e. width / height).  For example, an image 400px wide and 200px tall has an aspect ratio of 2.


Options
----------

### Properties

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
  <td>1 (Yes)</td>
</tr><tr>
  <td>&amp;remoteImages</td>
  <td>Allow imageSlim to operate on images from other servers (requires cURL). Because imageSlim first downloads any remote images and caches them in assets/components/imageslim/cache/ it’s able to bypass phpThumb’s offsite linking and hotlinking settings.  Plus it makes the performance hit on offsite images a one-time thing; on subsequent runs there’s no speed decrease since the images are cached locally.</td>
  <td>0 (No)</td>
 </tr><tr>
  <td>&amp;remoteTimeout</td>
  <td>Maximum amount of time to allow for a remote image download.<br>Units: seconds</td>
  <td>5</td>
</tr><tr>
  <td>&amp;q</td>
  <td>JPEG quality: 1 (worst) &ndash; 95 (best)</td>
  <td>75</td>
</tr><tr><td>&amp;imgSrc</td>
  <td>Attribute containing the image URL.<br>Normally this is src, but it could be a data attribute instead.</td>
  <td>src</td>
</tr><tr><td>&amp;phpthumbof</td>
  <td>An optional string of parameters to pass to phpThumbOf.<br>Be careful with this one though, since phpThumbOf will be run on <i>every</i> image in the input, not just the oversized ones.<br>Certain parameters–w, h, f, q, zc–may be overridden by imageSlim depending on the image and other settings.</td>
  <td></td>
</tr><tr><td>&amp;debug</td>
  <td>Output debug info in an HTML comment.</td>
  <td>0 (No)</td>
</tr><tr><td>&amp;useResizer</td>
  <td>See the corresponding setting below.</td>
  <td></td>
</tr>
</table>

### Settings

<table>
<tr><th>Setting</th><th>Description</th><th>Default</th></tr>
<tr>
  <td>imageslim.use_resizer</td>
  <td>If pThumb is installed, allow imageSlim to use Resizer even if it isn’t enabled sitewide. Resizer is faster than phpThumb for image sizing.</td>
  <td>Yes</td>
</tr>
</table>


An Example of Image Sizing
------------------------

If you're finding it hard to grasp how natural size, display size, max size and scale all relate to one another, let's do an example.  Suppose some blissfully unaware user sticks a giant image into Content.  It's got a natural size, say, of 2000px x 1600px.  That's way too big for the page, so he's set the dimensions to be 900px x 720px (hey, at least the aspect ratio is the same for both (1.25) and it won't be stretched).  Done and dusted.  Only why is that image loading so darned slow?  Time for a little imageSlim&trade;.

On the front end, the container [[*content]] is going into is semi-fluid, but you know it's a maximum of 800px wide.  So we'll set &amp;maxWidth to 800.  Then we might as well take advantage of any extra resolution to make sharper images for retina displays; let's set &amp;scale to 1.5.  Apply some imageSlim....

I.S. quickly discovers that monstrous image and jumps into action.  Had no &amp;maxWidth been set, it'd use the indicated display size of 900 x 720.  But in our case that's still too big and imageSlim will change the display width to 800 px (update the inline CSS or width/heigh attributes and everything, nice).  Then it calls phpThumbOf to create an image with a natural width of 1200 pixels (800 x 1.5).  Since the aspect ratios matched to begin with, the height gets scaled proportionally.  The final image has a display size of 800 x 640 and a natural size of 1200 x 960.

Notes
-------

* _CSS_ – imageSlim only works with inline CSS. If you're affecting image sizes somewhere else in your CSS, it won't see that. Also if the dimensions in the inline CSS are given in a unit other than pixels (% or em, for instance) it'll ignore those, since it'd be potentially quite involved to translate those values into pixels.
* _Resizing_ – If imageSlim needs to change the display size of an image, it'll use the same method as the image initially had (either inline CSS or \<img\> tag attributes).
* _Vector graphics_ – imageSlim knows better than to mess with SVG. There's no point!
* _Image formats_ – imageSlim will convert any non-jpegs (gifs, bmps, tiffs, etc) it has to resize into pngs by default.
* _Stretched images_ – if you have &amp;fixAspect on, imageSlim will fix them _if_ it has enough resolution to work with. If the display size exceeds the natural size, it's not going to interpolate images up to fix the aspect ratio.  However if the image is being sized down because of &amp;maxWidth or &amp;maxHeight, imageSlim will still try to fix it, even lowering the scale some if it needs to (just not lower than 1).
* _HTML5_ — imageSlim uses PHP’s DOM extension to parse the input and look for images.  DOM is an XML parser; PHP doesn’t have a built-in HTML/HTML5 parser.  Mostly it works fine for HTML, but there are a few discrepencies which can sometimes surface, usually when you’re feeding it content not coming from a rich-text editor like TinyMCE.  Be aware that it always outputs a syntacticly correct XHTML fragment. If you’ve got a tag left open it will close it, or if you’re using a purely HTML5 construct like a boolean attribute then it’ll tweak it (the same way that TinyMCE would).

phpThumbOf Notes
-----------------

phpThumbOf has several bugs and performance issues, and doesn't appear to be actively maintained.  I've created a fork of it, a drop-in replacement called [pThumb](https://github.com/oo12/phpThumbOf).  Among other things, it addresses the following phpThumbOf issues which can affect imageSlim:

* _Image filenames_ – phpThumbOf 1.4.0 has some problems with image names, in particular when Hash Thumbnail Names in System Settings is set to Off, the default.  Images ending in .jpg lose the last character of their name when the ```f=jpeg``` option is used (Issue [#53](https://github.com/splittingred/phpThumbOf/issues/53)).  imageSlim frequently uses this option and if you have an an image with a 1-charater file name (ex. 1.jpg) the image won't display because it's become ```.[hash].jpeg```. Also if multiple images with the same name (in different directories) on the same page are called with the same phpThumbOf options, phpThumbOf will mistakenly use the first image for all the rest (Issue [#48](https://github.com/splittingred/phpThumbOf/issues/48)).  And if you have the exact same image used on different pages with the same phpThumbOf options it'll create separate files for each one, which means more work for the server and interferes with caching in visitors' browsers (Issue [#44](https://github.com/splittingred/phpThumbOf/issues/44)).
* _Cache_ – Clearing the site cache (via Site > Clear Cache in the Manager) will remove *all* the images in the phpThumbOf cache, meaning everything has to be rebuilt. If you don't want this to happen, disable the phpThumbOfCacheManager plugin. You can always clean out the cache manually.
* _Cache cleanup_ - phpThumbOf attempts to clean up its cache directory every time it's called, so if you have a page with 30 images it'll run its cleanup method 30 times while the page is being built, and that can start to affect performance, especially when the cache has a lot of files.  Furthermore because of a conflict with the core phpThumb class, phpThumbOf's cache cleanup never actually deletes any files, only wastes time (Issue [#54](https://github.com/splittingred/phpThumbOf/issues/54)). Besides switching to pThumb, another way to disable this is to go to System Settings and under core > phpThumb set Max Cache Age, Max Cache Files, and Max Cache Size (three settings) all to 0.

[![githalytics.com alpha](https://cruel-carlota.pagodabox.com/62b82f55f4fa8e341951547aad88c15d "githalytics.com")](http://githalytics.com/oo12/imageSlim)
