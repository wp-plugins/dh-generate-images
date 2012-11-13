=== DH Generate Images ===
Contributors: vidhill
Donate link: 
Tags: Custom Image Size, Featured Image, Thumbnail
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate or Re-generate registered Custom Image Sizes including default image sizes and thumbnails.

== Description ==

Generate or Re-generate registered Custom Image Sizes including default image sizes and thumbnails.

The most common usage for this plugin would be in the case where a new Custom Image size has been added after images have been uploaded to the media library. In this case WordPress does not generate the custom image sizes for existing images. This plugin does.

Also if the settings for Thumbnail, Medium or Large images are changed this plugin can be use regenerate thumbnails etc.

== Installation ==

1. Upload `dh-gen-images` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. A 'DH Generate Images' will be added to the Media menu.

== Frequently asked questions ==

Why did the plugin not generate some images?
The most common reason for an image generation failure is a source image being smaller than the image to be generated.. 
e.g. Original image is 100px x 100px and custom image size is 200px x 200px, In this case the plugin will not generate an image


== Screenshots ==

1. Generate Images Screen

== Changelog ==

Version 1.0 First release

== Upgrade notice ==

Version 1.0