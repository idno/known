
var ImageTools = ImageTools || {};

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
};

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
    var angle;
    
    if (w == 0) w = 300;
    if (h == 0) h = 200;
    
    switch(exif_orientation){

	case 8:
	    angle = -90;
	    $(imgid).css('transform-box', 'fill-box');
	    $(imgid).css('transform-origin', '0 0');
	    $(imgid).css('transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('-webkit-transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('-ms-transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('margin-left', '100%');
	    //$(containerdiv).css("width",h+"px");
	    $(containerdiv).css("width",w+"px");
	    $(containerdiv).css("height",w+"px");
	    break;
	case 3:
	    angle = 180;
	    $(imgid).css('transform-box', 'fill-box');
	    $(imgid).css('transform-origin', '0 0');
	    $(imgid).css('transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('-webkit-transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('-ms-transform', 'rotate(' + angle + 'deg)');
	    break;
	case 6:
	    angle = 90;
	    $(imgid).css('transform-origin', '0 0');
	    $(imgid).css('transform-box', 'fill-box');
	    $(imgid).css('margin-left', '100%');
	    $(imgid).css('transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('-webkit-transform', 'rotate(' + angle + 'deg)');
	    $(imgid).css('-ms-transform', 'rotate(' + angle + 'deg)');
	    //$(containerdiv).css("width",h+"px");
	    $(containerdiv).css("width",w+"px");
	    $(containerdiv).css("height",w+"px");
	    break;
     }
};

