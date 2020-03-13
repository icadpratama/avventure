/**
 * Full Background Video
 *
 * More info on Audio/Video Media Events/Attributes/Methods
 * - https://developer.mozilla.org/en-US/docs/Web/Guide/Events/Media_events
 * - http://www.w3schools.com/tags/ref_av_dom.asp
 */

(function (global) {
  "use strict";

  // Define Bideo constructor on the global object
  global.Bideo = function () {

    // Plugin options
    this.opt = null;
    // The Video element
    this.videoEl = null;

    // Approximate Loading Rate
    //
    // The value will be a number like 0.8
    // which means to load 4 seconds of the video
    // it takes 5 seconds. If the number is super low
    // like 0.2 (regular 3g connections) then you can
    // decide whether to play the video or not.
    // This behaviour will be controller with
    // the `acceptableLoadingRate` option.
    this.approxLoadingRate = null;

    // Methods to which `this` will be bound
    this._resize = null;
    this._progress = null;

    // Time at which video is initialized
    this.startTime = null;

    this.onLoadCalled = false;

    // Initialize and setup the video in DOM`
    this.init = function (opt) {
      // If not set then set to an empty object
      this.opt = opt = opt || {};

      var self = this;

      self._resize = self.resize.bind(this);

      // Video element
      self.videoEl = opt.videoEl;

      // Meta data event
      self.videoEl.addEventListener('loadedmetadata', self._resize, false);

      // Fired when enough has been buffered to begin the video
      // self.videoEl.readyState === 4 (HAVE_ENOUGH_DATA)
      self.videoEl.addEventListener('canplay', function () {
        // Play the video when enough has been buffered
        if (!self.opt.isMobile) {
          self.opt.onLoad && self.opt.onLoad();
          if (self.opt.autoplay !== false) self.videoEl.play();
        }
      });

      // If resizing is required (resize video as window/container resizes)
      if (self.opt.resize) {
        global.addEventListener('resize', self._resize, false);
      }

      // Start time of video initialization
      this.startTime = (new Date()).getTime();

      // Create `source` for video
      this.opt.src.forEach(function (srcOb, i, arr) {
        var key
          , val
          , source = document.createElement('source');

        // Set all the attribute key=val supplied in `src` option
        for (key in srcOb) {
          if (srcOb.hasOwnProperty(key)) {
            val = srcOb[key];

            source.setAttribute(key, val);
          }
        }

        self.videoEl.appendChild(source);
      });

      if (self.opt.isMobile) {
        if (self.opt.playButton) {
          self.opt.videoEl.addEventListener('timeupdate', function () {
            if (!self.onLoadCalled) {
              self.opt.onLoad && self.opt.onLoad();
              self.onLoadCalled = true;
            }
          });


          self.opt.playButton.addEventListener('click', function () {
            self.opt.pauseButton.style.display = 'inline-block';
            this.style.display = 'none';

            self.videoEl.play();
          }, false);

          self.opt.pauseButton.addEventListener('click', function () {
            this.style.display = 'none';
            self.opt.playButton.style.display = 'inline-block';

            self.videoEl.pause();
          }, false);
        }
      }

      return;
    };

    // Called once video metadata is available
    //
    // Also called when window/container is resized
    this.resize = function () {
      // IE/Edge still don't support object-fit: cover
      if ('object-fit' in document.body.style) return;

      // Video's intrinsic dimensions
      var w = this.videoEl.videoWidth
        , h = this.videoEl.videoHeight;

      // Intrinsic ratio
      // Will be more than 1 if W > H and less if H > W
      var videoRatio = (w / h).toFixed(2);

      // Get the container DOM element and its styles
      //
      // Also calculate the min dimensions required (this will be
      // the container dimentions)
      var container = this.opt.container
        , containerStyles = global.getComputedStyle(container)
        , minW = parseInt( containerStyles.getPropertyValue('width') )
        , minH = parseInt( containerStyles.getPropertyValue('height') );

      // If !border-box then add paddings to width and height
      if (containerStyles.getPropertyValue('box-sizing') !== 'border-box') {
        var paddingTop = containerStyles.getPropertyValue('padding-top')
          , paddingBottom = containerStyles.getPropertyValue('padding-bottom')
          , paddingLeft = containerStyles.getPropertyValue('padding-left')
          , paddingRight = containerStyles.getPropertyValue('padding-right');

        paddingTop = parseInt(paddingTop);
        paddingBottom = parseInt(paddingBottom);
        paddingLeft = parseInt(paddingLeft);
        paddingRight = parseInt(paddingRight);

        minW += paddingLeft + paddingRight;
        minH += paddingTop + paddingBottom;
      }

      // What's the min:intrinsic dimensions
      //
      // The idea is to get which of the container dimension
      // has a higher value when compared with the equivalents
      // of the video. Imagine a 1200x700 container and
      // 1000x500 video. Then in order to find the right balance
      // and do minimum scaling, we have to find the dimension
      // with higher ratio.
      //
      // Ex: 1200/1000 = 1.2 and 700/500 = 1.4 - So it is best to
      // scale 500 to 700 and then calculate what should be the
      // right width. If we scale 1000 to 1200 then the height
      // will become 600 proportionately.
      var widthRatio = minW / w;
      var heightRatio = minH / h;

      // Whichever ratio is more, the scaling
      // has to be done over that dimension
      if (widthRatio > heightRatio) {
        var new_width = minW;
        var new_height = Math.ceil( new_width / videoRatio );
      } else {
        var new_height = minH;
        var new_width = Math.ceil( new_height * videoRatio );
      }

      this.videoEl.style.width = new_width + 'px';
      this.videoEl.style.height = new_height + 'px';
    };

  };

}(window));/* jQuery tubular plugin
|* by Sean McCambridge
|* http://www.seanmccambridge.com/tubular
|* version: 1.0
|* updated: October 1, 2012
|* since 2010
|* licensed under the MIT License
|* Enjoy.
|* 
|* Thanks,
|* Sean */

