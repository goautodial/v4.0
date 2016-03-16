(function($) {
	'use strict';
	$('audio[controls]').before(function(){
		var song = this;
			song.controls=false;
		var player_box = document.createElement('div');
			$(player_box).addClass($(song).attr('class') + ' well container-fluid playa');
		var data_sec = document.createElement('section');
			$(data_sec).addClass('collapse');
		var toggle_holder = document.createElement('div');
			$(toggle_holder).addClass('btn-group row-fluid');
		var data_toggle = document.createElement('a');
			$(data_toggle).html('<i class="icon-reorder"></i>');
			$(data_toggle).addClass('btn btn-block');
			$(data_toggle).attr('style', 'opacity:0.3');
			$(data_toggle).click(function (){$(data_sec).collapse('toggle');});
			$(data_toggle).attr('title', 'Details');
			$(data_toggle).tooltip({'container': 'body', 'placement': 'top', 'html': true});
			$(toggle_holder).append(data_toggle);
		var data_table = document.createElement('table');
			$(data_table).addClass('table table-condensed');
		var player = document.createElement('section');
			$(player).addClass('btn-group row-fluid');
		var load_error = function(){
			console.log('error');
			$(player_box).find('.btn').addClass('disabled');
			$(player_box).find('input[type="range"]').hide();
			$(player_box).find('.icon-spin').text('Error');
			$(player_box).find('.icon-spin').parent().attr('title', 'There was an error loading the audio.');
			$(player_box).find('.icon-spin').parent().tooltip('fixTitle');
			$(player_box).find('.icon-spin').removeClass('icon-spinner icon-spin');
		};
		var addPlay = function() {
			var play = document.createElement('button');
				$(play).addClass('btn disabled span1');
			play.setPlayState = function(toggle){
					$(play).removeClass('disabled');
				if (toggle === 'play') {
					$(play).html('<i class="icon-play"></i>');
					$(play).click(function () {
						song.play();
					});
				}
				if (toggle === 'pause') {
					$(play).html('<i class="icon-pause"></i>');
					$(play).click(function () {
						song.pause();
					});
				}
			};
			$(song).on('play', function(){play.setPlayState('pause');});
			$(song).on('canplay', function(){play.setPlayState('play');});
			$(song).on('pause', function(){play.setPlayState('play');});
			var timeout = 0;
			var loadCheck = setInterval(function() {
				if(isNaN(song.duration) === false){
					play.setPlayState('play');
					clearInterval(loadCheck);
					return true;
				}
				if(song.networkState === 3 || timeout === 75){
					load_error();
					clearInterval(loadCheck);
					return false;
				}
				timeout++;
			}, 50);
			
			$(player).append(play);
		};
		var addSeek = function() {
			var seek = document.createElement('input');
				$(seek).attr({
					'type': 'range',
					'min': 0,
					'value': 0,
					'class': 'seek'
				});
			seek.progress = function () {
				var bg = 'rgba(223, 240, 216, 1) 0%';
				bg += ', rgba(223, 240, 216, 1) ' + ((song.currentTime/song.duration) * 100) + '%';
				bg += ', rgba(223, 240, 216, 0) ' + ((song.currentTime/song.duration) * 100) + '%';
				for (var i=0; i<song.buffered.length; i++){
					if (song.buffered.end(i) > song.currentTime && isNaN(song.buffered.end(i)) === false && isNaN(song.buffered.start(i)) === false){
						var bufferedstart;
						var bufferedend;
						if (song.buffered.end(i) < song.duration) {
							bufferedend = ((song.buffered.end(i)/song.duration) * 100);
						}
						else {
							bufferedend = 100;
						}
						if (song.buffered.start(i) > song.currentTime){
							bufferedstart = ((song.buffered.start(i)/song.duration) * 100);
						}
						else {
							bufferedstart = ((song.currentTime/song.duration) * 100);
						}
						bg += ', rgba(217, 237, 247, 0) ' + bufferedstart + '%';
						bg += ', rgba(217, 237, 247, 1) ' + bufferedstart + '%';
						bg += ', rgba(217, 237, 247, 1) ' + bufferedend + '%';
						bg += ', rgba(217, 237, 247, 0) ' + bufferedend + '%';
					}						
				}
				$(seek).css('background', '-webkit-linear-gradient(left, ' + bg + ')');
				//These may be re-enabled when/if other browsers support the background like webkit
				//$(seek).css('background','-o-linear-gradient(left,  ' + bg + ')');
				//$(seek).css('background','-moz-linear-gradient(left,  ' + bg + ')');
				//$(seek).css('background','-ms-linear-gradient(left,  ' + bg + ')');
				//$(seek).css('background','linear-gradient(to right,  ' + bg + ')');
				$(seek).css('background-color', '#ddd');
			};
			seek.set = function () {
				$(seek).val(song.currentTime);
				seek.progress();
			};
			seek.slide = function () {
				song.currentTime = $(seek).val();
				seek.progress();
			};
			seek.init = function () {
				$(seek).attr({
					'max': song.duration,
					'step': song.duration / 100
				});
				seek.set();
			};
			seek.reset = function () {
				$(seek).val(0);
				song.currentTime = $(seek).val();
				if(!song.loop){song.pause();}
				else {song.play();}
			};
			var seek_wrapper = document.createElement('div');
				$(seek_wrapper).addClass('btn disabled span4');

			$(seek_wrapper).append(seek);
			$(seek).on('change', seek.slide);
			$(song).on('timeupdate', seek.init);
			$(song).on('loadedmetadata', seek.init);
			$(song).on('loadeddata', seek.init);
			$(song).on('progress', seek.init);
			$(song).on('canplay', seek.init);
			$(song).on('canplaythrough', seek.init);
			$(song).on('ended', seek.reset);
			if(song.readyState > 0){
				seek.init();
			}
			$(player).append(seek_wrapper);
		};
		var addTime = function() {
			var time = document.createElement('a');
				$(time).addClass('btn span3');
				$(time).tooltip({'container': 'body', 'placement': 'right', 'html': true});
			time.twodigit = function (myNum) {
				return ("0" + myNum).slice(-2);
			};
			time.timesplit = function (a) {
				if (isNaN(a)){return '<i class="icon-spinner icon-spin"></i>';}
				var hours = Math.floor(a / 3600);
				var minutes = Math.floor(a / 60) - (hours * 60);
				var seconds = Math.floor(a) - (hours * 3600) - (minutes * 60);
				var timeStr = time.twodigit(minutes) + ':' + time.twodigit(seconds);
				if (hours > 0) {
					timeStr = hours + ':' + timeStr;
				}
				return timeStr;
			};
			time.showtime = function () {
				$(time).html(time.timesplit(song.duration));
				$(time).attr({'title': 'Click to Reset<hr style="padding:0; margin:0;" />Position: ' + (time.timesplit(song.currentTime))});
				if (!song.paused){
					$(time).html(time.timesplit(song.currentTime));
					$(time).attr({'title': 'Click to Reset<hr style="padding:0; margin:0;" />Length: ' + (time.timesplit(song.duration))});
				}
				$(time).tooltip('fixTitle');
			};
			$(time).click(function () {
				song.pause();
				song.currentTime = 0;
				time.showtime();
				$(time).tooltip('fixTitle');
				$(time).tooltip('show');
			});
			$(time).tooltip('show');
			$(song).on('loadedmetadata', time.showtime);
			$(song).on('loadeddata', time.showtime);
			$(song).on('progress', time.showtime);
			$(song).on('canplay', time.showtime);
			$(song).on('canplaythrough', time.showtime);
			$(song).on('timeupdate', time.showtime);
			if(song.readyState > 0){
				time.showtime();
			}
			else {
				$(time).html('<i class="icon-spinner icon-spin"></i>');
			}
			$(player).append(time);
		};
		var addMute = function() {
			var mute = document.createElement('button');
				$(mute).addClass('btn span1');
			mute.checkVolume = function () {
				if (song.volume > 0.5 && !song.muted) {
					$(mute).html('<i class="icon-volume-up"></i>');
				} else if (song.volume < 0.5 && song.volume > 0 && !song.muted) {
					$(mute).html('<i class="icon-volume-down"></i>');
				} else {
					$(mute).html('<i class="icon-volume-off"></i>');
				}
			};
			$(mute).click(function () {
				if (song.muted) {
					song.muted = false;
					song.volume = song.oldvolume;
				} else {
					song.muted = true;
					song.oldvolume = song.volume;
					song.volume = 0;
				}
				mute.checkVolume();
			});
			mute.checkVolume();
			$(song).on('volumechange', mute.checkVolume);
			$(player).append(mute);
		};
		var addVolume = function() {
			var volume = document.createElement('input');
				$(volume).attr({
					'type': 'range',
					'min': 0,
					'max': 1,
					'step': 1 / 100,
					'value': 1
				});
			volume.slide = function () {
				song.muted = false;
				song.volume = $(volume).val();
			};
			volume.set = function () {
				$(volume).val(song.volume);
			};
			var vol_wrapper = document.createElement('div');
				$(vol_wrapper).addClass('btn disabled span3');
			$(vol_wrapper).append(volume);
			$(volume).on("change", volume.slide);
			$(song).on('volumechange', volume.set);
			$(player).append(vol_wrapper);
		};
		var addAlbumArt = function() {
			var albumArt = document.createElement('img');
				$(albumArt).addClass('thumbnail');
				$(albumArt).attr('src', $(song).data('infoAlbumArt'));
			$(data_sec).append(albumArt);
		};
		var addInfo = function(title, dataId) {
			var row = document.createElement('tr');
			var head = document.createElement('th');
			var data = document.createElement('td');
			$(head).html(title);
			$(data).html($(song).data(dataId));
			$(row).append(head);
			$(row).append(data);
			$(data_table).append(row);
		};
		var addData = function() {
			if (typeof($(song).data('infoAlbumArt')) !== 'undefined'){ addAlbumArt();}
			if (typeof($(song).data('infoArtist')) !== 'undefined'){ addInfo('Artist', 'infoArtist');}
			if (typeof($(song).data('infoTitle')) !== 'undefined'){ addInfo('Title', 'infoTitle');}
			if (typeof($(song).data('infoAlbumTitle')) !== 'undefined'){ addInfo('Album', 'infoAlbumTitle');}
			if (typeof($(song).data('infoLabel')) !== 'undefined'){ addInfo('Label', 'infoLabel');}
			if (typeof($(song).data('infoYear')) !== 'undefined'){ addInfo('Year', 'infoYear');}
			if ($(data_table).html() !== ""){
				$(data_sec).append(data_table);
				$(player_box).append(toggle_holder);
				$(player_box).append(data_sec);
			}
		};
		var addPlayer = function() {
			if ($(song).data('play') !== 'off'){ addPlay();}
			if ($(song).data('seek') !== 'off'){ addSeek();}
			if ($(song).data('time') !== 'off'){ addTime();}
			if ($(song).data('mute') !== 'off'){ addMute();}
			if ($(song).data('volume') !== 'off'){ addVolume();}
			$(player_box).append(player);
		};
		var addAttribution = function() {
			var attribution = document.createElement('small');
				$(attribution).addClass('pull-right muted');
			if (typeof($(song).data('infoAttLink')) !== 'undefined'){
				var attribution_link = document.createElement('a');
					$(attribution_link).addClass('muted');
					$(attribution_link).attr('href', $(song).data('infoAttLink'));
					$(attribution_link).html($(song).data('infoAtt'));
				$(attribution).append(attribution_link);
			}
			else {
				$(attribution).html($(song).data('infoAtt'));
			}
			$(player_box).append(attribution);
		};
		var fillPlayerBox = function() {
			addData();
			addPlayer();
			if (typeof($(song).data('infoAtt')) !== 'undefined'){ addAttribution();}
		};
		fillPlayerBox();
		$(song).on('error', function(){
			load_error();
		});
		return player_box;
	});
})(jQuery)