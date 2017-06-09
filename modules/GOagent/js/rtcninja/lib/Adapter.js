'use strict';

// Expose the Adapter function/object.
module.exports = Adapter;


// Dependencies

var browser = require('bowser'),
	debug = require('debug')('rtcninja:Adapter'),
	debugerror = require('debug')('rtcninja:ERROR:Adapter'),

	// Internal vars
	getUserMedia = null,
	mediaDevices = null,
	RTCPeerConnection = null,
	RTCSessionDescription = null,
	RTCIceCandidate = null,
	MediaStreamTrack = null,
	getMediaDevices = null,
	attachMediaStream = null,
	canRenegotiate = false,
	oldSpecRTCOfferOptions = false,
	browserVersion = Number(browser.version) || 0,
	isDesktop = !!(!browser.mobile && (!browser.tablet || (browser.msie && browserVersion >= 10))),
	hasWebRTC = false,
	// Dirty trick to get this library working in a Node-webkit env with browserified libs
	virtGlobal = global.window || global,
	// Don't fail in Node
	virtNavigator = virtGlobal.navigator || {};

debugerror.log = console.warn.bind(console);


// Constructor.

function Adapter(options) {
	// Chrome desktop, Chrome Android, Opera desktop, Opera Android, Android native browser
	// or generic Webkit browser.
	if (
		(isDesktop && browser.chrome && browserVersion >= 32) ||
		(browser.android && browser.chrome && browserVersion >= 39) ||
		(isDesktop && browser.opera && browserVersion >= 27) ||
		(browser.android && browser.opera && browserVersion >= 24) ||
		(browser.android && browser.webkit && !browser.chrome && browserVersion >= 37) ||
		(virtNavigator.webkitGetUserMedia && virtGlobal.webkitRTCPeerConnection)
	) {
		hasWebRTC = true;
		getUserMedia = virtNavigator.webkitGetUserMedia.bind(virtNavigator);
		mediaDevices = virtNavigator.mediaDevices;
		RTCPeerConnection = virtGlobal.webkitRTCPeerConnection;
		RTCSessionDescription = virtGlobal.RTCSessionDescription;
		RTCIceCandidate = virtGlobal.RTCIceCandidate;
		MediaStreamTrack = virtGlobal.MediaStreamTrack;
		if (MediaStreamTrack && MediaStreamTrack.getSources) {
			getMediaDevices = MediaStreamTrack.getSources.bind(MediaStreamTrack);
		} else if (virtNavigator.getMediaDevices) {
			getMediaDevices = virtNavigator.getMediaDevices.bind(virtNavigator);
		}
		attachMediaStream = function (element, stream) {
			element.src = URL.createObjectURL(stream);
			return element;
		};
		canRenegotiate = true;
		oldSpecRTCOfferOptions = false;
	// Old Firefox desktop, old Firefox Android.
	} else if (
		(browser.firefox && browserVersion < 47) ||
		(virtNavigator.mozGetUserMedia && virtGlobal.mozRTCPeerConnection)
	) {
		hasWebRTC = true;
		getUserMedia = virtNavigator.mozGetUserMedia.bind(virtNavigator);
		mediaDevices = virtNavigator.mediaDevices;
		RTCPeerConnection = virtGlobal.mozRTCPeerConnection;
		RTCSessionDescription = virtGlobal.mozRTCSessionDescription;
		RTCIceCandidate = virtGlobal.mozRTCIceCandidate;
		MediaStreamTrack = virtGlobal.MediaStreamTrack;
		attachMediaStream = function (element, stream) {
			element.src = URL.createObjectURL(stream);
			return element;
		};
		canRenegotiate = false;
		oldSpecRTCOfferOptions = false;
	// Modern Firefox desktop, modern Firefox Android.
	} else if (
		((browser.firefox || browser.gecko) && browserVersion >= 47)
	) {
		hasWebRTC = true;
		getUserMedia = virtNavigator.mozGetUserMedia.bind(virtNavigator);
		mediaDevices = virtNavigator.mediaDevices;
		RTCPeerConnection = virtGlobal.RTCPeerConnection;
		RTCSessionDescription = virtGlobal.RTCSessionDescription;
		RTCIceCandidate = virtGlobal.RTCIceCandidate;
		MediaStreamTrack = virtGlobal.MediaStreamTrack;
		attachMediaStream = function (element, stream) {
			element.src = URL.createObjectURL(stream);
			return element;
		};
		canRenegotiate = false;
		oldSpecRTCOfferOptions = false;
		// WebRTC plugin required. For example IE or Safari with the Temasys plugin.
	} else if (
		options.plugin &&
		typeof options.plugin.isRequired === 'function' &&
		options.plugin.isRequired() &&
		typeof options.plugin.isInstalled === 'function' &&
		options.plugin.isInstalled()
	) {
		var pluginiface = options.plugin.interface;

		hasWebRTC = true;
		getUserMedia = pluginiface.getUserMedia;
		mediaDevices = pluginiface.mediaDevices;
		RTCPeerConnection = pluginiface.RTCPeerConnection;
		RTCSessionDescription = pluginiface.RTCSessionDescription;
		RTCIceCandidate = pluginiface.RTCIceCandidate;
		MediaStreamTrack = pluginiface.MediaStreamTrack;
		if (MediaStreamTrack && MediaStreamTrack.getSources) {
			getMediaDevices = MediaStreamTrack.getSources.bind(MediaStreamTrack);
		} else if (virtNavigator.getMediaDevices) {
			getMediaDevices = virtNavigator.getMediaDevices.bind(virtNavigator);
		}
		attachMediaStream = pluginiface.attachMediaStream;
		canRenegotiate = pluginiface.canRenegotiate;
		oldSpecRTCOfferOptions = true;
	// Best effort (may be adater.js is loaded).
	} else if (virtNavigator.getUserMedia && virtGlobal.RTCPeerConnection) {
		hasWebRTC = true;
		getUserMedia = virtNavigator.getUserMedia.bind(virtNavigator);
		mediaDevices = virtNavigator.mediaDevices;
		RTCPeerConnection = virtGlobal.RTCPeerConnection;
		RTCSessionDescription = virtGlobal.RTCSessionDescription;
		RTCIceCandidate = virtGlobal.RTCIceCandidate;
		MediaStreamTrack = virtGlobal.MediaStreamTrack;
		if (MediaStreamTrack && MediaStreamTrack.getSources) {
			getMediaDevices = MediaStreamTrack.getSources.bind(MediaStreamTrack);
		} else if (virtNavigator.getMediaDevices) {
			getMediaDevices = virtNavigator.getMediaDevices.bind(virtNavigator);
		}
		attachMediaStream = virtGlobal.attachMediaStream || function (element, stream) {
			element.src = URL.createObjectURL(stream);
			return element;
		};
		canRenegotiate = true;
		oldSpecRTCOfferOptions = false;
	}


	function throwNonSupported(item) {
		return function () {
			throw new Error('rtcninja: WebRTC not supported, missing ' + item +
			' [browser: ' + browser.name + ' ' + browser.version + ']');
		};
	}


	// Public API.

	// Expose a WebRTC checker.
	Adapter.hasWebRTC = function () {
		return hasWebRTC;
	};

	// Expose getUserMedia.
	if (getUserMedia) {
		Adapter.getUserMedia = function (constraints, successCallback, errorCallback) {
			debug('getUserMedia() | constraints: %o', constraints);

			try {
				getUserMedia(constraints,
					function (stream) {
						debug('getUserMedia() | success');
						if (successCallback) {
							successCallback(stream);
						}
					},
					function (error) {
						debug('getUserMedia() | error:', error);
						if (errorCallback) {
							errorCallback(error);
						}
					}
				);
			}
			catch (error) {
				debugerror('getUserMedia() | error:', error);
				if (errorCallback) {
					errorCallback(error);
				}
			}
		};
	} else {
		Adapter.getUserMedia = function (constraints, successCallback, errorCallback) {
			debugerror('getUserMedia() | WebRTC not supported');
			if (errorCallback) {
				errorCallback(new Error('rtcninja: WebRTC not supported, missing ' +
				'getUserMedia [browser: ' + browser.name + ' ' + browser.version + ']'));
			} else {
				throwNonSupported('getUserMedia');
			}
		};
	}

	// Expose mediaDevices.
	Adapter.mediaDevices = mediaDevices;

	// Expose RTCPeerConnection.
	Adapter.RTCPeerConnection = RTCPeerConnection || throwNonSupported('RTCPeerConnection');

	// Expose RTCSessionDescription.
	Adapter.RTCSessionDescription = RTCSessionDescription || throwNonSupported('RTCSessionDescription');

	// Expose RTCIceCandidate.
	Adapter.RTCIceCandidate = RTCIceCandidate || throwNonSupported('RTCIceCandidate');

	// Expose MediaStreamTrack.
	Adapter.MediaStreamTrack = MediaStreamTrack || throwNonSupported('MediaStreamTrack');

	// Expose getMediaDevices.
	Adapter.getMediaDevices = getMediaDevices;

	// Expose MediaStreamTrack.
	Adapter.attachMediaStream = attachMediaStream || throwNonSupported('attachMediaStream');

	// Expose canRenegotiate attribute.
	Adapter.canRenegotiate = canRenegotiate;

	// Expose closeMediaStream.
	Adapter.closeMediaStream = function (stream) {
		if (!stream) {
			return;
		}

		// Latest spec states that MediaStream has no stop() method and instead must
		// call stop() on every MediaStreamTrack.
		try {
			debug('closeMediaStream() | calling stop() on all the MediaStreamTrack');

			var tracks, i, len;

			if (stream.getTracks) {
				tracks = stream.getTracks();
				for (i = 0, len = tracks.length; i < len; i += 1) {
					tracks[i].stop();
				}
			} else {
				tracks = stream.getAudioTracks();
				for (i = 0, len = tracks.length; i < len; i += 1) {
					tracks[i].stop();
				}
				tracks = stream.getVideoTracks();
				for (i = 0, len = tracks.length; i < len; i += 1) {
					tracks[i].stop();
				}
			}
		} catch (error) {
			// Deprecated by the spec, but still in use.
			// NOTE: In Temasys IE plugin stream.stop is a callable 'object'.
			if (typeof stream.stop === 'function' || typeof stream.stop === 'object') {
				debug('closeMediaStream() | calling stop() on the MediaStream');

				stream.stop();
			}
		}
	};

	// Expose fixPeerConnectionConfig.
	Adapter.fixPeerConnectionConfig = function (pcConfig) {
		var i, len, iceServer, hasUrls, hasUrl;

		if (!Array.isArray(pcConfig.iceServers)) {
			pcConfig.iceServers = [];
		}

		for (i = 0, len = pcConfig.iceServers.length; i < len; i += 1) {
			iceServer = pcConfig.iceServers[i];
			hasUrls = iceServer.hasOwnProperty('urls');
			hasUrl = iceServer.hasOwnProperty('url');

			if (typeof iceServer === 'object') {
				// Has .urls but not .url, so add .url with a single string value.
				if (hasUrls && !hasUrl) {
					iceServer.url = (Array.isArray(iceServer.urls) ? iceServer.urls[0] : iceServer.urls);
				// Has .url but not .urls, so add .urls with same value.
				} else if (!hasUrls && hasUrl) {
					iceServer.urls = (Array.isArray(iceServer.url) ? iceServer.url.slice() : iceServer.url);
				}

				// Ensure .url is a single string.
				if (hasUrl && Array.isArray(iceServer.url)) {
					iceServer.url = iceServer.url[0];
				}
			}
		}
	};

	// Expose fixRTCOfferOptions.
	Adapter.fixRTCOfferOptions = function (options) {
		options = options || {};

		// New spec.
		if (!oldSpecRTCOfferOptions) {
			if (options.mandatory && options.mandatory.hasOwnProperty('OfferToReceiveAudio')) {
				options.offerToReceiveAudio = options.mandatory.OfferToReceiveAudio ? 1 : 0;
			}
			if (options.mandatory && options.mandatory.hasOwnProperty('OfferToReceiveVideo')) {
				options.offerToReceiveVideo = options.mandatory.OfferToReceiveVideo ? 1 : 0;
			}
			delete options.mandatory;
		// Old spec.
		} else {
			if (options.hasOwnProperty('offerToReceiveAudio')) {
				options.mandatory = options.mandatory || {};
				options.mandatory.OfferToReceiveAudio = options.offerToReceiveAudio ? true : false;
			}
			if (options.hasOwnProperty('offerToReceiveVideo')) {
				options.mandatory = options.mandatory || {};
				options.mandatory.OfferToReceiveVideo = options.offerToReceiveVideo ? true : false;
			}
		}
	};

	return Adapter;
}