;(function ($, window) {

    // test for feature support and return if failure
    
    // defaults
    var defaults = {
        ratio: 16/9, // usually either 4/3 or 16/9 -- tweak as needed
        videoId: 'ZCAnLxRvNNc', // toy robot in space is a good default, no?
        mute: true,
        repeat: true,
        width: $(window).width(),
        wrapperZIndex: 99,
        playButtonClass: 'tubular-play',
        pauseButtonClass: 'tubular-pause',
        muteButtonClass: 'tubular-mute',
        volumeUpClass: 'tubular-volume-up',
        volumeDownClass: 'tubular-volume-down',
        increaseVolumeBy: 10,
        start: 0
    };

    // methods

    var tubular = function(node, options) { // should be called on the wrapper div
        var options = $.extend({}, defaults, options),
            $body = $('body'), // cache body node
            $node = $(node); // cache wrapper node

        // build container
        var tubularContainer = '<div id="tubular-container" style="overflow: hidden; position: fixed; z-index: 1; width: 100%; height: 100%"><div id="tubular-player" style="position: absolute"></div></div><div id="tubular-shield" style="width: 100%; height: 100%; z-index: 2; position: absolute; left: 0; top: 0;"></div>';

        // set up css prereq's, inject tubular container and set up wrapper defaults
        $('html,body').css({'width': '100%', 'height': '100%'});
        $body.prepend(tubularContainer);
        $node.css({position: 'relative', 'z-index': options.wrapperZIndex});

        // set up iframe player, use global scope so YT api can talk
        window.player;
        window.onYouTubeIframeAPIReady = function() {
            player = new YT.Player('tubular-player', {
                width: options.width,
                height: Math.ceil(options.width / options.ratio),
                videoId: options.videoId,
                playerVars: {
                    controls: 0,
                    showinfo: 0,
                    modestbranding: 1,
                    wmode: 'transparent'
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        };

        window.onPlayerReady = function(e) {
            resize();
            if (options.mute) e.target.mute();
            e.target.seekTo(options.start);
            e.target.playVideo();
        };

        window.onPlayerStateChange = function(state) {
            if (state.data === 0 && options.repeat) { // video ended and repeat option is set true
                player.seekTo(options.start); // restart
            }
        };

        // resize handler updates width, height and offset of player after resize/init
        var resize = function() {
            var width = $(window).width(),
                pWidth, // player width, to be defined
                height = $(window).height(),
                pHeight, // player height, tbd
                $tubularPlayer = $('#tubular-player');

            // when screen aspect ratio differs from video, video must center and underlay one dimension

            if (width / options.ratio < height) { // if new video height < window height (gap underneath)
                pWidth = Math.ceil(height * options.ratio); // get new player width
                $tubularPlayer.width(pWidth).height(height).css({left: (width - pWidth) / 2, top: 0}); // player width is greater, offset left; reset top
            } else { // new video width < window width (gap to right)
                pHeight = Math.ceil(width / options.ratio); // get new player height
                $tubularPlayer.width(width).height(pHeight).css({left: 0, top: (height - pHeight) / 2}); // player height is greater, offset top; reset left
            }

        };

        // events
        $(window).on('resize.tubular', function() {
            resize();
        });

        $('body').on('click','.' + options.playButtonClass, function(e) { // play button
            e.preventDefault();
            player.playVideo();
        }).on('click', '.' + options.pauseButtonClass, function(e) { // pause button
            e.preventDefault();
            player.pauseVideo();
        }).on('click', '.' + options.muteButtonClass, function(e) { // mute button
            e.preventDefault();
            (player.isMuted()) ? player.unMute() : player.mute();
        }).on('click', '.' + options.volumeDownClass, function(e) { // volume down button
            e.preventDefault();
            var currentVolume = player.getVolume();
            if (currentVolume < options.increaseVolumeBy) currentVolume = options.increaseVolumeBy;
            player.setVolume(currentVolume - options.increaseVolumeBy);
        }).on('click', '.' + options.volumeUpClass, function(e) { // volume up button
            e.preventDefault();
            if (player.isMuted()) player.unMute(); // if mute is on, unmute
            var currentVolume = player.getVolume();
            if (currentVolume > 100 - options.increaseVolumeBy) currentVolume = 100 - options.increaseVolumeBy;
            player.setVolume(currentVolume + options.increaseVolumeBy);
        });
    };

    // load yt iframe js api

    var tag = document.createElement('script');
    tag.src = "//www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // create plugin

    $.fn.tubular = function (options) {
        return this.each(function () {
            if (!$.data(this, 'tubular_instantiated')) { // let's only run one
                $.data(this, 'tubular_instantiated', tubular(this, options));
            }
        });
    };

})(jQuery, window);/**
 * Javascript utilities
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

/* global jQuery:false */
/* global AVVENTURE_STORAGE:false */

/* Global variables manipulations
---------------------------------------------------------------- */

(function(){
	"use strict";

	// Global variables storage
	if (typeof AVVENTURE_STORAGE == 'undefined') {
		window.AVVENTURE_STORAGE = {};
	}

	// Get global variable
	window.avventure_storage_get = function(var_name) {
		return avventure_isset( AVVENTURE_STORAGE[var_name] ) ? AVVENTURE_STORAGE[var_name] : '';
	};

	// Set global variable
	window.avventure_storage_set = function(var_name, value) {
		AVVENTURE_STORAGE[var_name] = value;
	};

	// Inc/Dec global variable with specified value
	window.avventure_storage_inc = function(var_name) {
		var value                  = arguments[1] === undefined ? 1 : arguments[1];
		AVVENTURE_STORAGE[var_name] += value;
	};

	// Concatenate global variable with specified value
	window.avventure_storage_concat = function(var_name, value) {
		AVVENTURE_STORAGE[var_name] += '' + value;
	};

	// Get global array element
	window.avventure_storage_get_array = function(var_name, key) {
		return avventure_isset( AVVENTURE_STORAGE[var_name][key] ) ? AVVENTURE_STORAGE[var_name][key] : '';
	};

	// Set global array element
	window.avventure_storage_set_array = function(var_name, key, value) {
		if ( ! avventure_isset( AVVENTURE_STORAGE[var_name] )) {
			AVVENTURE_STORAGE[var_name] = {};
		}
		AVVENTURE_STORAGE[var_name][key] = value;
	};

	// Inc/Dec global array element with specified value
	window.avventure_storage_inc_array = function(var_name, key) {
		var value                       = arguments[2] === undefined ? 1 : arguments[2];
		AVVENTURE_STORAGE[var_name][key] += value;
	};

	// Concatenate global array element with specified value
	window.avventure_storage_concat_array = function(var_name, key, value) {
		AVVENTURE_STORAGE[var_name][key] += '' + value;
	};

	/* PHP-style functions
	---------------------------------------------------------------- */
	window.avventure_isset = function(obj) {
		return typeof(obj) != 'undefined';
	};

	window.avventure_empty = function(obj) {
		return typeof(obj) == 'undefined' || (typeof(obj) == 'object' && obj == null) || (typeof(obj) == 'array' && obj.length == 0) || (typeof(obj) == 'string' && avventure_alltrim( obj ) == '') || obj === 0;
	};

	window.avventure_is_array = function(obj)  {
		return typeof(obj) == 'array';
	};

	window.avventure_is_object = function(obj)  {
		return typeof(obj) == 'object';
	};

	window.avventure_clone_object = function(obj) {
		if (obj == null || typeof(obj) != 'object') {
			return obj;
		}
		var temp = {};
		for (var key in obj) {
			temp[key] = avventure_clone_object( obj[key] );
		}
		return temp;
	};

	window.avventure_merge_objects = function(obj1, obj2)  {
		for (var i in obj2) {
			obj1[i] = obj2[i];
		}
		return obj1;
	};

	// Generates a storable representation of a value
	window.avventure_serialize = function(mixed_val) {
		var obj_to_array = arguments.length == 1 || argument[1] === true;

		switch (typeof(mixed_val)) {

			case "number":
				if (isNaN( mixed_val ) || ! isFinite( mixed_val )) {
					return false;
				} else {
					return (Math.floor( mixed_val ) == mixed_val ? "i" : "d") + ":" + mixed_val + ";";
				}

			case "string":
				return "s:" + mixed_val.length + ":\"" + mixed_val + "\";";

			case "boolean":
				return "b:" + (mixed_val ? "1" : "0") + ";";

			case "object":
				if (mixed_val == null) {
					return "N;";
				} else if (mixed_val instanceof Array) {
					var idxobj = { idx: -1 };
					var map    = [];
					for (var i = 0; i < mixed_val.length; i++) {
						idxobj.idx++;
						var ser = avventure_serialize( mixed_val[i] );
						if (ser) {
							map.push( avventure_serialize( idxobj.idx ) + ser );
						}
					}
					return "a:" + mixed_val.length + ":{" + map.join( "" ) + "}";
				} else {
					var class_name = avventure_get_class( mixed_val );
					if (class_name == undefined) {
						return false;
					}
					var props = new Array();
					for (var prop in mixed_val) {
						var ser = avventure_serialize( mixed_val[prop] );
						if (ser) {
							props.push( avventure_serialize( prop ) + ser );
						}
					}
					if (obj_to_array) {
						return "a:" + props.length + ":{" + props.join( "" ) + "}";
					} else {
						return "O:" + class_name.length + ":\"" + class_name + "\":" + props.length + ":{" + props.join( "" ) + "}";
					}
				}

			case "undefined":
				return "N;";
		}
		return false;
	};

	// Returns the name of the class of an object
	window.avventure_get_class = function(obj) {
		if (obj instanceof Object && ! (obj instanceof Array) && ! (obj instanceof Function) && obj.constructor) {
			var arr = obj.constructor.toString().match( /function\s*(\w+)/ );
			if (arr && arr.length == 2) {
				return arr[1];
			}
		}
		return false;
	};

	/* String functions
	---------------------------------------------------------------- */

	window.avventure_in_list = function(str, list) {
		var delim  = arguments[2] !== undefined ? arguments[2] : '|';
		var icase  = arguments[3] !== undefined ? arguments[3] : true;
		var retval = false;
		if (icase) {
			if (typeof(str) == 'string') {
				str = str.toLowerCase();
			}
			list = list.toLowerCase();
		}
		var parts = list.split( delim );
		for (var i = 0; i < parts.length; i++) {
			if (parts[i] == str) {
				retval = true;
				break;
			}
		}
		return retval;
	};

	window.avventure_alltrim = function(str) {
		var dir      = arguments[1] !== undefined ? arguments[1] : 'a';
		var rez      = '';
		var i, start = 0, end = str.length - 1;
		if (dir == 'a' || dir == 'l') {
			for (i = 0; i < str.length; i++) {
				if (str.substr( i,1 ) != ' ') {
					start = i;
					break;
				}
			}
		}
		if (dir == 'a' || dir == 'r') {
			for (i = str.length - 1; i >= 0; i--) {
				if (str.substr( i,1 ) != ' ') {
					end = i;
					break;
				}
			}
		}
		return str.substring( start, end + 1 );
	};

	window.avventure_ltrim = function(str) {
		return avventure_alltrim( str, 'l' );
	};

	window.avventure_rtrim = function(str) {
		return avventure_alltrim( str, 'r' );
	};

	window.avventure_padl = function(str, len) {
		var ch  = arguments[2] !== undefined ? arguments[2] : ' ';
		var rez = str.substr( 0,len );
		if (rez.length < len) {
			for (var i = 0; i < len - str.length; i++) {
				rez += ch;
			}
		}
		return rez;
	};

	window.avventure_padr = function(str, len) {
		var ch  = arguments[2] !== undefined ? arguments[2] : ' ';
		var rez = str.substr( 0,len );
		if (rez.length < len) {
			for (var i = 0; i < len - str.length; i++) {
				rez = ch + rez;
			}
		}
		return rez;
	};

	window.avventure_padc = function(str, len) {
		var ch  = arguments[2] !== undefined ? arguments[2] : ' ';
		var rez = str.substr( 0,len );
		if (rez.length < len) {
			for (var i = 0; i < Math.floor( (len - str.length) / 2 ); i++) {
				rez = ch + rez + ch;
			}
		}
		return rez + (rez.length < len ? ch : '');
	};

	window.avventure_replicate = function(str, num) {
		var rez = '';
		for (var i = 0; i < num; i++) {
			rez += str;
		}
		return rez;
	};

	window.avventure_prepare_macros = function(str) {
		return str
			.replace( /\{\{/g, "<i>" )
			.replace( /\}\}/g, "</i>" )
			.replace( /\(\(/g, "<b>" )
			.replace( /\)\)/g, "</b>" )
			.replace( /\|\|/g, "<br>" );
	};

	/* Numbers functions
	---------------------------------------------------------------- */

	// Round number to specified precision.
	// For example: num=1.12345, prec=2,  rounded=1.12
	//              num=12345,   prec=-2, rounded=12300
	window.avventure_round_number = function(num) {
		var precision = arguments[1] !== undefined ? arguments[1] : 0;
		var p         = Math.pow( 10, precision );
		return Math.round( num * p ) / p;
	};

	// Clear number from any characters and append it with 0 to desired precision
	// For example: num=test1.12dd, prec=3, cleared=1.120
	window.avventure_clear_number = function(num) {
		var precision = arguments[1] !== undefined ? arguments[1] : 0;
		var defa      = arguments[2] !== undefined ? arguments[2] : 0;
		var res       = '';
		var decimals  = -1;
		num           = "" + num;
		if (num == "") {
			num = "" + defa;
		}
		for (var i = 0; i < num.length; i++) {
			if (decimals == 0) {
				break;
			} else if (decimals > 0) {
				decimals--;
			}
			var ch = num.substr( i,1 );
			if (ch == '.') {
				if (precision > 0) {
					res += ch;
				}
				decimals = precision;
			} else if ((ch >= 0 && ch <= 9) || (ch == '-' && i == 0)) {
				res += ch;
			}
		}
		if (precision > 0 && decimals != 0) {
			if (decimals == -1) {
				res     += '.';
				decimals = precision;
			}
			for (i = decimals; i > 0; i--) {
				res += '0';
			}
		}
		//if (isNaN(res)) res = clearNumber(defa, precision, defa);
		return res;
	};

	// Convert number from decimal to hex
	window.avventure_dec2hex = function(n) {
		return Number( n ).toString( 16 );
	};

	// Convert number from hex to decimal
	window.avventure_hex2dec = function(hex) {
		return parseInt( hex,16 );
	};

	/* Array manipulations
	---------------------------------------------------------------- */

	window.avventure_in_array = function(val, thearray)  {
		var rez = false;
		for (var i = 0; i < thearray.length - 1; i++) {
			if (thearray[i] == val) {
				rez = true;
				break;
			}
		}
		return rez;
	};

	window.avventure_sort_array = function(thearray)  {
		var caseSensitive = arguments[1] !== undefined ? arguments[1] : false;
		var tmp           = '';
		for (var x = 0; x < thearray.length - 1; x++) {
			for (var y = (x + 1); y < thearray.length; y++) {
				if (caseSensitive) {
					if (thearray[x] > thearray[y]) {
						tmp         = thearray[x];
						thearray[x] = thearray[y];
						thearray[y] = tmp;
					}
				} else {
					if (thearray[x].toLowerCase() > thearray[y].toLowerCase()) {
						tmp         = thearray[x];
						thearray[x] = thearray[y];
						thearray[y] = tmp;
					}
				}
			}
		}
		return thearray;
	};

	/* Date manipulations
	---------------------------------------------------------------- */

	// Return array[Year, Month, Day, Hours, Minutes, Seconds]
	// from string: Year[-/.]Month[-/.]Day[T ]Hours:Minutes:Seconds
	window.avventure_parse_date = function(dt) {
		dt      = dt.replace( /\//g, '-' ).replace( /\./g, '-' ).replace( /T/g, ' ' ).split( '+' )[0];
		var dt2 = dt.split( ' ' );
		var d   = dt2[0].split( '-' );
		var t   = dt2[1].split( ':' );
		d.push( t[0], t[1], t[2] );
		return d;
	};

	// Return difference string between two dates
	window.avventure_get_date_difference = function(dt1) {
		var dt2        = arguments[1] !== undefined ? arguments[1] : '';
		var short_date = arguments[2] !== undefined ? arguments[2] : true;
		var sec        = arguments[3] !== undefined ? arguments[3] : false;
		var a1         = avventure_parse_date( dt1 );
		dt1            = Date.UTC( a1[0], a1[1], a1[2], a1[3], a1[4], a1[5] );
		if (dt2 == '') {
			dt2    = new Date();
			var a2 = [dt2.getFullYear(), dt2.getMonth() + 1, dt2.getDate(), dt2.getHours(), dt2.getMinutes(), dt2.getSeconds()];
		} else {
			var a2 = avventure_parse_date( dt2 );
		}
		dt2         = Date.UTC( a2[0], a2[1], a2[2], a2[3], a2[4], a2[5] );
		var diff    = Math.round( (dt2 - dt1) / 1000 );
		var days    = Math.floor( diff / (24 * 3600) );
		diff       -= days * 24 * 3600;
		var hours   = Math.floor( diff / 3600 );
		diff       -= hours * 3600;
		var minutes = Math.floor( diff / 60 );
		diff       -= minutes * 60;
		var rez     = '';
		if (days > 0) {
			rez += (rez !== '' ? ' ' : '') + days + ' day' + (days > 1 ? 's' : '');
		}
		if (( ! short_date || rez == '') && hours > 0) {
			rez += (rez !== '' ? ' ' : '') + hours + ' hour' + (hours > 1 ? 's' : '');
		}
		if (( ! short_date || rez == '') && minutes > 0) {
			rez += (rez !== '' ? ' ' : '') + minutes + ' minute' + (minutes > 1 ? 's' : '');
		}
		if (sec || rez == '') {
			rez += rez !== '' || sec ? (' ' + diff + ' second' + (diff > 1 ? 's' : '')) : 'less then minute';
		}
		return rez;
	};

	/* Colors functions
	---------------------------------------------------------------- */

	window.avventure_hex2rgb = function(hex) {
		hex = parseInt( ((hex.indexOf( '#' ) > -1) ? hex.substring( 1 ) : hex), 16 );
		return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
	};

	window.avventure_hex2rgba = function(hex, alpha) {
		var rgb = avventure_hex2rgb( hex );
		return 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + alpha + ')';
	};

	window.avventure_rgb2hex = function(color) {
		var aRGB;
		color = color.replace( /\s/g,"" ).toLowerCase();
		if (color == 'rgba(0,0,0,0)' || color == 'rgba(0%,0%,0%,0%)') {
			color = 'transparent';
		}
		if (color.indexOf( 'rgba(' ) == 0) {
			aRGB = color.match( /^rgba\((\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?)\)$/i );
		} else {
			aRGB = color.match( /^rgb\((\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?)\)$/i );
		}

		if (aRGB) {
			color = '';
			for (var i = 1; i <= 3; i++) {
				color += Math.round( (aRGB[i][aRGB[i].length - 1] == "%" ? 2.55 : 1) * parseInt( aRGB[i] ) ).toString( 16 ).replace( /^(.)$/,'0$1' );
			}
		} else {
			color = color.replace( /^#?([\da-f])([\da-f])([\da-f])$/i, '$1$1$2$2$3$3' );
		}
		return (color.substr( 0,1 ) != '#' ? '#' : '') + color;
	};

	window.avventure_components2hex = function(r,g,b) {
		return '#' +
			Number( r ).toString( 16 ).toUpperCase().replace( /^(.)$/,'0$1' ) +
			Number( g ).toString( 16 ).toUpperCase().replace( /^(.)$/,'0$1' ) +
			Number( b ).toString( 16 ).toUpperCase().replace( /^(.)$/,'0$1' );
	};

	window.avventure_rgb2components = function(color) {
		color       = avventure_rgb2hex( color );
		var matches = color.match( /^#?([\dabcdef]{2})([\dabcdef]{2})([\dabcdef]{2})$/i );
		if ( ! matches) {
			return false;
		}
		for (var i = 1, rgb = new Array( 3 ); i <= 3; i++) {
			rgb[i - 1] = parseInt( matches[i],16 );
		}
		return rgb;
	};

	window.avventure_hex2hsb = function(hex) {
		var h   = arguments[1] !== undefined ? arguments[1] : 0;
		var s   = arguments[2] !== undefined ? arguments[2] : 0;
		var b   = arguments[3] !== undefined ? arguments[3] : 0;
		var hsb = avventure_rgb2hsb( avventure_hex2rgb( hex ) );
		hsb.h   = Math.min( 359, hsb.h + h );
		hsb.s   = Math.min( 100, hsb.s + s );
		hsb.b   = Math.min( 100, hsb.b + b );
		return hsb;
	};

	window.avventure_hsb2hex = function(hsb) {
		var rgb = avventure_hsb2rgb( hsb );
		return avventure_components2hex( rgb.r, rgb.g, rgb.b );
	};

	window.avventure_rgb2hsb = function(rgb) {
		var hsb = {};
		hsb.b   = Math.max( Math.max( rgb.r,rgb.g ),rgb.b );
		hsb.s   = (hsb.b <= 0) ? 0 : Math.round( 100 * (hsb.b - Math.min( Math.min( rgb.r,rgb.g ),rgb.b )) / hsb.b );
		hsb.b   = Math.round( (hsb.b / 255) * 100 );
		if ((rgb.r == rgb.g) && (rgb.g == rgb.b)) {
			hsb.h = 0;
		} else if (rgb.r >= rgb.g && rgb.g >= rgb.b) {
			hsb.h = 60 * (rgb.g - rgb.b) / (rgb.r - rgb.b);
		} else if (rgb.g >= rgb.r && rgb.r >= rgb.b) {
			hsb.h = 60 + 60 * (rgb.g - rgb.r) / (rgb.g - rgb.b);
		} else if (rgb.g >= rgb.b && rgb.b >= rgb.r) {
			hsb.h = 120 + 60 * (rgb.b - rgb.r) / (rgb.g - rgb.r);
		} else if (rgb.b >= rgb.g && rgb.g >= rgb.r) {
			hsb.h = 180 + 60 * (rgb.b - rgb.g) / (rgb.b - rgb.r);
		} else if (rgb.b >= rgb.r && rgb.r >= rgb.g) {
			hsb.h = 240 + 60 * (rgb.r - rgb.g) / (rgb.b - rgb.g);
		} else if (rgb.r >= rgb.b && rgb.b >= rgb.g) {
			hsb.h = 300 + 60 * (rgb.r - rgb.b) / (rgb.r - rgb.g);
		} else {
			hsb.h = 0;
		}
		hsb.h = Math.round( hsb.h );
		return hsb;
	};

	window.avventure_hsb2rgb = function(hsb) {
		var rgb = {};
		var h   = Math.round( hsb.h );
		var s   = Math.round( hsb.s * 255 / 100 );
		var v   = Math.round( hsb.b * 255 / 100 );
		if (s == 0) {
			rgb.r = rgb.g = rgb.b = v;
		} else {
			var t1 = v;
			var t2 = (255 - s) * v / 255;
			var t3 = (t1 - t2) * (h % 60) / 60;
			if (h == 360) {
				h = 0;
			}
			if (h < 60) {
				rgb.r = t1;	rgb.b = t2;   rgb.g = t2 + t3; } else if (h < 120) {
				rgb.g = t1; rgb.b = t2;	rgb.r = t1 - t3; } else if (h < 180) {
					rgb.g = t1; rgb.r = t2;	rgb.b = t2 + t3; } else if (h < 240) {
					rgb.b = t1; rgb.r = t2;	rgb.g = t1 - t3; } else if (h < 300) {
							rgb.b = t1; rgb.g = t2;	rgb.r = t2 + t3; } else if (h < 360) {
							rgb.r = t1; rgb.g = t2;	rgb.b = t1 - t3; } else {
								rgb.r = 0;  rgb.g = 0;	rgb.b = 0;	 }
		}
		return { r:Math.round( rgb.r ), g:Math.round( rgb.g ), b:Math.round( rgb.b ) };
	};

	window.avventure_color_picker = function(){
		var id         = arguments[0] !== undefined ? arguments[0] : "iColorPicker" + Math.round( Math.random() * 1000 );
		var colors     = arguments[1] !== undefined ? arguments[1] :
		'#f00,#ff0,#0f0,#0ff,#00f,#f0f,#fff,#ebebeb,#e1e1e1,#d7d7d7,#cccccc,#c2c2c2,#b7b7b7,#acacac,#a0a0a0,#959595,'
		+ '#ee1d24,#fff100,#00a650,#00aeef,#2f3192,#ed008c,#898989,#7d7d7d,#707070,#626262,#555,#464646,#363636,#262626,#111,#000,'
		+ '#f7977a,#fbad82,#fdc68c,#fff799,#c6df9c,#a4d49d,#81ca9d,#7bcdc9,#6ccff7,#7ca6d8,#8293ca,#8881be,#a286bd,#bc8cbf,#f49bc1,#f5999d,'
		+ '#f16c4d,#f68e54,#fbaf5a,#fff467,#acd372,#7dc473,#39b778,#16bcb4,#00bff3,#438ccb,#5573b7,#5e5ca7,#855fa8,#a763a9,#ef6ea8,#f16d7e,'
		+ '#ee1d24,#f16522,#f7941d,#fff100,#8fc63d,#37b44a,#00a650,#00a99e,#00aeef,#0072bc,#0054a5,#2f3192,#652c91,#91278f,#ed008c,#ee105a,'
		+ '#9d0a0f,#a1410d,#a36209,#aba000,#588528,#197b30,#007236,#00736a,#0076a4,#004a80,#003370,#1d1363,#450e61,#62055f,#9e005c,#9d0039,'
		+ '#790000,#7b3000,#7c4900,#827a00,#3e6617,#045f20,#005824,#005951,#005b7e,#003562,#002056,#0c004b,#30004a,#4b0048,#7a0045,#7a0026';
		var colorsList = colors.split( ',' );
		var tbl        = '<table class="colorPickerTable"><thead>';
		for (var i = 0; i < colorsList.length; i++) {
			if (i % 16 == 0) {
				tbl += (i > 0 ? '</tr>' : '') + '<tr>';
			}
			tbl += '<td style="background-color:' + colorsList[i] + '">&nbsp;</td>';
		}
		tbl += '</tr></thead><tbody>'
			+ '<tr class="height_60">'
			+ '<td colspan="8" id="' + id + '_colorPreview" class="colorpicker_td_extra_style">'
			+ '<input class="colorpicker_input_extra_style" maxlength="7" />'
			+ '<a href="#" id="' + id + '_moreColors" class="iColorPicker_moreColors"></a>'
			+ '</td>'
			+ '<td colspan="8" id="' + id + '_colorOriginal" class="colorpicker_td_extra_style">'
			+ '<input class="colorpicker_input_extra_style" readonly="readonly" />'
			+ '</td>'
			+ '</tr></tbody></table>';

		jQuery( document.createElement( "div" ) )
			.attr( "id", id )
			.css( 'display','none' )
			.html( tbl )
			.appendTo( "body" )
			.addClass( "iColorPickerTable" )
			.on(
				'mouseover', 'thead td', function(){
					var aaa = avventure_rgb2hex( jQuery( this ).css( 'background-color' ) );
					jQuery( '#' + id + '_colorPreview' ).css( 'background',aaa );
					jQuery( '#' + id + '_colorPreview input' ).val( aaa );
				}
			)
			.on(
				'keypress', '#' + id + '_colorPreview input', function(key){
					var aaa = jQuery( this ).val();
					if (aaa.length < 7 && ((key.which >= 48 && key.which <= 57) || (key.which >= 97 && key.which <= 102) || (key.which === 35 || aaa.length === 0))) {
						aaa += String.fromCharCode( key.which );
					} else if (key.which == 8 && aaa.length > 0) {
						aaa = aaa.substring( 0, aaa.length - 1 );
					} else if (key.which === 13 && (aaa.length === 4 || aaa.length === 7)) {
						var fld  = jQuery( '#' + id ).data( 'field' );
						var func = jQuery( '#' + id ).data( 'func' );
						if (func != null && func != 'undefined') {
							func( fld, aaa );
						} else {
							fld.val( aaa ).css( 'backgroundColor', aaa ).trigger( 'change' );
						}
						jQuery( '#' + id + '_Bg' ).fadeOut( 500 );
						jQuery( '#' + id ).fadeOut( 500 );

					} else {
						key.preventDefault();
						return false;
					}
					if (aaa.substr( 0,1 ) === '#' && (aaa.length === 4 || aaa.length === 7)) {
						jQuery( '#' + id + '_colorPreview' ).css( 'background',aaa );
					}
				}
			)
			.on(
				'click', 'thead td', function(e){
					var fld  = jQuery( '#' + id ).data( 'field' );
					var func = jQuery( '#' + id ).data( 'func' );
					var aaa  = avventure_rgb2hex( jQuery( this ).css( 'background-color' ) );
					if (func != null && func != 'undefined') {
						func( fld, aaa );
					} else {
						fld.val( aaa ).css( 'backgroundColor', aaa ).trigger( 'change' );
					}
					jQuery( '#' + id + '_Bg' ).fadeOut( 500 );
					jQuery( '#' + id ).fadeOut( 500 );
					e.preventDefault();
					return false;
				}
			)
			.on(
				'click', 'tbody .iColorPicker_moreColors', function(e){
					var thead = jQuery( this ).parents( 'table' ).find( 'thead' );
					var out   = '';
					if (thead.hasClass( 'more_colors' )) {
						for (var i = 0; i < colorsList.length; i++) {
							if (i % 16 == 0) {
								out += (i > 0 ? '</tr>' : '') + '<tr>';
							}
							out += '<td style="background-color:' + colorsList[i] + '">&nbsp;</td>';
						}
						thead.removeClass( 'more_colors' ).empty().html( out + '</tr>' );
						jQuery( '#' + id + '_colorPreview' ).attr( 'colspan', 8 );
						jQuery( '#' + id + '_colorOriginal' ).attr( 'colspan', 8 );
					} else {
						var rgb = [0,0,0], i = 0, j = -1;	// Set j=-1 or j=0 - show 2 different colors layouts
						while (rgb[0] < 0xF || rgb[1] < 0xF || rgb[2] < 0xF) {
							if (i % 18 == 0) {
								out += (i > 0 ? '</tr>' : '') + '<tr>';
							}
							i++;
							out    += '<td style="background-color:' + avventure_components2hex( rgb[0] * 16 + rgb[0],rgb[1] * 16 + rgb[1],rgb[2] * 16 + rgb[2] ) + '">&nbsp;</td>';
							rgb[2] += 3;
							if (rgb[2] > 0xF) {
								rgb[1] += 3;
								if (rgb[1] > (j === 0 ? 6 : 0xF)) {
									rgb[0] += 3;
									if (rgb[0] > 0xF) {
										if (j === 0) {
											j      = 1;
											rgb[0] = 0;
											rgb[1] = 9;
											rgb[2] = 0;
										} else {
											break;
										}
									} else {
										rgb[1] = (j < 1 ? 0 : 9);
										rgb[2] = 0;
									}
								} else {
									rgb[2] = 0;
								}
							}
						}
						thead.addClass( 'more_colors' ).empty().html( out + '<td class="bg_color_white" colspan="8">&nbsp;</td></tr>' );
						jQuery( '#' + id + '_colorPreview' ).attr( 'colspan', 9 );
						jQuery( '#' + id + '_colorOriginal' ).attr( 'colspan', 9 );
					}
					jQuery( '#' + id + ' table.colorPickerTable thead td' )
					.css(
						{
							'width':'12px',
							'height':'14px',
							'border':'1px solid #000',
							'cursor':'pointer'
						}
					);
					e.preventDefault();
					return false;
				}
			);
		jQuery( document.createElement( "div" ) )
			.attr( "id", id + "_Bg" )
			.on(
				'click', function(e) {
					jQuery( "#" + id + "_Bg" ).fadeOut( 500 );
					jQuery( "#" + id ).fadeOut( 500 );
					e.preventDefault();
					return false;
				}
			)
			.appendTo( "body" );
		jQuery( '#' + id + ' table.colorPickerTable thead td' )
			.css(
				{
					'width':'12px',
					'height':'14px',
					'border':'1px solid #000',
					'cursor':'pointer'
				}
			);
		jQuery( '#' + id + ' table.colorPickerTable' )
			.css( {'border-collapse':'collapse'} );
		jQuery( '#' + id )
			.css(
				{
					'border':'1px solid #ccc',
					'background':'#333',
					'padding':'5px',
					'color':'#fff'
				}
			);
		jQuery( '#' + id + '_colorPreview' )
			.css( {'height':'50px'} );
		return id;
	};

	window.avventure_color_picker_show = function(id, fld, func) {
		if (id === null || id === '') {
			id = jQuery( '.iColorPickerTable' ).attr( 'id' );
		}
		var eICP = fld.offset();
		var w    = jQuery( '#' + id ).width();
		var h    = jQuery( '#' + id ).height();
		var l    = eICP.left + w < jQuery( window ).width() - 10 ? eICP.left : jQuery( window ).width() - 10 - w;
		var t    = eICP.top + fld.outerHeight() + h < jQuery( document ).scrollTop() + jQuery( window ).height() - 10 ? eICP.top + fld.outerHeight() : eICP.top - h - 13;
		jQuery( "#" + id )
			.data( {field: fld, func: func} )
			.css(
				{
					'top':t + "px",
					'left':l + "px",
					'position':'absolute',
					'z-index':999999
				}
			)
			.fadeIn( 500 );
		jQuery( "#" + id + "_Bg" )
			.css(
				{
					'position':'fixed',
					'z-index':999998,
					'top':0,
					'left':0,
					'width':'100%',
					'height':'100%'
				}
			)
			.fadeIn( 500 );
		var def = fld.val().substr( 0, 1 ) == '#' ? fld.val() : avventure_rgb2hex( fld.css( 'backgroundColor' ) );
		jQuery( '#' + id + '_colorPreview input,#' + id + '_colorOriginal input' ).val( def );
		jQuery( '#' + id + '_colorPreview,#' + id + '_colorOriginal' ).css( 'background',def );
	};

	/* Cookies manipulations
	---------------------------------------------------------------- */

	window.avventure_get_cookie = function(name) {
		var defa  = arguments[1] !== undefined ? arguments[1] : null;
		var start = document.cookie.indexOf( name + '=' );
		var len   = start + name.length + 1;
		if (( ! start) && (name != document.cookie.substring( 0, name.length ))) {
			return defa;
		}
		if (start == -1) {
			return defa;
		}
		var end = document.cookie.indexOf( ';', len );
		if (end == -1) {
			end = document.cookie.length;
		}
		return unescape( document.cookie.substring( len, end ) );
	};

	window.avventure_set_cookie = function(name, value) {
		var expires = arguments[2] !== undefined ? arguments[2] : 0;
		var path    = arguments[3] !== undefined ? arguments[3] : '/';
		var domain  = arguments[4] !== undefined ? arguments[4] : '';
		var secure  = arguments[5] !== undefined ? arguments[5] : '';
		var today   = new Date();
		today.setTime( today.getTime() );
		if (expires) {
			expires = expires * 1000 * 60 * 60 * 24;
		}
		var expires_date = new Date( today.getTime() + (expires) );
		document.cookie  = name + '='
				+ escape( value )
				+ ((expires) ? ';expires=' + expires_date.toGMTString() : '')
				+ ((path) ? ';path=' + path : '')
				+ ((domain) ? ';domain=' + domain : '')
				+ ((secure) ? ';secure' : '');
	};

	window.avventure_del_cookie = function(name, path, domain) {
		var path   = arguments[1] !== undefined ? arguments[1] : '/';
		var domain = arguments[2] !== undefined ? arguments[2] : '';
		if (avventure_get_cookie( name )) {
			document.cookie = name + '=' + ((path) ? ';path=' + path : '')
					+ ((domain) ? ';domain=' + domain : '')
					+ ';expires=Thu, 01-Jan-1970 00:00:01 GMT';
		}
	};

	/* ListBox and ComboBox manipulations
	---------------------------------------------------------------- */

	window.avventure_clear_listbox = function(box) {
		for (var i = box.options.length - 1; i >= 0; i--) {
			box.options[i] = null;
		}
	};

	window.avventure_add_listbox_item = function(box, val, text) {
		var item   = new Option();
		item.value = val;
		item.text  = text;
		box.options.add( item );
	};

	window.avventure_del_listbox_item_by_value = function(box, val) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].value == val) {
				box.options[i] = null;
				break;
			}
		}
	};

	window.avventure_del_listbox_item_by_text = function(box, txt) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].text == txt) {
				box.options[i] = null;
				break;
			}
		}
	};

	window.avventure_find_listbox_item_by_value = function(box, val) {
		var idx = -1;
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].value == val) {
				idx = i;
				break;
			}
		}
		return idx;
	};

	window.avventure_find_listbox_item_by_text = function(box, txt) {
		var idx = -1;
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].text == txt) {
				idx = i;
				break;
			}
		}
		return idx;
	};

	window.avventure_select_listbox_item_by_value = function(box, val) {
		for (var i = 0; i < box.options.length; i++) {
			box.options[i].selected = (val == box.options[i].value);
		}
	};

	window.avventure_select_listbox_item_by_text = function(box, txt) {
		for (var i = 0; i < box.options.length; i++) {
			box.options[i].selected = (txt == box.options[i].text);
		}
	};

	window.avventure_get_listbox_values = function(box) {
		var delim = arguments[1] !== undefined ? arguments[1] : ',';
		var str   = '';
		for (var i = 0; i < box.options.length; i++) {
			str += (str ? delim : '') + box.options[i].value;
		}
		return str;
	};

	window.avventure_get_listbox_texts = function(box) {
		var delim = arguments[1] !== undefined ? arguments[1] : ',';
		var str   = '';
		for (var i = 0; i < box.options.length; i++) {
			str += (str ? delim : '') + box.options[i].text;
		}
		return str;
	};

	window.avventure_sort_listbox = function(box)  {
		var temp_opts = new Array();
		var temp      = new Option();
		for (var i = 0; i < box.options.length; i++) {
			temp_opts[i] = box.options[i].clone();
		}
		for (var x = 0; x < temp_opts.length - 1; x++) {
			for (var y = (x + 1); y < temp_opts.length; y++) {
				if (temp_opts[x].text > temp_opts[y].text) {
					temp         = temp_opts[x];
					temp_opts[x] = temp_opts[y];
					temp_opts[y] = temp;
				}
			}
		}
		for (var i = 0; i < box.options.length; i++) {
			box.options[i] = temp_opts[i].clone();
		}
	};

	window.avventure_get_listbox_selected_index = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return i;
			}
		}
		return -1;
	};

	window.avventure_get_listbox_selected_value = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i].value;
			}
		}
		return null;
	};

	window.avventure_get_listbox_selected_text = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i].text;
			}
		}
		return null;
	};

	window.avventure_get_listbox_selected_option = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i];
			}
		}
		return null;
	};

	/* Radio buttons manipulations
	---------------------------------------------------------------- */

	window.avventure_get_radio_value = function(radioGroupObj) {
		for (var i = 0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked) {
				return radioGroupObj[i].value;
			}
		}
		return null;
	};

	window.avventure_set_radio_checked_by_num = function(radioGroupObj, num) {
		for (var i = 0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked && i != num) {
				radioGroupObj[i].checked = false;
			} else if (i == num) {
				radioGroupObj[i].checked = true;
			}
		}
	};

	window.avventure_set_radio_checked_by_value = function(radioGroupObj, val) {
		for (var i = 0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked && radioGroupObj[i].value != val) {
				radioGroupObj[i].checked = false;
			} else if (radioGroupObj[i].value == val) {
				radioGroupObj[i].checked = true;
			}
		}
	};

	/* Form manipulations
	---------------------------------------------------------------- */

	/*
	// Usage example:
	var error = avventure_form_validate(jQuery(form_selector), {		// -------- Options ---------
		error_message_show: true,									// Display or not error message
		error_message_time: 5000,									// Time to display error message
		error_message_class: 'message_box message_box_error',		// Class, appended to error message block
		error_message_text: 'Global error text',					// Global error message text (if don't write message in checked field)
		error_fields_class: 'error_field',							// Class, appended to error fields
		exit_after_first_error: false,								// Cancel validation and exit after first error
		rules: [
			{
				field: 'author',																// Checking field name
				min_length: { value: 1,	 message: 'The author name can\'t be empty' },			// Min character count (0 - don't check), message - if error occurs
				max_length: { value: 60, message: 'Too long author name'}						// Max character count (0 - don't check), message - if error occurs
			},
			{
				field: 'email',
				min_length: { value: 7,	 message: 'Too short (or empty) email address' },
				max_length: { value: 60, message: 'Too long email address'},
				mask: { value: '^([a-z0-9_\\-]+\\.)*[a-z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$', message: 'Invalid email address'}
			},
			{
				field: 'comment',
				min_length: { value: 1,	 message: 'The comment text can\'t be empty' },
				max_length: { value: 200, message: 'Too long comment'}
			},
			{
				field: 'pwd1',
				min_length: { value: 5,	 message: 'The password can\'t be less then 5 characters' },
				max_length: { value: 20, message: 'Too long password'}
			},
			{
				field: 'pwd2',
				equal_to: { value: 'pwd1',	 message: 'The passwords in both fields must be equals' }
			}
		]
	});
	*/

	window.avventure_form_validate = function(form, opt) {
		var error_msg = '';
		form.find( ":input" ).each(
			function() {
				if (error_msg !== '' && opt.exit_after_first_error) {
					return;
				}
				for (var i = 0; i < opt.rules.length; i++) {
					if (jQuery( this ).attr( "name" ) == opt.rules[i].field) {
						var val   = jQuery( this ).val();
						var error = false;
						if (typeof(opt.rules[i].min_length) == 'object') {
							if (opt.rules[i].min_length.value > 0 && val.length < opt.rules[i].min_length.value) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].min_length.message) != 'undefined' ? opt.rules[i].min_length.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].max_length) == 'object') {
							if (opt.rules[i].max_length.value > 0 && val.length > opt.rules[i].max_length.value) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].max_length.message) != 'undefined' ? opt.rules[i].max_length.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].mask) == 'object') {
							if (opt.rules[i].mask.value !== '') {
								var regexp = new RegExp( opt.rules[i].mask.value );
								if ( ! regexp.test( val )) {
									if (error_msg == '') {
										jQuery( this ).get( 0 ).focus();
									}
									error_msg += '<p class="error_item">' + (typeof(opt.rules[i].mask.message) != 'undefined' ? opt.rules[i].mask.message : opt.error_message_text ) + '</p>';
									error      = true;
								}
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].state) == 'object') {
							if (opt.rules[i].state.value == 'checked' && ! jQuery( this ).get( 0 ).checked) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].state.message) != 'undefined' ? opt.rules[i].state.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].equal_to) == 'object') {
							if (opt.rules[i].equal_to.value !== '' && val != jQuery( jQuery( this ).get( 0 ).form[opt.rules[i].equal_to.value] ).val()) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].equal_to.message) != 'undefined' ? opt.rules[i].equal_to.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (opt.error_fields_class !== '') {
							jQuery( this ).toggleClass( opt.error_fields_class, error );
						}
					}
				}
			}
		);
		if (error_msg !== '' && opt.error_message_show) {
			var error_message_box = form.find( ".result" );
			if (error_message_box.length == 0) {
				error_message_box = form.parent().find( ".result" );
			}
			if (error_message_box.length == 0) {
				form.append( '<div class="result"></div>' );
				error_message_box = form.find( ".result" );
			}
			if (opt.error_message_class) {
				error_message_box.toggleClass( opt.error_message_class, true );
			}
			error_message_box.html( error_msg ).fadeIn();
			setTimeout( function() { error_message_box.fadeOut(); }, opt.error_message_time );
		}
		return error_msg !== '';
	};

	/* Document manipulations
	---------------------------------------------------------------- */

	// Animated scroll to selected id
	window.avventure_document_animate_to = function(id, callback) {
		var oft = ! isNaN( id ) ? Number( id ) : 0;
		if (isNaN( id )) {
			if (id.indexOf( '#' ) == -1) {
				id = '#' + id;
			}
			var obj = jQuery( id ).eq( 0 );
			if (obj.length == 0) {
				return;
			}
			oft = obj.offset().top;
		}
		var st    = jQuery( window ).scrollTop();
		var oft2  = Math.max( 0, oft - avventure_fixed_rows_height() );
		var speed = Math.min( 1200, Math.max( 300, Math.round( Math.abs( oft2 - st ) / jQuery( window ).height() * 300 ) ) );
		if (st == 0) {
			setTimeout(
				function() {
					if (isNaN( id )) {
						oft = obj.offset().top;
					}
					oft2 = Math.max( 0, oft - avventure_fixed_rows_height() );
					jQuery( 'body,html' ).stop( true ).animate( {scrollTop: oft2}, Math.floor( speed / 2 ), 'linear', callback );
				}, Math.floor( speed / 2 )
			);
		}
		jQuery( 'body,html' ).stop( true ).animate( {scrollTop: oft2}, speed, 'linear', callback );
	};

	// Detect fixed rows height
	window.avventure_fixed_rows_height = function() {
		var with_admin_bar  = arguments.length > 0 ? arguments[0] : true;
		var with_fixed_rows = arguments.length > 1 ? arguments[1] : true;
		var oft             = 0;
		// Admin bar height (if visible and fixed)
		if (with_admin_bar) {
			var admin_bar = jQuery( '#wpadminbar' );
			oft          += admin_bar.length > 0 && admin_bar.css( 'display' ) != 'none' && admin_bar.css( 'position' ) == 'fixed'
							? admin_bar.height()
							: 0;
		}
		// Fixed rows height
		if (with_fixed_rows) {
			jQuery( '.sc_layouts_row_fixed_on' ).each(
				function() {
					if (jQuery( this ).css( 'position' ) == 'fixed') {
						oft += jQuery( this ).height();
					}
				}
			);
		}
		return oft;
	};

	// Change browser address without reload page
	window.avventure_document_set_location = function(curLoc){
		try {
			history.pushState( null, null, curLoc );
			return;
		} catch (e) {
		}
		location.href = curLoc;
	};

	// Add/Change arguments to the url address
	window.avventure_add_to_url = function(loc, prm) {
		var ignore_empty = arguments[2] !== undefined ? arguments[2] : true;
		var q            = loc.indexOf( '?' );
		var attr         = {};
		if (q > 0) {
			var qq    = loc.substr( q + 1 ).split( '&' );
			var parts = '';
			for (var i = 0; i < qq.length; i++) {
				var parts      = qq[i].split( '=' );
				attr[parts[0]] = parts.length > 1 ? parts[1] : '';
			}
		}
		for (var p in prm) {
			attr[p] = prm[p];
		}
		loc   = (q > 0 ? loc.substr( 0, q ) : loc) + '?';
		var i = 0;
		for (p in attr) {
			if (ignore_empty && attr[p] == '') {
				continue;
			}
			loc += (i++ > 0 ? '&' : '') + p + '=' + attr[p];
		}
		return loc;
	};

	// Check if url is page-inner (local) link
	window.avventure_is_local_link = function(url) {
		var rez = url !== undefined;
		if (rez) {
			var url_pos = url.indexOf( '#' );
			if (url_pos == 0 && url.length == 1) {
				rez = false;
			} else {
				if (url_pos < 0) {
					url_pos = url.length;
				}
				var loc     = window.location.href;
				var loc_pos = loc.indexOf( '#' );
				if (loc_pos > 0) {
					loc = loc.substring( 0, loc_pos );
				}
				rez = url_pos == 0;
				if ( ! rez) {
					rez = loc == url.substring( 0, url_pos );
				}
			}
		}
		return rez;
	};

	/* Browsers detection
	---------------------------------------------------------------- */

	window.avventure_browser_is_mobile = function() {
		var check = false;
		(function(a){if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test( a ) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test( a.substr( 0,4 ) )) {
				check = true}
		})( navigator.userAgent || navigator.vendor || window.opera );
		return check;
	};

	window.avventure_browser_is_ios = function() {
		return navigator.userAgent.match( /iPad|iPhone|iPod/i ) != null;
	};

	window.avventure_is_retina = function() {
		var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
		return (window.devicePixelRatio > 1) || (window.matchMedia && window.matchMedia( mediaQuery ).matches);
	};

	/* File functions
	---------------------------------------------------------------- */

	window.avventure_get_file_name = function(path) {
		path    = path.replace( /\\/g, '/' );
		var pos = path.lastIndexOf( '/' );
		if (pos >= 0) {
			path = path.substr( pos + 1 );
		}
		return path;
	};

	window.avventure_get_file_ext = function(path) {
		var pos = path.lastIndexOf( '.' );
		path    = pos >= 0 ? path.substr( pos + 1 ) : '';
		return path;
	};

	/* Image functions
	---------------------------------------------------------------- */

	// Return true, if all images in the specified container are loaded
	window.avventure_is_images_loaded = function(cont) {
		var complete = true;
		cont.find( 'img' ).each(
			function() {
				// If any of previous images is not loaded - skip rest
				if ( ! complete) {
					return;
				}
				var img = jQuery( this ).get( 0 );
				if (typeof img.naturalWidth == 'number' && typeof img.naturalHeight == 'number') {
					// See if "naturalWidth" and "naturalHeight" properties are available
					complete = ! (this.naturalWidth == 0 && this.naturalHeight == 0);
				} else if (typeof img.complete == 'boolean') {
					// See if "complete" property is available
					complete = img.complete;
				}
			}
		);
		return complete;
	};

	// Call function when all images in the specified container are loaded
	window.avventure_when_images_loaded = function(cont, callback, max_delay) {
		if (max_delay === undefined) {
			var max_delay = 3000;
		}
		if (max_delay <= 0 || avventure_is_images_loaded( cont )) {
			callback();
		} else {
			setTimeout(
				function(){
					avventure_when_images_loaded( cont, callback, max_delay - 200 );
				}, 200
			);
		}
	};

	/* Debug functions
	---------------------------------------------------------------- */
	window.avventure_debug_object = function(obj) {
		var recursive   = arguments[1] ? arguments[1] : 0;			// Show inner objects (arrays) in depth
		var showMethods = arguments[2] ? arguments[2] : false;		// Show object's methods
		var level       = arguments[3] ? arguments[3] : 0;				// Nesting level (for internal usage only)
		var dispStr     = "";
		var addStr      = "";
		var curStr      = "";
		if (level > 0) {
			dispStr += (obj === null ? "null" : typeof(obj)) + "\n";
			addStr   = avventure_replicate( ' ', level * 2 );
		}
		if (obj !== null && (typeof(obj) == 'object' || typeof(obj) == 'array')) {
			for (var prop in obj) {
				if ( ! showMethods && typeof(obj[prop]) == 'function') {	// || prop=='innerHTML' || prop=='outerHTML' || prop=='innerText' || prop=='outerText')
					continue;
				}
				if (level < recursive && (typeof(obj[prop]) == 'object' || typeof(obj[prop]) == 'array') && obj[prop] != obj) {
					dispStr += addStr + prop + '=' + avventure_debug_object( obj[prop], recursive, showMethods, level + 1 );
				} else {
					try {
						curStr = "" + obj[prop];
					} catch (e) {
						curStr = "--- Not evaluate ---";
					}
					dispStr += addStr + prop + '=' + (typeof(obj[prop]) == 'string' ? '"' : '') + curStr + (typeof(obj[prop]) == 'string' ? '"' : '') + "\n";
				}
			}
		} else if (typeof(obj) != 'function') {
			dispStr += addStr + (typeof(obj) == 'string' ? '"' : '') + obj + (typeof(obj) == 'string' ? '"' : '') + "\n";
		}

		return dispStr;	//decodeURI(dispStr);
	};

	window.avventure_debug_log = function(s, clr) {
		if (AVVENTURE_STORAGE['user_logged_in']) {
			if (jQuery( '#debug_log' ).length == 0) {
				jQuery( 'body' ).append( '<div id="debug_log"><span id="debug_log_close">x</span><pre id="debug_log_content"></pre></div>' );
				jQuery( "#debug_log_close" ).on(
					'click', function(e) {
						jQuery( '#debug_log' ).hide();
						e.preventDefault();
						return false;
					}
				);
			}
			if (clr) {
				jQuery( '#debug_log_content' ).empty();
			}
			jQuery( '#debug_log_content' ).prepend( s + ' ' );
			jQuery( '#debug_log' ).show();
		}
	};

	window.dcl === undefined && (window.dcl = function(s) { console.log( s ); });
	window.dco === undefined && (window.dco = function(s,r) { console.log( avventure_debug_object( s,r ) ); });
	window.dal === undefined && (window.dal = function(s) { if (AVVENTURE_STORAGE['user_logged_in']) {
			alert( s );
	} });
	window.dao === undefined && (window.dao = function(s,r) { if (AVVENTURE_STORAGE['user_logged_in']) {
			alert( avventure_debug_object( s,r ) );
	} });
	window.ddl === undefined && (window.ddl = function(s,c) { avventure_debug_log( s,c ); });
	window.ddo === undefined && (window.ddo = function(s,r,c) { avventure_debug_log( avventure_debug_object( s,r ),c ); });

})();
/* global jQuery:false */
/* global AVVENTURE_STORAGE:false */

