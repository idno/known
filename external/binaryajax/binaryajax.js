
/*
 * Binary Ajax 0.2
 * Copyright (c) 2008 Jacob Seidelin, cupboy@gmail.com, http://blog.nihilogic.dk/
 * Licensed under the MPL License [http://www.nihilogic.dk/licenses/mpl-license.txt]
 */


var BinaryFile = function(data, dataOffset, dataLength) {
	var dataOffset = dataOffset || 0;
	var dataLength = 0;

	this.getRawData = function() {
		return data;
	}

	if (typeof data == "string") {
		dataLength = dataLength || data.length;

		this.getByteAt = function(offset) {
			return data.charCodeAt(offset + dataOffset) & 0xFF;
		}
	} else if (typeof data == "unknown") {
		dataLength = dataLength || IEBinary_getLength(data);

		this.getByteAt = function(offset) {
			return IEBinary_getByteAt(data, offset + dataOffset);
		}
	} else {

	}

	this.getLength = function() {
		return dataLength;
	}

	this.getSByteAt = function(offset) {
		var byte = this.getByteAt(offset);
		if (byte > 127)
			return byte - 256;
		else
			return byte;
	}

	this.getShortAt = function(offset, bigEndian) {
		var short = bigEndian ? 
			(this.getByteAt(offset) << 8) + this.getByteAt(offset + 1)
			: (this.getByteAt(offset + 1) << 8) + this.getByteAt(offset)
		if (short < 0) short += 65536;
		return short;
	}
	this.getSShortAt = function(offset, bigEndian) {
		var ushort = this.getShortAt(offset, bigEndian);
		if (ushort > 32767)
			return ushort - 65536;
		else
			return ushort;
	}
	this.getLongAt = function(offset, bigEndian) {
		var byte1 = this.getByteAt(offset),
			byte2 = this.getByteAt(offset + 1),
			byte3 = this.getByteAt(offset + 2),
			byte4 = this.getByteAt(offset + 3);

		var long = bigEndian ? 
			(((((byte1 << 8) + byte2) << 8) + byte3) << 8) + byte4
			: (((((byte4 << 8) + byte3) << 8) + byte2) << 8) + byte1;
		if (long < 0) long += 4294967296;
		return long;
	}
	this.getSLongAt = function(offset, bigEndian) {
		var ulong = this.getLongAt(offset, bigEndian);
		if (ulong > 2147483647)
			return ulong - 4294967296;
		else
			return ulong;
	}
	this.getStringAt = function(offset, length) {
		var chars = [];
		for (var i=offset,j=0;i<offset+length;i++,j++) {
			chars[j] = String.fromCharCode(this.getByteAt(i));
		}
		return chars.join("");
	}

	this.getCharAt = function(offset) {
		return String.fromCharCode(this.getByteAt(offset));
	}
	this.toBase64 = function() {
		return window.btoa(data);
	}
	this.fromBase64 = function(str) {
		data = window.atob(str);
	}
}


var BinaryAjax = (function() {

	function createRequest() {
		var http = null;
		if (window.XMLHttpRequest) {
			http = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			http = new ActiveXObject("Microsoft.XMLHTTP");
		}
		return http;
	}

	function getHead(url, callback, error) {
		var http = createRequest();
		if (http) {
			if (callback) {
				if (typeof(http.onload) != "undefined") {
					http.onload = function() {
						if (http.status == "200") {
							callback(this);
						} else {
							if (error) error();
						}
						http = null;
					};
				} else {
					http.onreadystatechange = function() {
						if (http.readyState == 4) {
							if (http.status == "200") {
								callback(this);
							} else {
								if (error) error();
							}
							http = null;
						}
					};
				}
			}
			http.open("HEAD", url, true);
			http.send(null);
		} else {
			if (error) error();
		}
	}

	function sendRequest(url, callback, error, range, acceptRanges, fileSize) {
		var http = createRequest();
		if (http) {

			var dataOffset = 0;
			if (range && !acceptRanges) {
				dataOffset = range[0];
			}
			var dataLen = 0;
			if (range) {
				dataLen = range[1]-range[0]+1;
			}

			if (callback) {
				if (typeof(http.onload) != "undefined") {
					http.onload = function() {
						if (http.status == "200" || http.status == "206" || http.status == "0") {
							http.binaryResponse = new BinaryFile(http.responseText, dataOffset, dataLen);
							http.fileSize = fileSize || http.getResponseHeader("Content-Length");
							callback(http);
						} else {
							if (error) error();
						}
						http = null;
					};
				} else {
					http.onreadystatechange = function() {
						if (http.readyState == 4) {
							if (http.status == "200" || http.status == "206" || http.status == "0") {
								// IE6 craps if we try to extend the XHR object
								var res = {
									status : http.status,
									// IE needs responseBody, Chrome/Safari needs responseText
									binaryResponse : new BinaryFile(http.responseBody || http.responseText, dataOffset, dataLen),
									fileSize : fileSize || http.getResponseHeader("Content-Length")
								};
								callback(res);
							} else {
								if (error) error();
							}
							http = null;
						}
					};
				}
			}
			http.open("GET", url, true);

			if (http.overrideMimeType) http.overrideMimeType('text/plain; charset=x-user-defined');

			if (range && acceptRanges) {
				http.setRequestHeader("Range", "bytes=" + range[0] + "-" + range[1]);
			}

			http.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 1970 00:00:00 GMT");

			http.send(null);
		} else {
			if (error) error();
		}
	}

	return function(url, callback, error, range) {
		if (range) {
			getHead(
				url, 
				function(http) {
					var length = parseInt(http.getResponseHeader("Content-Length"),10);
					var acceptRanges = http.getResponseHeader("Accept-Ranges");

					var start, end;
					start = range[0];
					if (range[0] < 0) 
						start += length;
					end = start + range[1] - 1;

					sendRequest(url, callback, error, [start, end], (acceptRanges == "bytes"), length);
				}
			);
		} else {
			sendRequest(url, callback, error);
		}
	}

}());


document.write(
	"<script type='text/vbscript'>\r\n"
	+ "Function IEBinary_getByteAt(strBinary, offset)\r\n"
	+ "	IEBinary_getByteAt = AscB(MidB(strBinary,offset+1,1))\r\n"
	+ "End Function\r\n"
	+ "Function IEBinary_getLength(strBinary)\r\n"
	+ "	IEBinary_getLength = LenB(strBinary)\r\n"
	+ "End Function\r\n"
	+ "</script>\r\n"
);
