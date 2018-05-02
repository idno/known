/**
 * Image manipulation and exif functions
 * 
 * IMPORTANT:
 * This file isn't loaded directly, for changes to show you must generate a minified
 * version. E.g.
 *
 *   yui-compressor image.js > image.min.js
 */

"use strict";

function ImageTools() {}

/**
 * Convert base 64 encoded data into an array/image buffer.
 * From: https://stackoverflow.com/questions/24010310/using-exif-and-binaryfile-get-an-error
 */
ImageTools.base64ToArrayBuffer = function(base64) {
    base64 = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
    var binaryString = atob(base64);
    var len = binaryString.length;
    var bytes = new Uint8Array(len);
    for (var i = 0; i < len; i++) {
	bytes[i] = binaryString.charCodeAt(i);
    }
    return bytes.buffer;
}

/**
 * Wrapper.
 * @description Use Image.base64ToArrayBuffer
 */
function base64ToArrayBuffer(base64) { return ImageTools.base64ToArrayBuffer(base64); }


/**
 * Transform an img ID based on the passed exif orientation.
 * @param string imgid ID of the image to rotate
 * @param exif.Orientation exif_orientation The orientation data from exif
 * @param string containerdiv the containing div
 * @returns {undefined}
 */
ImageTools.exifRotateImg = function(imgid, exif_orientation, containerdiv) {
    
    var h = $(imgid).height();
    var w = $(imgid).width();
    
    if (w == 0) w = 300;
    if (h == 0) h = 200;
    
    switch(exif_orientation){

	case 8:
	    var angle = -90;
	    
	    $(imgid).css('transform-origin', '0 0');
	    $(imgid).css('transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('margin-left', '100%');
	    //$(containerdiv).css("width",h+"px");
	    $(containerdiv).css("width",w+"px");
	    $(containerdiv).css("height",w+"px");
	    break;
	case 3:
	    var angle = 180;
	    $(imgid).css('transform-origin', '0 0');
	    $(imgid).css('transform', 'rotate(' + angle + 'deg)');
	    break;
	case 6:
	    var angle = 90;
	    $(imgid).css('transform-origin', '0 0');
	    $(imgid).css('margin-left', '100%');
	    $(imgid).css('transform', 'rotate(' + angle + 'deg)');
	    //$(containerdiv).css("width",h+"px");
	    $(containerdiv).css("width",w+"px");
	    $(containerdiv).css("height",w+"px");
	    break;
     }
}

/**
 * Wrapper.
 * @@description Use Image.exifRotateImg
 */
function exifRotateImg(imgid, exif_orientation, containerdiv) { ImageTools.exifRotateImg(imgid, exif_orientation, containerdiv); }