jQuery( document ).ready(
	function() {
		"use strict";

		var theme_init_counter = 0;

		avventure_init_actions();

		// Theme init actions
		function avventure_init_actions() {

			if (AVVENTURE_STORAGE['vc_edit_mode'] && jQuery( '.vc_empty-placeholder' ).length == 0 && theme_init_counter++ < 30) {
				setTimeout( avventure_init_actions, 200 );
				return;
			}

			// Check fullheight elements
			jQuery( document ).on( 'action.init_hidden_elements', avventure_stretch_height );
			jQuery( document ).on( 'action.sc_layouts_row_fixed_off', avventure_stretch_height );
			jQuery( document ).on( 'action.sc_layouts_row_fixed_on', avventure_stretch_height );

			// Add resize on VC action vc-full-width-row
			// But we emulate 'action.resize_vc_row_start' and 'action.resize_vc_row_end'
			// to correct resize sliders and video inside 'boxed' pages
			var vc_resize = false;
			jQuery( document ).on(
				'action.resize_vc_row_start', function(e, el) {
					vc_resize = true;
					avventure_resize_actions( el );
				}
			);

			// Resize handlers
			jQuery( window ).resize(
				function() {
					if ( ! vc_resize) {
						avventure_resize_actions();
					}
				}
			);

			// Scroll handlers
			AVVENTURE_STORAGE['scroll_busy'] = true;
			jQuery( window ).scroll(
				function() {
					if (window.requestAnimationFrame) {
						if ( ! AVVENTURE_STORAGE['scroll_busy']) {
							window.requestAnimationFrame( avventure_scroll_actions );
							AVVENTURE_STORAGE['scroll_busy'] = true;
						}
					} else {
						avventure_scroll_actions();
					}
				}
			);

			// First call to init core actions
			avventure_ready_actions();
			avventure_resize_actions();
			avventure_scroll_actions();

			// Wait when logo is loaded
			if (jQuery( 'body' ).hasClass( 'menu_style_side' )) {
				var side_logo = jQuery( '.menu_side_wrap .sc_layouts_logo' );
				if (side_logo.length > 0 && ! avventure_is_images_loaded( side_logo )) {
					avventure_when_images_loaded(
						side_logo, function() {
							avventure_stretch_sidemenu();
						}
					);
				}
			}
		}

		// Theme first load actions
		//==============================================
		function avventure_ready_actions() {

			// Add scheme class and js support
			//------------------------------------
			document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/,'js' );
			if (document.documentElement.className.indexOf( AVVENTURE_STORAGE['site_scheme'] ) == -1) {
				document.documentElement.className += ' ' + AVVENTURE_STORAGE['site_scheme'];
			}

			// Init background video
			//------------------------------------
			// Use Bideo to play local video
			if (AVVENTURE_STORAGE['background_video'] && jQuery( '.top_panel.with_bg_video' ).length > 0 && window.Bideo) {
				// Waiting 10ms after mejs init
				setTimeout(
					function() {
						jQuery( '.top_panel.with_bg_video' ).prepend( '<video id="background_video" loop muted></video>' );
						var bv = new Bideo();
						bv.init(
							{
								// Video element
								videoEl: document.querySelector( '#background_video' ),

								// Container element
								container: document.querySelector( '.top_panel' ),

								// Resize
								resize: true,

								// autoplay: false,

								isMobile: window.matchMedia( '(max-width: 768px)' ).matches,

								playButton: document.querySelector( '#background_video_play' ),
								pauseButton: document.querySelector( '#background_video_pause' ),

								// Array of objects containing the src and type
								// of different video formats to add
								// For example:
								//	src: [
								//			{	src: 'night.mp4', type: 'video/mp4' }
								//			{	src: 'night.webm', type: 'video/webm;codecs="vp8, vorbis"' }
								//		]
								src: [
								{
									src: AVVENTURE_STORAGE['background_video'],
									type: 'video/' + avventure_get_file_ext( AVVENTURE_STORAGE['background_video'] )
								}
								],

								// What to do once video loads (initial frame)
								onLoad: function () {
									//document.querySelector('#background_video_cover').style.display = 'none';
								}
							}
						);
					}, 10
				);

				// Use Tubular to play video from Youtube
			} else if (jQuery.fn.tubular) {
				jQuery( 'div#background_video' ).each(
					function() {
						var youtube_code = jQuery( this ).data( 'youtube-code' );
						if (youtube_code) {
							jQuery( this ).tubular( {videoId: youtube_code} );
							jQuery( '#tubular-player' ).appendTo( jQuery( this ) ).show();
							jQuery( '#tubular-container,#tubular-shield' ).remove();
						}
					}
				);
			}

			// Tabs
			//------------------------------------
			if (jQuery( '.avventure_tabs:not(.inited)' ).length > 0 && jQuery.ui && jQuery.ui.tabs) {
				jQuery( '.avventure_tabs:not(.inited)' ).each(
					function () {
						// Get initially opened tab
						var init = jQuery( this ).data( 'active' );
						if (isNaN( init )) {
							init       = 0;
							var active = jQuery( this ).find( '> ul > li[data-active="true"]' ).eq( 0 );
							if (active.length > 0) {
								init = active.index();
								if (isNaN( init ) || init < 0) {
									init = 0;
								}
							}
						} else {
							init = Math.max( 0, init );
						}
						// Init tabs
						jQuery( this ).addClass( 'inited' ).tabs(
							{
								active: init,
								show: {
									effect: 'fadeIn',
									duration: 300
								},
								hide: {
									effect: 'fadeOut',
									duration: 300
								},
								create: function( event, ui ) {
									if (ui.panel.length > 0) {
										jQuery( document ).trigger( 'action.init_hidden_elements', [ui.panel] );
									}
								},
								activate: function( event, ui ) {
									if (ui.newPanel.length > 0) {
										jQuery( document ).trigger( 'action.init_hidden_elements', [ui.newPanel] );
									}
								}
							}
						);
					}
				);
			}
			// AJAX loader for the tabs
			jQuery( '.avventure_tabs_ajax' ).on(
				"tabsbeforeactivate", function( event, ui ) {
					if (ui.newPanel.data( 'need-content' )) {
						avventure_tabs_ajax_content_loader( ui.newPanel, 1, ui.oldPanel );
					}
				}
			);
			// AJAX loader for the pages in the tabs
			jQuery( '.avventure_tabs_ajax' ).on(
				"click", '.nav-links a', function(e) {
					var panel = jQuery( this ).parents( '.avventure_tabs_content' );
					var page  = 1;
					var href  = jQuery( this ).attr( 'href' );
					var pos   = -1;
					if ((pos = href.lastIndexOf( '/page/' )) != -1 ) {
						page = Number( href.substr( pos + 6 ).replace( "/", "" ) );
						if ( ! isNaN( page )) {
							page = Math.max( 1, page );
						}
					}
					avventure_tabs_ajax_content_loader( panel, page );
					e.preventDefault();
					return false;
				}
			);

			// Menu
			//----------------------------------------------

			// Open/Close side menu
			jQuery( '.menu_side_button' ).on(
				'click', function(e){
					jQuery( this ).parent().toggleClass( 'opened' );
					e.preventDefault();
					return false;
				}
			);

			// Add images to the menu items with classes image-xxx
			jQuery( '.sc_layouts_menu li[class*="image-"]' ).each(
				function() {
					var classes = jQuery( this ).attr( 'class' ).split( ' ' );
					var icon    = '';
					for (var i = 0; i < classes.length; i++) {
						if (classes[i].indexOf( 'image-' ) >= 0) {
							icon = classes[i].replace( 'image-', '' );
							break;
						}
					}
					if (icon) {
						jQuery( this ).find( '>a' ).css( 'background-image', 'url(' + AVVENTURE_STORAGE['theme_url'] + 'trx_addons/css/icons.png/' + icon + '.png' );
					}
				}
			);

			// Add arrows to the mobile menu
			jQuery( '.menu_mobile .menu-item-has-children > a,.sc_layouts_menu_dir_vertical .menu-item-has-children > a' ).append( '<span class="open_child_menu"></span>' );

			// Open/Close mobile menu
			jQuery( '.sc_layouts_menu_mobile_button > a,.menu_mobile_button,.menu_mobile_description' ).on(
				'click', function(e) {
					if (jQuery( this ).parent().hasClass( 'sc_layouts_menu_mobile_button_burger' ) && jQuery( this ).next().hasClass( 'sc_layouts_menu_popup' )) {
						return;
					}
					jQuery( '.menu_mobile_overlay' ).fadeIn();
					jQuery( '.menu_mobile' ).addClass( 'opened' );
					jQuery( document ).trigger( 'action.stop_wheel_handlers' );
					e.preventDefault();
					return false;
				}
			);
			jQuery( document ).on(
				'keypress', function(e) {
					if (e.keyCode == 27) {
						if (jQuery( '.menu_mobile.opened' ).length == 1) {
							jQuery( '.menu_mobile_overlay' ).fadeOut();
							jQuery( '.menu_mobile' ).removeClass( 'opened' );
							jQuery( document ).trigger( 'action.start_wheel_handlers' );
							e.preventDefault();
							return false;
						}
					}
				}
			);;
			jQuery( '.menu_mobile_close, .menu_mobile_overlay' ).on(
				'click', function(e){
					jQuery( '.menu_mobile_overlay' ).fadeOut();
					jQuery( '.menu_mobile' ).removeClass( 'opened' );
					jQuery( document ).trigger( 'action.start_wheel_handlers' );
					e.preventDefault();
					return false;
				}
			);

			// Open/Close mobile submenu
			jQuery( '.menu_mobile,.sc_layouts_menu_dir_vertical' ).on(
				'click', 'li a, li a .open_child_menu', function(e) {
					var $a = jQuery( this ).hasClass( 'open_child_menu' ) ? jQuery( this ).parent() : jQuery( this );
					if ($a.parent().hasClass( 'menu-item-has-children' )) {
						if ($a.attr( 'href' ) == '#' || jQuery( this ).hasClass( 'open_child_menu' )) {
							if ($a.siblings( 'ul:visible' ).length > 0) {
								$a.siblings( 'ul' ).slideUp().parent().removeClass( 'opened' );
							} else {
								jQuery( this ).parents( 'li' ).siblings( 'li' ).find( 'ul:visible' ).slideUp().parent().removeClass( 'opened' );
								$a.siblings( 'ul' ).slideDown(
									function() {
										// Init layouts
										if ( ! jQuery( this ).hasClass( 'layouts_inited' ) && jQuery( this ).parents( '.menu_mobile' ).length > 0) {
											jQuery( this ).addClass( 'layouts_inited' );
											jQuery( document ).trigger( 'action.init_hidden_elements', [jQuery( this )] );
										}
									}
								).parent().addClass( 'opened' );
							}
						}
					}
					if ( ! jQuery( this ).hasClass( 'open_child_menu' ) && jQuery( this ).parents( '.menu_mobile' ).length > 0 && avventure_is_local_link( $a.attr( 'href' ) )) {
						jQuery( '.menu_mobile_close' ).trigger( 'click' );
					}
					if (jQuery( this ).hasClass( 'open_child_menu' ) || $a.attr( 'href' ) == '#') {
						e.preventDefault();
						return false;
					}
				}
			);

			if ( ! AVVENTURE_STORAGE['trx_addons_exist'] || jQuery( '.top_panel.top_panel_default .sc_layouts_menu_default' ).length > 0) {
				// Init superfish menus
				avventure_init_sfmenu( '.sc_layouts_menu:not(.inited) > ul:not(.inited)' );
				// Show menu
				jQuery( '.sc_layouts_menu:not(.inited)' ).each(
					function() {
						if (jQuery( this ).find( '>ul.inited' ).length == 1) {
							jQuery( this ).addClass( 'inited' );
						}
					}
				);
				// Generate 'scroll' event after the menu is showed
				jQuery( window ).trigger( 'scroll' );
			}

			// Blocks with stretch width
			//----------------------------------------------
			// Action to prepare stretch blocks in the third-party plugins
			jQuery( document ).trigger( 'action.prepare_stretch_width' );
			// Wrap stretch blocks
			jQuery( '.trx-stretch-width' ).wrap( '<div class="trx-stretch-width-wrap"></div>' );
			jQuery( '.trx-stretch-width' ).after( '<div class="trx-stretch-width-original"></div>' );
			avventure_stretch_width();

			// Pagination
			//------------------------------------

			// Load more
			jQuery( '.nav-load-more' ).on(
				'click', function(e) {
					if (AVVENTURE_STORAGE['load_more_link_busy']) {
						return;
					}
					AVVENTURE_STORAGE['load_more_link_busy'] = true;
					var more                               = jQuery( this );
					var page                               = Number( more.data( 'page' ) );
					var max_page                           = Number( more.data( 'max-page' ) );
					if (page >= max_page) {
						more.parent().hide();
						return;
					}
					more.parent().addClass( 'loading' );

					var panel = more.parents( '.avventure_tabs_content' );

					// Load simple page content
					if (panel.length == 0) {
						jQuery.get(
							location.href, {
								paged: page + 1
							}
						).done(
							function(response) {
								// Get inline styles and add to the page styles
								var selector = 'avventure-inline-styles-inline-css';
								var p1       = response.indexOf( selector );
								if (p1 < 0) {
									selector = 'trx_addons-inline-styles-inline-css';
									p1       = response.indexOf( selector );
								}
								if (p1 > 0) {
									p1                 = response.indexOf( '>', p1 ) + 1;
									var p2             = response.indexOf( '</style>', p1 );
									var inline_css_add = response.substring( p1, p2 );
									var inline_css     = jQuery( '#' + selector );
									if (inline_css.length == 0) {
										jQuery( 'body' ).append( '<style id="' + selector + '" type="text/css">' + inline_css_add + '</style>' );
									} else {
										inline_css.append( inline_css_add );
									}
								}
								// Get new posts and append to the .posts_container
								avventure_loadmore_add_items(
									jQuery( '.content .posts_container' ).eq( 0 ),
									jQuery( response ).find(
										'.content .posts_container > article,'
																	+ '.content .posts_container > div[class*="column-"],'
											+ '.content .posts_container > .masonry_item'
									)
								);
							}
						);

						// Load tab's panel content
					} else {
						jQuery.post(
							AVVENTURE_STORAGE['ajax_url'], {
								nonce: AVVENTURE_STORAGE['ajax_nonce'],
								action: 'avventure_ajax_get_posts',
								blog_template: panel.data( 'blog-template' ),
								blog_style: panel.data( 'blog-style' ),
								posts_per_page: panel.data( 'posts-per-page' ),
								cat: panel.data( 'cat' ),
								parent_cat: panel.data( 'parent-cat' ),
								post_type: panel.data( 'post-type' ),
								taxonomy: panel.data( 'taxonomy' ),
								page: page + 1
							}
						).done(
							function(response) {
								var rez = {};
								try {
									rez = JSON.parse( response );
								} catch (e) {
									rez = { error: AVVENTURE_STORAGE['msg_ajax_error'] };
									console.log( response );
								}
								if (rez.error !== '') {
									panel.html( '<div class="avventure_error">' + rez.error + '</div>' );
								} else {
									avventure_loadmore_add_items( panel.find( '.posts_container' ), jQuery( rez.data ).find( 'article' ) );
								}
							}
						);
					}

					// Append items to the container
					function avventure_loadmore_add_items(container, items) {
						if (container.length > 0 && items.length > 0) {
							items.addClass( 'just_loaded_items' );
							container.append( items );
							var just_loaded_items = container.find( '.just_loaded_items' );
							if (container.hasClass( 'portfolio_wrap' ) || container.hasClass( 'masonry_wrap' )) {
								just_loaded_items.addClass( 'hidden' );
								avventure_when_images_loaded(
									just_loaded_items, function() {
										just_loaded_items.removeClass( 'hidden' );
										container.masonry( 'appended', items ).masonry();
										if (container.hasClass( 'gallery_wrap' )) {
											AVVENTURE_STORAGE['GalleryFx'][container.attr( 'id' )].appendItems();
										}
									}
								);
							}
							just_loaded_items.removeClass( 'just_loaded_items hidden' );
							more.data( 'page', page + 1 ).parent().removeClass( 'loading' );
							// Remove TOC if exists (rebuild on init_hidden_elements)
							jQuery( '#toc_menu' ).remove();
							// Trigger actions to init new elements
							AVVENTURE_STORAGE['init_all_mediaelements'] = true;
							jQuery( document ).trigger( 'action.init_hidden_elements', [container.parent()] );
						}
						if (page + 1 >= max_page) {
							more.parent().hide();
						} else {
							AVVENTURE_STORAGE['load_more_link_busy'] = false;
						}
						// Fire 'window.scroll' after clearing busy state
						jQuery( window ).trigger( 'scroll' );
					}
					e.preventDefault();
					return false;
				}
			);

			// Infinite scroll
			jQuery( document ).on(
				'action.scroll_avventure', function(e) {
					if (AVVENTURE_STORAGE['load_more_link_busy']) {
						return;
					}
					var container = jQuery( '.content > .posts_container' ).eq( 0 );
					var inf       = jQuery( '.nav-links-infinite' );
					if (inf.length == 0) {
						return;
					}
					if (container.offset().top + container.height() < jQuery( window ).scrollTop() + jQuery( window ).height() * 1.5) {
						inf.find( 'a' ).trigger( 'click' );
					}
				}
			);

			// Comments
			//------------------------------------

			// Checkbox with "I agree..."
			if (jQuery('input[type="checkbox"][name="i_agree_privacy_policy"]:not(.inited),input[type="checkbox"][name="gdpr_terms"]:not(.inited),input[type="checkbox"][name="wpgdprc"]:not(.inited),#wpmtst-form input[type="checkbox"]:not(.inited),input[type="checkbox"][name="AGREE_TO_TERMS"]:not(.inited)').length > 0) {
				jQuery('input[type="checkbox"][name="i_agree_privacy_policy"]:not(.inited),input[type="checkbox"][name="gdpr_terms"]:not(.inited),input[type="checkbox"][name="wpgdprc"]:not(.inited),#wpmtst-form input[type="checkbox"]:not(.inited),input[type="checkbox"][name="AGREE_TO_TERMS"]:not(.inited)')
					.addClass('inited')
					.on('change', function(e) {
						if (jQuery(this).get(0).checked)
							jQuery(this).parents('form').find('button,input[type="submit"]').removeAttr('disabled');
						else
							jQuery(this).parents('form').find('button,input[type="submit"]').attr('disabled', 'disabled');
					}).trigger('change');
			}


			// Other settings
			//------------------------------------

			jQuery( document ).trigger( 'action.ready_avventure' );

			// Add theme-specific handlers
			jQuery( document ).on( 'action.init_hidden_elements', avventure_init_post_formats );
			jQuery( document ).on( 'action.init_hidden_elements', avventure_add_toc_to_sidemenu );

			// Init hidden elements (if exists)
			jQuery( document ).trigger( 'action.init_hidden_elements', [jQuery( 'body' ).eq( 0 )] );

		} //end ready

		// Scroll actions
		//==============================================

		// Do actions when page scrolled
		function avventure_scroll_actions() {

			// Call theme/plugins specific action (if exists)
			//----------------------------------------------
			jQuery( document ).trigger( 'action.scroll_avventure' );

			// Fix/unfix sidebar
			avventure_fix_sidebar();

			// Shift top and footer panels when header position is 'Under content'
			avventure_shift_under_panels();

			// Set flag about scroll actions are finished
			AVVENTURE_STORAGE['scroll_busy'] = false;
		}

		// Shift top and footer panels when header position is 'Under content'
		function avventure_shift_under_panels() {
			if (jQuery( 'body' ).hasClass( 'header_position_under' ) && ! avventure_browser_is_mobile()) {

				var header  = jQuery( '.top_panel' );
				var footer  = jQuery( '.footer_wrap' );
				var content = jQuery( '.page_content_wrap' );

				// Disable 'under' behavior on small screen
				if (jQuery( 'body' ).hasClass( 'mobile_layout' )) {	//jQuery(window).width() < 768) {
					if (header.css( 'position' ) == 'fixed') {
						// Header
						header.css(
							{
								'position': 'relative',
								'left': 'auto',
								'top': 'auto',
								'width': 'auto',
								'transform': 'none',
								'zIndex': 3
							}
						);
						header.find( '.top_panel_mask' ).hide();
						// Content
						content.css(
							{
								'marginTop': 0,
								'marginBottom': 0,
								'zIndex': 2
							}
						);
						// Footer
						footer.css(
							{
								'position': 'relative',
								'left': 'auto',
								'bottom': 'auto',
								'width': 'auto',
								'transform': 'none',
								'zIndex': 1
							}
						);
						footer.find( '.top_panel_mask' ).hide();
					}
					return;
				}
				var delta           = 50;
				var scroll_offset   = jQuery( window ).scrollTop();
				var header_height   = header.height();
				var shift           = ! (/Chrome/.test( navigator.userAgent ) && /Google Inc/.test( navigator.vendor )) || header.find( '.slider_engine_revo' ).length == 0
							? 0	//1.2		// Parallax speed (if 0 - disable parallax)
							: 0;
				var adminbar        = jQuery( '#wpadminbar' );
				var adminbar_height = adminbar.length == 0 ? 0 : adminbar.height();
				var mask            = header.find( '.top_panel_mask' );
				var css             = {};
				if (mask.length == 0) {
					header.append( '<div class="top_panel_mask"></div>' );
					mask = header.find( '.top_panel_mask' );
				}
				if (header.css( 'position' ) !== 'fixed') {
					content.css(
						{
							'zIndex': 5,
							'marginTop': header_height + 'px'
						}
					);
					header.css(
						{
							'position': 'fixed',
							'left': 0,
							'top': adminbar_height + 'px',
							'width': '100%',
							'zIndex': 3
						}
					);
				} else {
					content.css( 'marginTop', header_height + 'px' );
				}
				if (scroll_offset > 0) {
					var offset = scroll_offset;	// - adminbar_height;
					if (offset <= header_height) {
						var mask_opacity = Math.max( 0, Math.min( 0.8, (offset - delta) / header_height ) );
						// Don't shift header with Revolution slider in Chrome
						if (shift) {
							header.css( 'transform', 'translate3d(0px, ' + (-Math.round( offset / shift )) + 'px, 0px)' );
						}
						mask.css(
							{
								'opacity': mask_opacity,
								'display': offset == 0 ? 'none' : 'block'
							}
						);
					} else {
						if (shift) {
							header.css( 'transform', 'translate3d(0px, ' + (-Math.round( offset / shift )) + 'px, 0px)' );
						}
					}
				} else {
					if (shift) {
						header.css( 'transform', 'none' );
					}
					if (mask.css( 'display' ) != 'none') {
						mask.css(
							{
								'opacity': 0,
								'display': 'none'
							}
						);
					}
				}
				var footer_height  = Math.min( footer.height(), jQuery( window ).height() );
				var footer_visible = (scroll_offset + jQuery( window ).height()) - (header.outerHeight() + jQuery( '.page_content_wrap' ).outerHeight());
				if (footer.css( 'position' ) !== 'fixed') {
					content.css(
						{
							'marginBottom': footer_height + 'px'
						}
					);
					footer.css(
						{
							'position': 'fixed',
							'left': 0,
							'bottom': 0,
							'width': '100%',
							'zIndex': 2
						}
					);
				} else {
					content.css( 'marginBottom', footer_height + 'px' );
				}
				if (footer_visible > 0) {
					if (footer.css( 'zIndex' ) == 2) {
						footer.css( 'zIndex', 4 );
					}
					mask = footer.find( '.top_panel_mask' );
					if (mask.length == 0) {
						footer.append( '<div class="top_panel_mask"></div>' );
						mask = footer.find( '.top_panel_mask' );
					}
					if (footer_visible <= footer_height) {
						var mask_opacity = Math.max( 0, Math.min( 0.8, (footer_height - footer_visible) / footer_height ) );
						// Don't shift header with Revolution slider in Chrome
						if (shift) {
							footer.css( 'transform', 'translate3d(0px, ' + Math.round( (footer_height - footer_visible) / shift ) + 'px, 0px)' );
						}
						mask.css(
							{
								'opacity': mask_opacity,
								'display': footer_height - footer_visible <= 0 ? 'none' : 'block'
							}
						);
					} else {
						if (shift) {
							footer.css( 'transform', 'none' );
						}
						if (mask.css( 'display' ) != 'none') {
							mask.css(
								{
									'opacity': 0,
									'display': 'none'
								}
							);
						}
					}
				} else {
					if (footer.css( 'zIndex' ) == 4) {
						footer.css( 'zIndex', 2 );
					}
				}
			}
		}

		// Resize actions
		//==============================================

		// Do actions when page scrolled
		function avventure_resize_actions(cont) {
			avventure_check_layout();
			avventure_fix_sidebar();
			avventure_fix_footer();
			avventure_stretch_width( cont );
			avventure_stretch_height( null, cont );
			avventure_stretch_bg_video();
			avventure_vc_row_fullwidth_to_boxed( cont );
			avventure_stretch_sidemenu();
			avventure_resize_video( cont );
			avventure_shift_under_panels();

			// Call theme/plugins specific action (if exists)
			//----------------------------------------------
			jQuery( document ).trigger( 'action.resize_avventure', [cont] );
		}

		// Stretch sidemenu (if present)
		function avventure_stretch_sidemenu() {
			var toc_items = jQuery( '.menu_side_wrap .toc_menu_item' );
			if (toc_items.length == 0) {
				return;
			}
			var toc_items_height = jQuery( window ).height()
								- avventure_fixed_rows_height( true, false )
								- jQuery( '.menu_side_wrap .sc_layouts_logo' ).outerHeight()
								- toc_items.length;
			var th               = Math.floor( toc_items_height / toc_items.length );
			var th_add           = toc_items_height - th * toc_items.length;
			if (AVVENTURE_STORAGE['menu_side_stretch'] && toc_items.length >= 5 && th >= 30) {
				toc_items.find( ".toc_menu_description,.toc_menu_icon" ).css(
					{
						'height': th + 'px',
						'lineHeight': th + 'px'
					}
				);
				toc_items.eq( 0 ).find( ".toc_menu_description,.toc_menu_icon" ).css(
					{
						'height': (th + th_add) + 'px',
						'lineHeight': (th + th_add) + 'px'
					}
				);
			}
			//jQuery('.menu_side_wrap #toc_menu').height(toc_items_height + toc_items.length - toc_items.eq(0).height());
		}

		// Scroll sidemenu (if present)
		jQuery( document ).on(
			'action.toc_menu_item_active', function() {
				var toc_menu = jQuery( '.menu_side_wrap #toc_menu' );
				if (toc_menu.length == 0) {
					return;
				}
				var toc_items = toc_menu.find( '.toc_menu_item' );
				if (toc_items.length == 0) {
					return;
				}
				var th           = toc_items.eq( 0 ).height(),
				toc_menu_pos     = parseFloat( toc_menu.css( 'top' ) ),
				toc_items_height = toc_items.length * th,
				menu_side_height = jQuery( window ).height()
								- avventure_fixed_rows_height( true, false )
								- jQuery( '.menu_side_wrap .sc_layouts_logo' ).outerHeight()
								- jQuery( '.menu_side_wrap .sc_layouts_logo + .toc_menu_item' ).outerHeight();
				if (toc_items_height > menu_side_height) {
					var toc_item_active = jQuery( '.menu_side_wrap .toc_menu_item_active' ).eq( 0 );
					if (toc_item_active.length == 1) {
						var toc_item_active_pos = (toc_item_active.index() + 1) * th;
						if (toc_menu_pos + toc_item_active_pos > menu_side_height - th) {
							toc_menu.css( 'top', Math.max( -toc_item_active_pos + 3 * th, menu_side_height - toc_items_height ) );
						} else if (toc_menu_pos < 0 && toc_item_active_pos < -toc_menu_pos + 2 * th) {
							toc_menu.css( 'top', Math.min( -toc_item_active_pos + 3 * th, 0 ) );
						}
					}
				} else if (toc_menu_pos < 0) {
					toc_menu.css( 'top', 0 );
				}
			}
		);

		// Check for mobile layout
		function avventure_check_layout() {
			var resize = true;
			if (jQuery( 'body' ).hasClass( 'no_layout' )) {
				jQuery( 'body' ).removeClass( 'no_layout' );
				resize = false;
			}
			var w = window.innerWidth;
			if (w == undefined) {
				w = jQuery( window ).width() + (jQuery( window ).height() < jQuery( document ).height() || jQuery( window ).scrollTop() > 0 ? 16 : 0);
			}
			if (AVVENTURE_STORAGE['mobile_layout_width'] >= w) {
				if ( ! jQuery( 'body' ).hasClass( 'mobile_layout' )) {
					jQuery( 'body' ).removeClass( 'desktop_layout' ).addClass( 'mobile_layout' );
					if (resize) {
						jQuery( window ).trigger( 'resize' );
					}
				}
			} else {
				if ( ! jQuery( 'body' ).hasClass( 'desktop_layout' )) {
					jQuery( 'body' ).removeClass( 'mobile_layout' ).addClass( 'desktop_layout' );
					jQuery( '.menu_mobile' ).removeClass( 'opened' );
					jQuery( '.menu_mobile_overlay' ).hide();
					if (resize) {
						jQuery( window ).trigger( 'resize' );
					}
				}
			}
			if (AVVENTURE_STORAGE['mobile_device'] || avventure_browser_is_mobile()) {
				jQuery( 'body' ).addClass( 'mobile_device' );
			}
		}

		// Stretch area to full window width
		function avventure_stretch_width(cont) {
			if (cont === undefined) {
				cont = jQuery( 'body' );
			}
			cont.find( '.trx-stretch-width' ).each(
				function() {
					var $el             = jQuery( this );
					var $el_cont        = $el.parents( '.page_wrap' );
					var $el_cont_offset = 0;
					if ($el_cont.length == 0) {
						$el_cont = jQuery( window );
					} else {
						$el_cont_offset = $el_cont.offset().left;
					}
					var $el_full        = $el.next( '.trx-stretch-width-original' );
					var el_margin_left  = parseInt( $el.css( 'margin-left' ), 10 );
					var el_margin_right = parseInt( $el.css( 'margin-right' ), 10 );
					var offset          = $el_cont_offset - $el_full.offset().left - el_margin_left;
					var width           = $el_cont.width();
					if ( ! $el.hasClass( 'inited' )) {
						$el.addClass( 'inited invisible' );
						$el.css(
							{
								'position': 'relative',
								'box-sizing': 'border-box'
							}
						);
					}
					$el.css(
						{
							'left': offset,
							'width': $el_cont.width()
						}
					);
					if ( ! $el.hasClass( 'trx-stretch-content' ) ) {
						var padding      = Math.max( 0, -1 * offset );
						var paddingRight = Math.max( 0, width - padding - $el_full.width() + el_margin_left + el_margin_right );
						$el.css( { 'padding-left': padding + 'px', 'padding-right': paddingRight + 'px' } );
					}
					$el.removeClass( 'invisible' );
				}
			);
		}

		// Stretch area to the full window height
		function avventure_stretch_height(e, cont) {
			if (cont === undefined) {
				cont = jQuery( 'body' );
			}
			cont.find( '.avventure-full-height' ).each(
				function () {
					// If item now invisible
					if ( jQuery( this ).parents( 'div:hidden,section:hidden,article:hidden' ).length > 0 ) {
						return;
					}
					var fullheight_item = jQuery( this ),
						fullheight_row  = jQuery( this ).parents( '.vc_row-o-full-height' );
					if (fullheight_row.length > 0) {
						if (fullheight_row.css('height') != 'auto') {
							fullheight_item.height( fullheight_row.height() );
						} else if (fullheight_item.css( 'height' ) != 'auto') {
							fullheight_item.height( 'auto' );
						}
					} else {
						var wh = jQuery( window ).height() >= 698
							? jQuery( window ).height() - avventure_fixed_rows_height()
							: 'auto';
						if ( wh > 0 ) {
							if ( fullheight_item.data( 'display' ) != fullheight_item.css( 'display' ) ) {
								fullheight_item.css( 'display', fullheight_item.data( 'display' ) );
							}
							if ( fullheight_item.css( 'height', 'auto' ).outerHeight() <= wh ) {
								fullheight_item.css( 'height', wh );
							}
						} else if ( wh == 'auto' && fullheight_item.css( 'height' ) != 'auto' ) {
							if (fullheight_item.data( 'display' ) == undefined) {
								fullheight_item.attr( 'data-display', fullheight_item.css( 'display' ) );
							}
							fullheight_item.css( {'height': wh, 'display': 'block'} );
						}
					}
				}
			);
		}

		// Fit video frames to document width
		function avventure_resize_video(cont) {
			if (cont === undefined) {
				cont = jQuery( 'body' );
			}
			cont.find( 'video' ).each(
				function() {
					// If item now invisible
					if (jQuery( this ).hasClass( 'trx_addons_resize' ) || jQuery( this ).addClass( 'avventure_resize' ).parents( 'div:hidden,section:hidden,article:hidden' ).length > 0) {
						return;
					}
					var video     = jQuery( this ).eq( 0 );
					var ratio     = (video.data( 'ratio' ) !== undefined ? video.data( 'ratio' ).split( ':' ) : [16,9]);
					ratio         = ratio.length != 2 || ratio[0] == 0 || ratio[1] == 0 ? 16 / 9 : ratio[0] / ratio[1];
					var mejs_cont = video.parents( '.mejs-video' );
					var w_attr    = video.data( 'width' );
					var h_attr    = video.data( 'height' );
					if ( ! w_attr || ! h_attr) {
						w_attr = video.attr( 'width' );
						h_attr = video.attr( 'height' );
						if ( ! w_attr || ! h_attr) {
							return;
						}
						video.data( {'width': w_attr, 'height': h_attr} );
					}
					var percent = ('' + w_attr).substr( -1 ) == '%';
					w_attr      = parseInt( w_attr, 10 );
					h_attr      = parseInt( h_attr, 10 );
					var w_real  = Math.round(
						mejs_cont.length > 0
									? Math.min( percent ? 10000 : w_attr, mejs_cont.parents( 'div,article' ).width() )
									: Math.min( percent ? 10000 : w_attr, video.parents( 'div,article' ).width() )
					),
					h_real      = Math.round( percent ? w_real / ratio : w_real / w_attr * h_attr );
					if (parseInt( video.attr( 'data-last-width' ), 10 ) == w_real) {
						return;
					}
					if (percent) {
						video.height( h_real );
					} else if (video.parents( '.wp-video-playlist' ).length > 0) {
						if (mejs_cont.length === 0) {
							video.attr( {'width': w_real, 'height': h_real} );
						}
					} else {
						video.attr( {'width': w_real, 'height': h_real} ).css( {'width': w_real + 'px', 'height': h_real + 'px'} );
						if (mejs_cont.length > 0) {
							avventure_set_mejs_player_dimensions( video, w_real, h_real );
						}
					}
					video.attr( 'data-last-width', w_real );
				}
			);
			cont.find( '.video_frame iframe' ).each(
				function() {
					// If item now invisible
					if (jQuery( this ).hasClass( 'trx_addons_resize' ) || jQuery( this ).addClass( 'avventure_resize' ).parents( 'div:hidden,section:hidden,article:hidden' ).length > 0) {
						return;
					}
					var iframe = jQuery( this ).eq( 0 );
					if (iframe.attr( 'src' ).indexOf( 'soundcloud' ) > 0) {
						return;
					}
					var ratio  = (iframe.data( 'ratio' ) !== undefined
							? iframe.data( 'ratio' ).split( ':' )
							: (iframe.parent().data( 'ratio' ) !== undefined
								? iframe.parent().data( 'ratio' ).split( ':' )
								: (iframe.find( '[data-ratio]' ).length > 0
									? iframe.find( '[data-ratio]' ).data( 'ratio' ).split( ':' )
									: [16,9]
									)
								)
							);
					ratio      = ratio.length != 2 || ratio[0] == 0 || ratio[1] == 0 ? 16 / 9 : ratio[0] / ratio[1];
					var w_attr = iframe.attr( 'width' );
					var h_attr = iframe.attr( 'height' );
					if ( ! w_attr || ! h_attr) {
						return;
					}
					var percent = ('' + w_attr).substr( -1 ) == '%';
					w_attr      = parseInt( w_attr, 10 );
					h_attr      = parseInt( h_attr, 10 );
					var par     = iframe.parents( 'div,section' ),
					pw          = par.width(),
					ph          = par.height(),
					w_real      = pw,
					h_real      = Math.round( percent ? w_real / ratio : w_real / w_attr * h_attr );
					if (par.css( 'position' ) == 'absolute' && h_real > ph) {
						h_real = ph;
						w_real = Math.round( percent ? h_real * ratio : h_real * w_attr / h_attr )
					}
					if (parseInt( iframe.attr( 'data-last-width' ), 10 ) == w_real) {
						return;
					}
					iframe.css( {'width': w_real + 'px', 'height': h_real + 'px'} );
					iframe.attr( 'data-last-width', w_real );
				}
			);
		}

		// Set Media Elements player dimensions
		function avventure_set_mejs_player_dimensions(video, w, h) {
			if (mejs) {
				for (var pl in mejs.players) {
					if (mejs.players[pl].media.src == video.attr( 'src' )) {
						if (mejs.players[pl].media.setVideoSize) {
							mejs.players[pl].media.setVideoSize( w, h );
						} else if (mejs.players[pl].media.setSize) {
							mejs.players[pl].media.setSize( w, h );
						}
						mejs.players[pl].setPlayerSize( w, h );
						mejs.players[pl].setControlsSize();
					}
				}
			}
		}

		// Stretch background video
		function avventure_stretch_bg_video() {
			var video_wrap = jQuery( 'div#background_video,.tourmaster-background-video' );
			if (video_wrap.length == 0) {
				return;
			}
			var cont = video_wrap.hasClass( 'tourmaster-background-video' ) ? video_wrap.parent() : video_wrap,
			w        = cont.width(),
			h        = cont.height(),
			video    = video_wrap.find( '>iframe,>video' );
			if (w / h < 16 / 9) {
				w = h / 9 * 16;
			} else {
				h = w / 16 * 9;
			}
			video
			.attr( {'width': w, 'height': h} )
			.css( {'width': w, 'height': h} );
		}

		// Recalculate width of the vc_row[data-vc-full-width="true"] when content boxed or menu_style=='left|right'
		function avventure_vc_row_fullwidth_to_boxed(cont) {
			if (jQuery( 'body' ).hasClass( 'body_style_boxed' ) || jQuery( 'body' ).hasClass( 'menu_style_side' )) {
				if (cont === undefined || ! cont.hasClass( '.vc_row' ) || ! cont.data( 'vc-full-width' )) {
					cont = jQuery( '.vc_row[data-vc-full-width="true"]' );
				}
				var width_content      = jQuery( '.page_wrap' ).width();
				var width_content_wrap = jQuery( '.page_content_wrap .content_wrap' ).width();
				var indent             = ( width_content - width_content_wrap ) / 2;
				var rtl                = jQuery( 'html' ).attr( 'dir' ) == 'rtl';
				cont.each(
					function() {
						var mrg             = parseInt( jQuery( this ).css( 'marginLeft' ) );
						var stretch_content = jQuery( this ).attr( 'data-vc-stretch-content' );
						var in_content      = jQuery( this ).parents( '.content_wrap' ).length > 0;
						jQuery( this ).css(
							{
								'width': width_content,
								'left': rtl ? 'auto' : (in_content ? -indent : 0) - mrg,
								'right': ! rtl ? 'auto' : (in_content ? -indent : 0) - mrg,
								'padding-left': stretch_content ? 0 : indent + mrg,
								'padding-right': stretch_content ? 0 : indent + mrg
							}
						);
					}
				);
			}
		}

		// Fix/unfix footer
		function avventure_fix_footer() {
			if (jQuery( 'body' ).hasClass( 'header_position_under' ) && ! avventure_browser_is_mobile()) {
				var ft = jQuery( '.footer_wrap' );
				if (ft.length > 0) {
					var ft_height = ft.outerHeight( false ),
					pc            = jQuery( '.page_content_wrap' ),
					pc_offset     = pc.offset().top,
					pc_height     = pc.height();
					if (pc_offset + pc_height + ft_height < jQuery( window ).height()) {
						if (ft.css( 'position' ) != 'absolute') {
							ft.css(
								{
									'position': 'absolute',
									'left': 0,
									'bottom': 0,
									'width' :'100%'
								}
							);
						}
					} else {
						if (ft.css( 'position' ) != 'relative') {
							ft.css(
								{
									'position': 'relative',
									'left': 'auto',
									'bottom': 'auto'
								}
							);
						}
					}
				}
			}
		}

		// Fix/unfix sidebar
		function avventure_fix_sidebar() {
			var sb      = jQuery( '.sidebar' );
			var content = sb.siblings( '.content' );
			if (sb.length > 0) {

				// Unfix when sidebar is under content
				if (content.css( 'float' ) == 'none') {

					var old_style = sb.data( 'old_style' );
					if (old_style !== undefined) {
						sb.attr( 'style', old_style ).removeAttr( 'data-old_style' );
					}

				} else {

					var sb_height      = sb.outerHeight();
					var content_height = content.outerHeight();
					var content_top    = content.offset().top;
					var scroll_offset  = jQuery( window ).scrollTop();

					var top_panel_fixed_height = avventure_fixed_rows_height();

					// If sidebar shorter then content and page scrolled below the content's top
					if (sb_height < content_height && scroll_offset + top_panel_fixed_height > content_top) {

						var sb_init = {
							'position': 'undefined',
							'float': 'none',
							'top': 'auto',
							'bottom' : 'auto'
						};

						if (typeof AVVENTURE_STORAGE['scroll_offset_last'] == 'undefined') {
							AVVENTURE_STORAGE['sb_top_last']        = content_top;
							AVVENTURE_STORAGE['scroll_offset_last'] = scroll_offset;
							AVVENTURE_STORAGE['scroll_dir_last']    = 1;
						}
						var scroll_dir = scroll_offset - AVVENTURE_STORAGE['scroll_offset_last'];
						if (scroll_dir == 0) {
							scroll_dir = AVVENTURE_STORAGE['scroll_dir_last'];
						} else {
							scroll_dir = scroll_dir > 0 ? 1 : -1;
						}

						var sb_big = sb_height + 30 >= jQuery( window ).height() - top_panel_fixed_height,
						sb_top     = sb.offset().top;

						if (sb_top < 0) {
							sb_top = AVVENTURE_STORAGE['sb_top_last'];
						}

						// If sidebar height greater then window height
						if (sb_big) {

							// If change scrolling dir
							if (scroll_dir != AVVENTURE_STORAGE['scroll_dir_last'] && sb.css( 'position' ) == 'fixed') {
								sb_init.top      = sb_top - content_top;
								sb_init.position = 'absolute';

								// If scrolling down
							} else if (scroll_dir > 0) {
								if (scroll_offset + jQuery( window ).height() >= content_top + content_height + 30) {
									sb_init.bottom   = 0;
									sb_init.position = 'absolute';
								} else if (scroll_offset + jQuery( window ).height() >= (sb.css( 'position' ) == 'absolute' ? sb_top : content_top) + sb_height + 30) {

									sb_init.bottom   = 30;
									sb_init.position = 'fixed';
								}

								// If scrolling up
							} else {
								if (scroll_offset + top_panel_fixed_height <= sb_top) {
									sb_init.top      = top_panel_fixed_height;
									sb_init.position = 'fixed';
								}
							}

							// If sidebar height less then window height
						} else {
							if (scroll_offset + top_panel_fixed_height >= content_top + content_height - sb_height) {
								sb_init.bottom   = 0;
								sb_init.position = 'absolute';
							} else {
								sb_init.top      = top_panel_fixed_height;
								sb_init.position = 'fixed';
							}
						}

						if (sb_init.position != 'undefined') {
							// Detect horizontal position when resize
							var pos = 0;
							if (sb_init.position == 'fixed' || ( ! jQuery( 'body' ).hasClass( 'body_style_wide' ) && ! jQuery( 'body' ).hasClass( 'body_style_boxed' ))) {
								var sb_parent = sb.parent();
								pos           = sb_parent.position();
								pos           = pos.left + Math.max( 0, parseInt( sb_parent.css( 'paddingLeft' ), 10 ) )
											+ Math.max( 0, parseInt( sb_parent.css( 'marginLeft' ), 10 ) )
											+ (sb_init.position == 'fixed' && jQuery( 'body' ).hasClass( 'menu_style_right' ) && sb.hasClass( 'right' )
												? Math.max( 0, parseInt( jQuery( 'body' ).css( 'marginRight' ), 10 ) )
												: (sb_init.position == 'fixed' && jQuery( 'body' ).hasClass( 'menu_style_left' ) && sb.hasClass( 'left' )
													? Math.max( 0, parseInt( jQuery( 'body' ).css( 'marginLeft' ), 10 ) )
													: 0));
							}
							if (sb.hasClass( 'right' )) {
								sb_init.right = sb_init.position == 'fixed' || jQuery( 'body' ).hasClass( 'body_style_fullwide' ) ? pos : 0;
							} else {
								sb_init.left = sb_init.position == 'fixed' || jQuery( 'body' ).hasClass( 'body_style_fullwide' ) ? pos : 0;
							}

							// Set position
							if (sb.css( 'position' ) != sb_init.position || AVVENTURE_STORAGE['scroll_dir_last'] != scroll_dir) {
								if (sb.data( 'old_style' ) === undefined) {
									var style = sb.attr( 'style' );
									if ( ! style) {
										style = '';
									}
									sb.attr( 'data-old_style', style );
								}
								sb.css( sb_init );
							}
						}

						AVVENTURE_STORAGE['sb_top_last']        = sb_top;
						AVVENTURE_STORAGE['scroll_offset_last'] = scroll_offset;
						AVVENTURE_STORAGE['scroll_dir_last']    = scroll_dir;

					} else {

						// Unfix when page scrolling to top
						var old_style = sb.data( 'old_style' );
						if (old_style !== undefined) {
							sb.attr( 'style', old_style ).removeAttr( 'data-old_style' );
						}

					}
				}
			}
		}

		// Navigation
		//==============================================

		// Init Superfish menu
		function avventure_init_sfmenu(selector) {
			jQuery( selector ).show().each(
				function() {
					// Do not init the mobile menu - only add class 'inited'
					if (jQuery( this ).addClass( 'inited' ).parents( '.menu_mobile' ).length > 0) {
						return;
					}
					var animation_in = jQuery( this ).parent().data( 'animation_in' );
					if (animation_in == undefined) {
						animation_in = "none";
					}
					var animation_out = jQuery( this ).parent().data( 'animation_out' );
					if (animation_out == undefined) {
						animation_out = "none";
					}
					jQuery( this ).superfish(
						{
							delay: 500,
							animation: {
								opacity: 'show'
							},
							animationOut: {
								opacity: 'hide'
							},
							speed: 		animation_in != 'none' ? 500 : 200,
							speedOut:	animation_out != 'none' ? 500 : 200,
							autoArrows: false,
							dropShadows: false,
							onBeforeShow: function(ul) {
								// Detect horizontal position (left | right)
								if (jQuery( this ).parents( "ul" ).length > 1) {
									var w          = jQuery( '.page_wrap' ).width();
									var par_offset = jQuery( this ).parents( "ul" ).offset().left;
									var par_width  = jQuery( this ).parents( "ul" ).outerWidth();
									var ul_width   = jQuery( this ).outerWidth();
									if (par_offset + par_width + ul_width > w - 20 && par_offset - ul_width > 0) {
										jQuery( this ).addClass( 'submenu_left' );
									} else {
										jQuery( this ).removeClass( 'submenu_left' );
									}
								}
								// Shift vertical if menu going out the window
								if (jQuery( this ).parents( '.top_panel' ).length > 0) {
									var ul_height = jQuery( this ).outerHeight(),
									w_height      = jQuery( window ).height(),
									row           = jQuery( this ).parents( '.sc_layouts_row' ),
									row_offset    = 0,
									row_height    = 0,
									par           = jQuery( this ).parent(),
									par_offset    = 0;
									while (row.length > 0) {
										row_offset += row.outerHeight();
										if (row.hasClass( 'sc_layouts_row_fixed_on' )) {
											break;
										}
										row = row.prev();
									}
									while (par.length > 0) {
										par_offset += par.position().top + par.parent().position().top;
										row_height  = par.outerHeight();
										if (par.position().top == 0) {
											break;
										}
										par = par.parents( 'li' );
									}
									if (row_offset + par_offset + ul_height > w_height) {
										if (par_offset > ul_height) {
											jQuery( this ).css(
												{
													'top': 'auto',
													'bottom': '-1.4em'
												}
											);
										} else {
											jQuery( this ).css(
												{
													'top': '-' + (par_offset - row_height - 2) + 'px',
													'bottom': 'auto'
												}
											);
										}
									}
								}
								// Animation in
								if (animation_in != 'none') {
									jQuery( this ).removeClass( 'animated fast ' + animation_out );
									jQuery( this ).addClass( 'animated fast ' + animation_in );
								}
							},
							onBeforeHide: function(ul) {
								if (animation_out != 'none') {
									jQuery( this ).removeClass( 'animated fast ' + animation_in );
									jQuery( this ).addClass( 'animated fast ' + animation_out );
								}
							},
							onShow: function(ul) {
								// Init layouts
								if ( ! jQuery( this ).hasClass( 'layouts_inited' )) {
									jQuery( this ).addClass( 'layouts_inited' );
									jQuery( document ).trigger( 'action.init_hidden_elements', [jQuery( this )] );
								}
							}
						}
					);
				}
			);
		}

		// Add TOC in the side menu
		// Make this function global because it used in the elementor.js
		function avventure_add_toc_to_sidemenu() {
			if (jQuery( '.menu_side_inner' ).length > 0 && jQuery( '#toc_menu' ).length > 0) {
				jQuery( '#toc_menu' ).appendTo( '.menu_side_inner' );
				avventure_stretch_sidemenu();
			}
		};

		// Post formats init
		//=====================================================

		function avventure_init_post_formats(e, cont) {

			// Wrap select with .select_container
			cont.find( 'select:not(.esg-sorting-select):not([class*="trx_addons_attrib_"])' ).each(
				function() {
					var s = jQuery( this );
					if (s.css( 'display' ) != 'none'
					&& s.parents( '.select_container' ).length == 0
					&& ! s.next().hasClass( 'select2' )
					&& ! s.hasClass( 'select2-hidden-accessible' )) {
						s.wrap( '<div class="select_container"></div>' );
					}
				}
			);

			// MediaElement init
			avventure_init_media_elements( cont );

			// Video play button
			cont.find( '.format-video .post_featured.with_thumb .post_video_hover:not(.inited)' )
				.addClass( 'inited' )
				.on(
					'click', function(e) {
						jQuery( this ).parents( '.post_featured' )
						.addClass( 'post_video_play' )
						.find( '.post_video' ).html( jQuery( this ).data( 'video' ) );
						jQuery( window ).trigger( 'resize' );
						e.preventDefault();
						return false;
					}
				);
		}

		function avventure_init_media_elements(cont) {
			if (AVVENTURE_STORAGE['use_mediaelements'] && cont.find( 'audio:not(.inited),video:not(.inited)' ).length > 0) {
				if (window.mejs) {
					if (window.mejs.MepDefaults) {
						window.mejs.MepDefaults.enableAutosize = true;
					}
					if (window.mejs.MediaElementDefaults) {
						window.mejs.MediaElementDefaults.enableAutosize = true;
					}
					cont.find( 'audio:not(.inited),video:not(.inited)' ).each(
						function() {
							// If item now invisible
							if (jQuery( this ).parents( 'div:hidden,section:hidden,article:hidden' ).length > 0) {
								return;
							}
							if (jQuery( this ).addClass( 'inited' ).parents( '.mejs-mediaelement' ).length == 0
							&& jQuery( this ).parents( '.wp-block-video' ).length == 0
							&& ! jQuery( this ).hasClass( 'wp-block-cover__video-background' )
							&& jQuery( this ).parents( '.elementor-background-video-container' ).length == 0
							&& (AVVENTURE_STORAGE['init_all_mediaelements']
								|| ( ! jQuery( this ).hasClass( 'wp-audio-shortcode' )
									&& ! jQuery( this ).hasClass( 'wp-video-shortcode' )
									&& ! jQuery( this ).parent().hasClass( 'wp-playlist' )
									)
								)
							) {
								var media_tag = jQuery( this );
								var settings  = {
									enableAutosize: true,
									videoWidth: -1,		// if set, overrides <video width>
									videoHeight: -1,	// if set, overrides <video height>
									audioWidth: '100%',	// width of audio player
									audioHeight: 30,	// height of audio player
									success: function(mejs) {
										if ( mejs.pluginType && 'flash' === mejs.pluginType && mejs.attributes ) {
											mejs.attributes.autoplay
											&& 'false' !== mejs.attributes.autoplay
											&& mejs.addEventListener( 'canplay', function () {	mejs.play(); }, false );
											mejs.attributes.loop
											&& 'false' !== mejs.attributes.loop
											&& mejs.addEventListener( 'ended', function () {	mejs.play(); }, false );
										}
									}
								};
								jQuery( this ).mediaelementplayer( settings );
							}
						}
					);
				} else {
					setTimeout( function() { avventure_init_media_elements( cont ); }, 400 );
				}
			}
			// Init all media elements after first run
			setTimeout( function() { AVVENTURE_STORAGE['init_all_mediaelements'] = true; }, 1000 );
		}

		// Load the tab's content
		function avventure_tabs_ajax_content_loader(panel, page, oldPanel) {
			if (panel.html().replace( /\s/g, '' ) == '') {
				var height = oldPanel === undefined ? panel.height() : oldPanel.height();
				if (isNaN( height ) || height < 100) {
					height = 100;
				}
				panel.html( '<div class="avventure_tab_holder" style="min-height:' + height + 'px;"></div>' );
			} else {
				panel.find( '> *' ).addClass( 'avventure_tab_content_remove' );
			}
			panel.data( 'need-content', false ).addClass( 'avventure_loading' );
			jQuery.post(
				AVVENTURE_STORAGE['ajax_url'], {
					nonce: AVVENTURE_STORAGE['ajax_nonce'],
					action: 'avventure_ajax_get_posts',
					blog_template: panel.data( 'blog-template' ),
					blog_style: panel.data( 'blog-style' ),
					posts_per_page: panel.data( 'posts-per-page' ),
					cat: panel.data( 'cat' ),
					parent_cat: panel.data( 'parent-cat' ),
					post_type: panel.data( 'post-type' ),
					taxonomy: panel.data( 'taxonomy' ),
					page: page
				}
			).done(
				function(response) {
						panel.removeClass( 'avventure_loading' );
						var rez = {};
					try {
						rez = JSON.parse( response );
					} catch (e) {
						rez = { error: AVVENTURE_STORAGE['msg_ajax_error'] };
						console.log( response );
					}
					if (rez.error !== '') {
						panel.html( '<div class="avventure_error">' + rez.error + '</div>' );
					} else {
						panel.prepend( rez.data ).fadeIn(
							function() {
								jQuery( document ).trigger( 'action.init_hidden_elements', [panel] );
								jQuery( window ).trigger( 'scroll' );
								setTimeout(
									function() {
										panel.find( '.avventure_tab_holde' +
                                            'r,.avventure_tab_content_remove' ).remove();
										jQuery( window ).trigger( 'scroll' );
									}, 600
								);
							}
						);
					}
				}
			);
		}


        /* Class "Height" for Spacer */
        jQuery('.elementor-spacer-inner').each( function(){
            jQuery(this).addClass('height-'+ jQuery(this).height());
        });

        /* Responsive for "Image Markers" */
        jQuery('.wpim').each(function () {
            var wpim_h = jQuery(this).find('.wpim-image img').attr("height");
            var wpim_w = jQuery(this).find('.wpim-image img').attr("width");
            if (wpim_h && wpim_w) {
                jQuery(this).find('.wpim-marker').each(function () {
                    jQuery(this).css({
                        "top": (jQuery(this).css('top').replace(/[^-\d\.]/g, '') / wpim_h) * 100 + '%',
                        "left": (jQuery(this).css('left').replace(/[^-\d\.]/g, '') / wpim_w) * 100 + '%'
                    });
                });
            }
        });

        // Hover from image with link
        jQuery('a[rel="magnific"] > img').each(function () {
            var spec_class = '';
            var arr = ['alignleft', 'aligncenter', 'alignright'];
            var this_element = jQuery(this);
            jQuery.each(arr, function() {
                if (this_element.hasClass(this)){
                    this_element.removeClass(this);
                    spec_class = this;
                }
            });
            jQuery(this).wrap('<span class="image-link ' + spec_class + '"></span>');
        });
        if ( jQuery('.wpim').length ) {
	        if ( jQuery('.wpim:eq(0)').next().attr('id') == 'elementor' )
	    		jQuery('.wpim:eq(0)').hide();
	    }
    }
);
jQuery( window ).load(function() {
	"use strict";
	if( jQuery('body').hasClass('body_style_boxed') ) {
		if( jQuery('.elementor-section-stretched').length ) {
			jQuery('.elementor-section-stretched').each(function() {
				jQuery(this).addClass('elementor-section-boxed');
				jQuery(this).removeClass('elementor-section-stretched');
				jQuery(this).removeClass('elementor-section-full_width');
				jQuery(this).removeAttr('data-id');
				jQuery(this).removeAttr('style');
				jQuery(this).removeAttr('data-settings');
			});
		}
	}
});
// Buttons decoration (add 'hover' class)
// Attention! Not use cont.find('selector')! Use jQuery('selector') instead!

jQuery( document ).on(
	'action.init_hidden_elements', function(e, cont) {
		"use strict";

		if (AVVENTURE_STORAGE['button_hover'] && AVVENTURE_STORAGE['button_hover'] != 'default') {
			jQuery(
				'button:not(.search_submit):not([class*="sc_button_hover_"]),\
				.theme_button:not([class*="sc_button_hover_"]),\
				.sc_button:not([class*="sc_button_simple"]):not([class*="sc_button_hover_"]),\
				.sc_form_field button:not([class*="sc_button_hover_"]),\
				.trx_addons_hover_content .trx_addons_hover_links a:not([class*="sc_button_hover_"]),\
				.avventure_tabs .avventure_tabs_titles li a:not([class*="sc_button_hover_"]),\
				.hover_shop_buttons .icons a:not([class*="sc_button_hover_style_"]),\
				.mptt-navigation-tabs li a:not([class*="sc_button_hover_style_"]),\
				.edd_download_purchase_form .button:not([class*="sc_button_hover_style_"]),\
				.edd-submit.button:not([class*="sc_button_hover_style_"]),\
				.widget_edd_cart_widget .edd_checkout a:not([class*="sc_button_hover_style_"]),\
				.woocommerce #respond input#submit:not([class*="sc_button_hover_"]),\
				.woocommerce .button:not([class*="shop_"]):not([class*="view"]):not([class*="sc_button_hover_"]),\
				.woocommerce-page .button:not([class*="shop_"]):not([class*="view"]):not([class*="sc_button_hover_"]),\
				#buddypress a.button:not([class*="sc_button_hover_"])\
				.error404 .go_home\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_' + AVVENTURE_STORAGE['button_hover'] );
			if (AVVENTURE_STORAGE['button_hover'] != 'arrow') {
				jQuery(
					'input[type="submit"]:not([class*="sc_button_hover_"]),\
					input[type="button"]:not([class*="sc_button_hover_"]),\
					.vc_tta-accordion .vc_tta-panel-heading .vc_tta-controls-icon:not([class*="sc_button_hover_"]),\
					.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:not([class*="sc_button_hover_"]),\
					.tribe-events-button:not([class*="sc_button_hover_"]),\
					#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a:not([class*="sc_button_hover_"]),\
					.tribe-bar-mini #tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a:not([class*="sc_button_hover_"]),\
					.tribe-events-cal-links a:not([class*="sc_button_hover_"]),\
					.tribe-events-sub-nav li a:not([class*="sc_button_hover_"]),\
					.isotope_filters_button:not([class*="sc_button_hover_"]),\
					.sc_promo_modern .sc_promo_link2:not([class*="sc_button_hover_"]),\
					.slider_container .slider_prev:not([class*="sc_button_hover_"]),\
					.slider_container .slider_next:not([class*="sc_button_hover_"]),\
					.sc_slider_controller_titles .slider_controls_wrap > a:not([class*="sc_button_hover_"]),\
					html .minimal-light .esg-navigationbutton\
					'
				).addClass( 'sc_button_hover_just_init sc_button_hover_' + AVVENTURE_STORAGE['button_hover'] );
			}
			// Add alter styles of buttons
			jQuery(
				'.sc_slider_controller_titles .slider_controls_wrap > a:not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_default' );
			jQuery(
				'.trx_addons_hover_content .trx_addons_hover_links a:not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_inverse' );
			jQuery(
				'.post_item_single .post_content .post_meta .post_share .social_item .social_icon:not([class*="sc_button_hover_style_"]),\
				.woocommerce #respond input#submit.alt:not([class*="sc_button_hover_style_"]),\
				.woocommerce a.button.alt:not([class*="sc_button_hover_style_"]),\
				.woocommerce button.button.alt:not([class*="sc_button_hover_style_"]),\
				.woocommerce input.button.alt:not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_hover' );
			jQuery(
				'.woocommerce .woocommerce-message .button:not([class*="sc_button_hover_style_"]),\
				.woocommerce .woocommerce-info .button:not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_alter' );
			jQuery(
				'.sidebar .trx_addons_tabs .trx_addons_tabs_titles li a:not([class*="sc_button_hover_style_"]),\
				.avventure_tabs .avventure_tabs_titles li a:not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_alterbd' );
			jQuery(
				'.vc_tta-accordion .vc_tta-panel-heading .vc_tta-controls-icon:not([class*="sc_button_hover_style_"]),\
				.vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:not([class*="sc_button_hover_style_"]),\
				.sc_button.color_style_dark:not([class*="sc_button_simple"]):not([class*="sc_button_hover_style_"]),\
				.slider_prev:not([class*="sc_button_hover_style_"]),\
				.slider_next:not([class*="sc_button_hover_style_"]),\
				.trx_addons_video_player.with_cover .video_hover:not([class*="sc_button_hover_style_"]),\
				.trx_addons_tabs .trx_addons_tabs_titles li a:not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_dark' );
			jQuery(
				'.error404 .go_home'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_extra' );
			jQuery(
				'.sc_button.color_style_link2:not([class*="sc_button_simple"]):not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_link2' );
			jQuery(
				'.sc_button.color_style_link3:not([class*="sc_button_simple"]):not([class*="sc_button_hover_style_"])\
				'
			).addClass( 'sc_button_hover_just_init sc_button_hover_style_link3' );
			// Remove just init hover class
			setTimeout(
				function() {
					jQuery( '.sc_button_hover_just_init' ).removeClass( 'sc_button_hover_just_init' );
				}, 500
			);
			// Remove hover class
			jQuery(
				'.mejs-controls button,\
				.mfp-close,\
				.sc_button_bg_image,\
				.hover_shop_buttons a,\
				button.pswp__button,\
				.woocommerce-orders-table__cell-order-actions .button,\
				.sc_layouts_row_type_narrow .sc_button\
				'
			).removeClass( 'sc_button_hover_' + AVVENTURE_STORAGE['button_hover'] );

		}

	}
);
/* global jQuery:false */
/* global AVVENTURE_STORAGE:false */
/* global TRX_ADDONS_STORAGE:false */

(function() {
	"use strict";
	
	jQuery(document).on('action.add_googlemap_styles', avventure_trx_addons_add_googlemap_styles);
	jQuery(document).on('action.init_hidden_elements', avventure_trx_addons_init);
	
	// Add theme specific styles to the Google map
	function avventure_trx_addons_add_googlemap_styles(e) {
		if (typeof TRX_ADDONS_STORAGE == 'undefined') return;
		TRX_ADDONS_STORAGE['googlemap_styles']['dark'] = [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"lightness":20},{"color":"#13162b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#5fc6ca"},{"lightness":21}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#cccdd2"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#13162b"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#ff0000"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#13162b"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#13162b"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#f4f9fc"},{"lightness":17}]}];
		TRX_ADDONS_STORAGE['googlemap_styles']['orange'] = [
            {
                "featureType": "administrative",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#444444"
                    }
                ]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [
                    {
                        "color": "#eceae8"
                    }
                ]
            },
            {
                "featureType": "landscape.man_made",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "hue": "#0200ff"
                    },
                    {
                        "saturation": "17"
                    },
                    {
                        "invert_lightness": true
                    },
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "landscape.man_made",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "visibility": "on"
                    },
                    {
                        "saturation": "-83"
                    },
                    {
                        "lightness": "-100"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "lightness": 45
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#dba860"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "all",
                "stylers": [
                    {
                        "color": "#f9fcfd"
                    },
                    {
                        "visibility": "on"
                    }
                ]
            }
        ];
	}
	
	
	function avventure_trx_addons_init(e, container) {
		if (arguments.length < 2) var container = jQuery('body');
		if (container===undefined || container.length === undefined || container.length == 0) return;
		container.find('.sc_countdown_item canvas:not(.inited)').addClass('inited').attr('data-color', AVVENTURE_STORAGE['alter_link_color']);
	}

})();/* global jQuery:false */
/* global AVVENTURE_STORAGE:false */

(function() {
	"use strict";

	jQuery( document ).on(
		'action.ready_avventure', function() {

			// CF7 checkboxes and radio - add class to correct check/uncheck pseudoelement when input at right side of the label
			jQuery( '.wpcf7-checkbox > .wpcf7-list-item > .wpcf7-list-item-label,.wpcf7-radio > .wpcf7-list-item > .wpcf7-list-item-label' ).each(
				function() {
					if (jQuery( this ).next( 'input[type="checkbox"],input[type="radio"]' ).length > 0) {
						jQuery( this ).addClass( 'wpcf7-list-item-right' );
					}
				}
			);
			jQuery( '.wpcf7-checkbox > .wpcf7-list-item > .wpcf7-list-item-label,.wpcf7-radio > .wpcf7-list-item > .wpcf7-list-item-label,.wpcf7-wpgdprc > .wpcf7-list-item > .wpcf7-list-item-label' ).on(
				'click', function() {
					var chk = jQuery( this ).siblings( 'input[type="checkbox"],input[type="radio"]' );
					if (chk.attr( 'type' ) == 'radio') {
						jQuery( this ).parents( '.wpcf7-radio' )
						.find( '.wpcf7-list-item-label' ).removeClass( 'wpcf7-list-item-checked' )
						.find( 'input[type="radio"]' ).each(
							function(){
								this.checked = false;
							}
						);
					}
					if (chk.length > 0) {
						chk.get( 0 ).checked = chk.get( 0 ).checked ? false : true;
						jQuery( this ).toggleClass( 'wpcf7-list-item-checked', chk.get( 0 ).checked );
					}
				}
			);
		}
	);

})();
