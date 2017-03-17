/*
 * rtcninja.js v0.7.0
 * WebRTC API wrapper to deal with different browsers
 * Copyright 2016 Iñaki Baz Castillo <inaki.baz@eface2face.com> (http://eface2face.com)
 * License MIT
 */

(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.rtcninja = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"bowser":5,"debug":6}],2:[function(require,module,exports){
'use strict';

// Expose the RTCPeerConnection class.
module.exports = RTCPeerConnection;


// Dependencies.

var merge = require('merge'),
	debug = require('debug')('rtcninja:RTCPeerConnection'),
	debugerror = require('debug')('rtcninja:ERROR:RTCPeerConnection'),
	Adapter = require('./Adapter'),

	// Internal constants.
	C = {
		REGEXP_NORMALIZED_CANDIDATE: new RegExp(/^candidate:/i),
		REGEXP_FIX_CANDIDATE: new RegExp(/(^a=|\r|\n)/gi),
		REGEXP_RELAY_CANDIDATE: new RegExp(/ relay /i),
		REGEXP_SDP_CANDIDATES: new RegExp(/^a=candidate:.*\r\n/igm),
		REGEXP_SDP_NON_RELAY_CANDIDATES: new RegExp(/^a=candidate:(.(?!relay ))*\r\n/igm)
	},

	// Internal variables.
	VAR = {
		normalizeCandidate: null
	};

debugerror.log = console.warn.bind(console);


// Constructor

function RTCPeerConnection(pcConfig, pcConstraints) {
	debug('new | [pcConfig:%o, pcConstraints:%o]', pcConfig, pcConstraints);

	// Set this.pcConfig and this.options.
	setConfigurationAndOptions.call(this, pcConfig);

	// NOTE: Deprecated pcConstraints argument.
	this.pcConstraints = pcConstraints;

	// Own version of the localDescription.
	this.ourLocalDescription = null;

	// Latest values of PC attributes to avoid events with same value.
	this.ourSignalingState = null;
	this.ourIceConnectionState = null;
	this.ourIceGatheringState = null;

	// Timer for options.gatheringTimeout.
	this.timerGatheringTimeout = null;

	// Timer for options.gatheringTimeoutAfterRelay.
	this.timerGatheringTimeoutAfterRelay = null;

	// Flag to ignore new gathered ICE candidates.
	this.ignoreIceGathering = false;

	// Flag set when closed.
	this.closed = false;

	// Set RTCPeerConnection.
	setPeerConnection.call(this);

	// Set properties.
	setProperties.call(this);
}


// Public API.

RTCPeerConnection.prototype.createOffer = function (successCallback, failureCallback, options) {
	debug('createOffer()');

	var self = this;

	Adapter.fixRTCOfferOptions(options);

	this.pc.createOffer(
		function (offer) {
			if (isClosed.call(self)) {
				return;
			}
			debug('createOffer() | success');
			if (successCallback) {
				successCallback(offer);
			}
		},
		function (error) {
			if (isClosed.call(self)) {
				return;
			}
			debugerror('createOffer() | error:', error);
			if (failureCallback) {
				failureCallback(error);
			}
		},
		options
	);
};


RTCPeerConnection.prototype.createAnswer = function (successCallback, failureCallback, options) {
	debug('createAnswer()');

	var self = this;

	this.pc.createAnswer(
		function (answer) {
			if (isClosed.call(self)) {
				return;
			}
			debug('createAnswer() | success');
			if (successCallback) {
				successCallback(answer);
			}
		},
		function (error) {
			if (isClosed.call(self)) {
				return;
			}
			debugerror('createAnswer() | error:', error);
			if (failureCallback) {
				failureCallback(error);
			}
		},
		options
	);
};


RTCPeerConnection.prototype.setLocalDescription = function (description, successCallback, failureCallback) {
	debug('setLocalDescription()');

	var self = this;

	this.pc.setLocalDescription(
		description,
		// success.
		function () {
			if (isClosed.call(self)) {
				return;
			}
			debug('setLocalDescription() | success');

			// Clear gathering timers.
			clearTimeout(self.timerGatheringTimeout);
			delete self.timerGatheringTimeout;
			clearTimeout(self.timerGatheringTimeoutAfterRelay);
			delete self.timerGatheringTimeoutAfterRelay;

			runTimerGatheringTimeout();
			if (successCallback) {
				successCallback();
			}
		},
		// failure
		function (error) {
			if (isClosed.call(self)) {
				return;
			}
			debugerror('setLocalDescription() | error:', error);
			if (failureCallback) {
				failureCallback(error);
			}
		}
	);

	// Enable (again) ICE gathering.
	this.ignoreIceGathering = false;

	// Handle gatheringTimeout.
	function runTimerGatheringTimeout() {
		if (typeof self.options.gatheringTimeout !== 'number') {
			return;
		}
		// If setLocalDescription was already called, it may happen that
		// ICE gathering is not needed, so don't run this timer.
		if (self.pc.iceGatheringState === 'complete') {
			return;
		}

		debug('setLocalDescription() | ending gathering in %d ms (gatheringTimeout option)',
			self.options.gatheringTimeout);

		self.timerGatheringTimeout = setTimeout(function () {
			if (isClosed.call(self)) {
				return;
			}

			debug('forced end of candidates after gatheringTimeout timeout');

			// Clear gathering timers.
			delete self.timerGatheringTimeout;
			clearTimeout(self.timerGatheringTimeoutAfterRelay);
			delete self.timerGatheringTimeoutAfterRelay;

			// Ignore new candidates.
			self.ignoreIceGathering = true;
			if (self.onicecandidate) {
				self.onicecandidate({ candidate: null }, null);
			}

		}, self.options.gatheringTimeout);
	}
};


RTCPeerConnection.prototype.setRemoteDescription = function (description, successCallback, failureCallback) {
	debug('setRemoteDescription()');

	var self = this;

	this.pc.setRemoteDescription(
		description,
		function () {
			if (isClosed.call(self)) {
				return;
			}
			debug('setRemoteDescription() | success');
			if (successCallback) {
				successCallback();
			}
		},
		function (error) {
			if (isClosed.call(self)) {
				return;
			}
			debugerror('setRemoteDescription() | error:', error);
			if (failureCallback) {
				failureCallback(error);
			}
		}
	);
};


RTCPeerConnection.prototype.updateIce = function (pcConfig) {
	debug('updateIce() | pcConfig: %o', pcConfig);

	// Update this.pcConfig and this.options.
	setConfigurationAndOptions.call(this, pcConfig);

	this.pc.updateIce(this.pcConfig);

	// Enable (again) ICE gathering.
	this.ignoreIceGathering = false;
};


RTCPeerConnection.prototype.addIceCandidate = function (candidate, successCallback, failureCallback) {
	debug('addIceCandidate() | candidate: %o', candidate);

	var self = this;

	this.pc.addIceCandidate(
		candidate,
		function () {
			if (isClosed.call(self)) {
				return;
			}
			debug('addIceCandidate() | success');
			if (successCallback) {
				successCallback();
			}
		},
		function (error) {
			if (isClosed.call(self)) {
				return;
			}
			debugerror('addIceCandidate() | error:', error);
			if (failureCallback) {
				failureCallback(error);
			}
		}
	);
};


RTCPeerConnection.prototype.getConfiguration = function () {
	debug('getConfiguration()');

	return this.pc.getConfiguration();
};


RTCPeerConnection.prototype.getLocalStreams = function () {
	debug('getLocalStreams()');

	return this.pc.getLocalStreams();
};


RTCPeerConnection.prototype.getRemoteStreams = function () {
	debug('getRemoteStreams()');

	return this.pc.getRemoteStreams();
};


RTCPeerConnection.prototype.getStreamById = function (streamId) {
	debug('getStreamById() | streamId: %s', streamId);

	return this.pc.getStreamById(streamId);
};


RTCPeerConnection.prototype.addStream = function (stream) {
	debug('addStream() | stream: %s', stream);

	this.pc.addStream(stream);
};


RTCPeerConnection.prototype.removeStream = function (stream) {
	debug('removeStream() | stream: %o', stream);

	this.pc.removeStream(stream);
};


RTCPeerConnection.prototype.close = function () {
	debug('close()');

	this.closed = true;

	// Clear gathering timers.
	clearTimeout(this.timerGatheringTimeout);
	delete this.timerGatheringTimeout;
	clearTimeout(this.timerGatheringTimeoutAfterRelay);
	delete this.timerGatheringTimeoutAfterRelay;

	this.pc.close();
};


RTCPeerConnection.prototype.createDataChannel = function () {
	debug('createDataChannel()');

	return this.pc.createDataChannel.apply(this.pc, arguments);
};


RTCPeerConnection.prototype.createDTMFSender = function (track) {
	debug('createDTMFSender()');

	if (this.pc.createDTMFSender) {
		return this.pc.createDTMFSender(track);
	} else {
		return null;
	}
};


RTCPeerConnection.prototype.getStats = function () {
	debug('getStats()');

	return this.pc.getStats.apply(this.pc, arguments);
};


RTCPeerConnection.prototype.setIdentityProvider = function () {
	debug('setIdentityProvider()');

	return this.pc.setIdentityProvider.apply(this.pc, arguments);
};


RTCPeerConnection.prototype.getIdentityAssertion = function () {
	debug('getIdentityAssertion()');

	return this.pc.getIdentityAssertion();
};


RTCPeerConnection.prototype.reset = function (pcConfig) {
	debug('reset() | pcConfig: %o', pcConfig);

	var pc = this.pc;

	// Remove events in the old PC.
	pc.onnegotiationneeded = null;
	pc.onicecandidate = null;
	pc.onaddstream = null;
	pc.onremovestream = null;
	pc.ondatachannel = null;
	pc.onsignalingstatechange = null;
	pc.oniceconnectionstatechange = null;
	pc.onicegatheringstatechange = null;
	pc.onidentityresult = null;
	pc.onpeeridentity = null;
	pc.onidpassertionerror = null;
	pc.onidpvalidationerror = null;

	// Clear gathering timers.
	clearTimeout(this.timerGatheringTimeout);
	delete this.timerGatheringTimeout;
	clearTimeout(this.timerGatheringTimeoutAfterRelay);
	delete this.timerGatheringTimeoutAfterRelay;

	// Silently close the old PC.
	debug('reset() | closing current peerConnection');
	pc.close();

	// Set this.pcConfig and this.options.
	setConfigurationAndOptions.call(this, pcConfig);

	// Create a new PC.
	setPeerConnection.call(this);
};


// Private Helpers.

function setConfigurationAndOptions(pcConfig) {
	// Clone pcConfig.
	this.pcConfig = merge(true, pcConfig);

	// Fix pcConfig.
	Adapter.fixPeerConnectionConfig(this.pcConfig);

	this.options = {
		iceTransportsRelay: (this.pcConfig.iceTransports === 'relay'),
		iceTransportsNone: (this.pcConfig.iceTransports === 'none'),
		gatheringTimeout: this.pcConfig.gatheringTimeout,
		gatheringTimeoutAfterRelay: this.pcConfig.gatheringTimeoutAfterRelay
	};

	// Remove custom rtcninja.RTCPeerConnection options from pcConfig.
	delete this.pcConfig.gatheringTimeout;
	delete this.pcConfig.gatheringTimeoutAfterRelay;

	debug('setConfigurationAndOptions | processed pcConfig: %o', this.pcConfig);
}


function isClosed() {
	return ((this.closed) || (this.pc && this.pc.iceConnectionState === 'closed'));
}


function setEvents() {
	var self = this,
		pc = this.pc;

	pc.onnegotiationneeded = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('onnegotiationneeded()');
		if (self.onnegotiationneeded) {
			self.onnegotiationneeded(event);
		}
	};

	pc.onicecandidate = function (event) {
		var candidate, isRelay, newCandidate;

		if (isClosed.call(self)) {
			return;
		}
		if (self.ignoreIceGathering) {
			return;
		}

		// Ignore any candidate (event the null one) if iceTransports:'none' is set.
		if (self.options.iceTransportsNone) {
			return;
		}

		candidate = event.candidate;

		if (candidate) {
			isRelay = C.REGEXP_RELAY_CANDIDATE.test(candidate.candidate);

			// Ignore if just relay candidates are requested.
			if (self.options.iceTransportsRelay && !isRelay) {
				return;
			}

			// Handle gatheringTimeoutAfterRelay.
			if (isRelay && !self.timerGatheringTimeoutAfterRelay &&
				(typeof self.options.gatheringTimeoutAfterRelay === 'number')) {
				debug('onicecandidate() | first relay candidate found, ending gathering in %d ms', self.options.gatheringTimeoutAfterRelay);

				self.timerGatheringTimeoutAfterRelay = setTimeout(function () {
					if (isClosed.call(self)) {
						return;
					}

					debug('forced end of candidates after timeout');

					// Clear gathering timers.
					delete self.timerGatheringTimeoutAfterRelay;
					clearTimeout(self.timerGatheringTimeout);
					delete self.timerGatheringTimeout;

					// Ignore new candidates.
					self.ignoreIceGathering = true;
					if (self.onicecandidate) {
						self.onicecandidate({candidate: null}, null);
					}
				}, self.options.gatheringTimeoutAfterRelay);
			}

			newCandidate = new Adapter.RTCIceCandidate({
				sdpMid: candidate.sdpMid,
				sdpMLineIndex: candidate.sdpMLineIndex,
				candidate: candidate.candidate
			});

			// Force correct candidate syntax (just check it once).
			if (VAR.normalizeCandidate === null) {
				if (C.REGEXP_NORMALIZED_CANDIDATE.test(candidate.candidate)) {
					VAR.normalizeCandidate = false;
				} else {
					debug('onicecandidate() | normalizing ICE candidates syntax (remove "a=" and "\\r\\n")');
					VAR.normalizeCandidate = true;
				}
			}
			if (VAR.normalizeCandidate) {
				newCandidate.candidate = candidate.candidate.replace(C.REGEXP_FIX_CANDIDATE, '');
			}

			debug(
				'onicecandidate() | m%d(%s) %s',
				newCandidate.sdpMLineIndex,
				newCandidate.sdpMid || 'no mid', newCandidate.candidate);
			if (self.onicecandidate) {
				self.onicecandidate(event, newCandidate);
			}
		// Null candidate (end of candidates).
		} else {
			debug('onicecandidate() | end of candidates');

			// Clear gathering timers.
			clearTimeout(self.timerGatheringTimeout);
			delete self.timerGatheringTimeout;
			clearTimeout(self.timerGatheringTimeoutAfterRelay);
			delete self.timerGatheringTimeoutAfterRelay;
			if (self.onicecandidate) {
				self.onicecandidate(event, null);
			}
		}
	};

	pc.onaddstream = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('onaddstream() | stream: %o', event.stream);
		if (self.onaddstream) {
			self.onaddstream(event, event.stream);
		}
	};

	pc.onremovestream = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('onremovestream() | stream: %o', event.stream);
		if (self.onremovestream) {
			self.onremovestream(event, event.stream);
		}
	};

	pc.ondatachannel = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('ondatachannel() | datachannel: %o', event.channel);
		if (self.ondatachannel) {
			self.ondatachannel(event, event.channel);
		}
	};

	pc.onsignalingstatechange = function (event) {
		if (pc.signalingState === self.ourSignalingState) {
			return;
		}

		debug('onsignalingstatechange() | signalingState: %s', pc.signalingState);
		self.ourSignalingState = pc.signalingState;
		if (self.onsignalingstatechange) {
			self.onsignalingstatechange(event, pc.signalingState);
		}
	};

	pc.oniceconnectionstatechange = function (event) {
		if (pc.iceConnectionState === self.ourIceConnectionState) {
			return;
		}

		debug('oniceconnectionstatechange() | iceConnectionState: %s', pc.iceConnectionState);
		self.ourIceConnectionState = pc.iceConnectionState;
		if (self.oniceconnectionstatechange) {
			self.oniceconnectionstatechange(event, pc.iceConnectionState);
		}
	};

	pc.onicegatheringstatechange = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		if (pc.iceGatheringState === self.ourIceGatheringState) {
			return;
		}

		debug('onicegatheringstatechange() | iceGatheringState: %s', pc.iceGatheringState);
		self.ourIceGatheringState = pc.iceGatheringState;
		if (self.onicegatheringstatechange) {
			self.onicegatheringstatechange(event, pc.iceGatheringState);
		}
	};

	pc.onidentityresult = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('onidentityresult()');
		if (self.onidentityresult) {
			self.onidentityresult(event);
		}
	};

	pc.onpeeridentity = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('onpeeridentity()');
		if (self.onpeeridentity) {
			self.onpeeridentity(event);
		}
	};

	pc.onidpassertionerror = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('onidpassertionerror()');
		if (self.onidpassertionerror) {
			self.onidpassertionerror(event);
		}
	};

	pc.onidpvalidationerror = function (event) {
		if (isClosed.call(self)) {
			return;
		}

		debug('onidpvalidationerror()');
		if (self.onidpvalidationerror) {
			self.onidpvalidationerror(event);
		}
	};
}


function setPeerConnection() {
	// Create a RTCPeerConnection.
	if (!this.pcConstraints) {
		this.pc = new Adapter.RTCPeerConnection(this.pcConfig);
	} else {
		// NOTE: Deprecated.
		this.pc = new Adapter.RTCPeerConnection(this.pcConfig, this.pcConstraints);
	}

	// Set RTC events.
	setEvents.call(this);
}


function getLocalDescription() {
	var pc = this.pc,
		options = this.options,
		sdp = null;

	if (!pc.localDescription) {
		this.ourLocalDescription = null;
		return null;
	}

	// Mangle the SDP string.
	if (options.iceTransportsRelay) {
		sdp = pc.localDescription.sdp.replace(C.REGEXP_SDP_NON_RELAY_CANDIDATES, '');
	} else if (options.iceTransportsNone) {
		sdp = pc.localDescription.sdp.replace(C.REGEXP_SDP_CANDIDATES, '');
	}

	this.ourLocalDescription = new Adapter.RTCSessionDescription({
		type: pc.localDescription.type,
		sdp: sdp || pc.localDescription.sdp
	});

	return this.ourLocalDescription;
}


function setProperties() {
	var self = this;

	Object.defineProperties(this, {
		peerConnection: {
			get: function () {
				return self.pc;
			}
		},

		signalingState: {
			get: function () {
				return self.pc.signalingState;
			}
		},

		iceConnectionState: {
			get: function () {
				return self.pc.iceConnectionState;
			}
		},

		iceGatheringState: {
			get: function () {
				return self.pc.iceGatheringState;
			}
		},

		localDescription: {
			get: function () {
				return getLocalDescription.call(self);
			}
		},

		remoteDescription: {
			get: function () {
				return self.pc.remoteDescription;
			}
		},

		peerIdentity: {
			get: function () {
				return self.pc.peerIdentity;
			}
		}
	});
}

},{"./Adapter":1,"debug":6,"merge":9}],3:[function(require,module,exports){
'use strict';

module.exports = rtcninja;


// Dependencies.

var browser = require('bowser'),
	debug = require('debug')('rtcninja'),
	debugerror = require('debug')('rtcninja:ERROR'),
	version = require('./version'),
	Adapter = require('./Adapter'),
	RTCPeerConnection = require('./RTCPeerConnection'),

	// Internal vars.
	called = false;

debugerror.log = console.warn.bind(console);
debug('version %s', version);
debug('detected browser: %s %s [mobile:%s, tablet:%s, android:%s, ios:%s]',
		browser.name, browser.version, !!browser.mobile, !!browser.tablet,
		!!browser.android, !!browser.ios);


// Constructor.

function rtcninja(options) {
	// Load adapter
	var iface = Adapter(options || {});  // jshint ignore:line

	called = true;

	// Expose RTCPeerConnection class.
	rtcninja.RTCPeerConnection = RTCPeerConnection;

	// Expose WebRTC API and utils.
	rtcninja.getUserMedia = iface.getUserMedia;
	rtcninja.mediaDevices = iface.mediaDevices;
	rtcninja.RTCSessionDescription = iface.RTCSessionDescription;
	rtcninja.RTCIceCandidate = iface.RTCIceCandidate;
	rtcninja.MediaStreamTrack = iface.MediaStreamTrack;
	rtcninja.getMediaDevices = iface.getMediaDevices;
	rtcninja.attachMediaStream = iface.attachMediaStream;
	rtcninja.closeMediaStream = iface.closeMediaStream;
	rtcninja.canRenegotiate = iface.canRenegotiate;

	// Log WebRTC support.
	if (iface.hasWebRTC()) {
		debug('WebRTC supported');
		return true;
	} else {
		debugerror('WebRTC not supported');
		return false;
	}
}


// Public API.

// If called without calling rtcninja(), call it.
rtcninja.hasWebRTC = function () {
	if (!called) {
		rtcninja();
	}

	return Adapter.hasWebRTC();
};


// Expose version property.
Object.defineProperty(rtcninja, 'version', {
	get: function () {
		return version;
	}
});


// Expose called property.
Object.defineProperty(rtcninja, 'called', {
	get: function () {
		return called;
	}
});


// Exposing stuff.

rtcninja.debug = require('debug');
rtcninja.browser = browser;

},{"./Adapter":1,"./RTCPeerConnection":2,"./version":4,"bowser":5,"debug":6}],4:[function(require,module,exports){
'use strict';

// Expose the 'version' field of package.json.
module.exports = require('../package.json').version;


},{"../package.json":10}],5:[function(require,module,exports){
/*!
 * Bowser - a browser detector
 * https://github.com/ded/bowser
 * MIT License | (c) Dustin Diaz 2015
 */

!function (name, definition) {
  if (typeof module != 'undefined' && module.exports) module.exports = definition()
  else if (typeof define == 'function' && define.amd) define(name, definition)
  else this[name] = definition()
}('bowser', function () {
  /**
    * See useragents.js for examples of navigator.userAgent
    */

  var t = true

  function detect(ua) {

    function getFirstMatch(regex) {
      var match = ua.match(regex);
      return (match && match.length > 1 && match[1]) || '';
    }

    function getSecondMatch(regex) {
      var match = ua.match(regex);
      return (match && match.length > 1 && match[2]) || '';
    }

    var iosdevice = getFirstMatch(/(ipod|iphone|ipad)/i).toLowerCase()
      , likeAndroid = /like android/i.test(ua)
      , android = !likeAndroid && /android/i.test(ua)
      , nexusMobile = /nexus\s*[0-6]\s*/i.test(ua)
      , nexusTablet = !nexusMobile && /nexus\s*[0-9]+/i.test(ua)
      , chromeos = /CrOS/.test(ua)
      , silk = /silk/i.test(ua)
      , sailfish = /sailfish/i.test(ua)
      , tizen = /tizen/i.test(ua)
      , webos = /(web|hpw)os/i.test(ua)
      , windowsphone = /windows phone/i.test(ua)
      , samsungBrowser = /SamsungBrowser/i.test(ua)
      , windows = !windowsphone && /windows/i.test(ua)
      , mac = !iosdevice && !silk && /macintosh/i.test(ua)
      , linux = !android && !sailfish && !tizen && !webos && /linux/i.test(ua)
      , edgeVersion = getFirstMatch(/edge\/(\d+(\.\d+)?)/i)
      , versionIdentifier = getFirstMatch(/version\/(\d+(\.\d+)?)/i)
      , tablet = /tablet/i.test(ua)
      , mobile = !tablet && /[^-]mobi/i.test(ua)
      , xbox = /xbox/i.test(ua)
      , result

    if (/opera/i.test(ua)) {
      //  an old Opera
      result = {
        name: 'Opera'
      , opera: t
      , version: versionIdentifier || getFirstMatch(/(?:opera|opr|opios)[\s\/](\d+(\.\d+)?)/i)
      }
    } else if (/opr|opios/i.test(ua)) {
      // a new Opera
      result = {
        name: 'Opera'
        , opera: t
        , version: getFirstMatch(/(?:opr|opios)[\s\/](\d+(\.\d+)?)/i) || versionIdentifier
      }
    }
    else if (/SamsungBrowser/i.test(ua)) {
      result = {
        name: 'Samsung Internet for Android'
        , samsungBrowser: t
        , version: versionIdentifier || getFirstMatch(/(?:SamsungBrowser)[\s\/](\d+(\.\d+)?)/i)
      }
    }
    else if (/coast/i.test(ua)) {
      result = {
        name: 'Opera Coast'
        , coast: t
        , version: versionIdentifier || getFirstMatch(/(?:coast)[\s\/](\d+(\.\d+)?)/i)
      }
    }
    else if (/yabrowser/i.test(ua)) {
      result = {
        name: 'Yandex Browser'
      , yandexbrowser: t
      , version: versionIdentifier || getFirstMatch(/(?:yabrowser)[\s\/](\d+(\.\d+)?)/i)
      }
    }
    else if (/ucbrowser/i.test(ua)) {
      result = {
          name: 'UC Browser'
        , ucbrowser: t
        , version: getFirstMatch(/(?:ucbrowser)[\s\/](\d+(?:\.\d+)+)/i)
      }
    }
    else if (/mxios/i.test(ua)) {
      result = {
        name: 'Maxthon'
        , maxthon: t
        , version: getFirstMatch(/(?:mxios)[\s\/](\d+(?:\.\d+)+)/i)
      }
    }
    else if (/epiphany/i.test(ua)) {
      result = {
        name: 'Epiphany'
        , epiphany: t
        , version: getFirstMatch(/(?:epiphany)[\s\/](\d+(?:\.\d+)+)/i)
      }
    }
    else if (/puffin/i.test(ua)) {
      result = {
        name: 'Puffin'
        , puffin: t
        , version: getFirstMatch(/(?:puffin)[\s\/](\d+(?:\.\d+)?)/i)
      }
    }
    else if (/sleipnir/i.test(ua)) {
      result = {
        name: 'Sleipnir'
        , sleipnir: t
        , version: getFirstMatch(/(?:sleipnir)[\s\/](\d+(?:\.\d+)+)/i)
      }
    }
    else if (/k-meleon/i.test(ua)) {
      result = {
        name: 'K-Meleon'
        , kMeleon: t
        , version: getFirstMatch(/(?:k-meleon)[\s\/](\d+(?:\.\d+)+)/i)
      }
    }
    else if (windowsphone) {
      result = {
        name: 'Windows Phone'
      , windowsphone: t
      }
      if (edgeVersion) {
        result.msedge = t
        result.version = edgeVersion
      }
      else {
        result.msie = t
        result.version = getFirstMatch(/iemobile\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/msie|trident/i.test(ua)) {
      result = {
        name: 'Internet Explorer'
      , msie: t
      , version: getFirstMatch(/(?:msie |rv:)(\d+(\.\d+)?)/i)
      }
    } else if (chromeos) {
      result = {
        name: 'Chrome'
      , chromeos: t
      , chromeBook: t
      , chrome: t
      , version: getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)
      }
    } else if (/chrome.+? edge/i.test(ua)) {
      result = {
        name: 'Microsoft Edge'
      , msedge: t
      , version: edgeVersion
      }
    }
    else if (/vivaldi/i.test(ua)) {
      result = {
        name: 'Vivaldi'
        , vivaldi: t
        , version: getFirstMatch(/vivaldi\/(\d+(\.\d+)?)/i) || versionIdentifier
      }
    }
    else if (sailfish) {
      result = {
        name: 'Sailfish'
      , sailfish: t
      , version: getFirstMatch(/sailfish\s?browser\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/seamonkey\//i.test(ua)) {
      result = {
        name: 'SeaMonkey'
      , seamonkey: t
      , version: getFirstMatch(/seamonkey\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/firefox|iceweasel|fxios/i.test(ua)) {
      result = {
        name: 'Firefox'
      , firefox: t
      , version: getFirstMatch(/(?:firefox|iceweasel|fxios)[ \/](\d+(\.\d+)?)/i)
      }
      if (/\((mobile|tablet);[^\)]*rv:[\d\.]+\)/i.test(ua)) {
        result.firefoxos = t
      }
    }
    else if (silk) {
      result =  {
        name: 'Amazon Silk'
      , silk: t
      , version : getFirstMatch(/silk\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/phantom/i.test(ua)) {
      result = {
        name: 'PhantomJS'
      , phantom: t
      , version: getFirstMatch(/phantomjs\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/slimerjs/i.test(ua)) {
      result = {
        name: 'SlimerJS'
        , slimer: t
        , version: getFirstMatch(/slimerjs\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/blackberry|\bbb\d+/i.test(ua) || /rim\stablet/i.test(ua)) {
      result = {
        name: 'BlackBerry'
      , blackberry: t
      , version: versionIdentifier || getFirstMatch(/blackberry[\d]+\/(\d+(\.\d+)?)/i)
      }
    }
    else if (webos) {
      result = {
        name: 'WebOS'
      , webos: t
      , version: versionIdentifier || getFirstMatch(/w(?:eb)?osbrowser\/(\d+(\.\d+)?)/i)
      };
      /touchpad\//i.test(ua) && (result.touchpad = t)
    }
    else if (/bada/i.test(ua)) {
      result = {
        name: 'Bada'
      , bada: t
      , version: getFirstMatch(/dolfin\/(\d+(\.\d+)?)/i)
      };
    }
    else if (tizen) {
      result = {
        name: 'Tizen'
      , tizen: t
      , version: getFirstMatch(/(?:tizen\s?)?browser\/(\d+(\.\d+)?)/i) || versionIdentifier
      };
    }
    else if (/qupzilla/i.test(ua)) {
      result = {
        name: 'QupZilla'
        , qupzilla: t
        , version: getFirstMatch(/(?:qupzilla)[\s\/](\d+(?:\.\d+)+)/i) || versionIdentifier
      }
    }
    else if (/chromium/i.test(ua)) {
      result = {
        name: 'Chromium'
        , chromium: t
        , version: getFirstMatch(/(?:chromium)[\s\/](\d+(?:\.\d+)?)/i) || versionIdentifier
      }
    }
    else if (/chrome|crios|crmo/i.test(ua)) {
      result = {
        name: 'Chrome'
        , chrome: t
        , version: getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)
      }
    }
    else if (android) {
      result = {
        name: 'Android'
        , version: versionIdentifier
      }
    }
    else if (/safari|applewebkit/i.test(ua)) {
      result = {
        name: 'Safari'
      , safari: t
      }
      if (versionIdentifier) {
        result.version = versionIdentifier
      }
    }
    else if (iosdevice) {
      result = {
        name : iosdevice == 'iphone' ? 'iPhone' : iosdevice == 'ipad' ? 'iPad' : 'iPod'
      }
      // WTF: version is not part of user agent in web apps
      if (versionIdentifier) {
        result.version = versionIdentifier
      }
    }
    else if(/googlebot/i.test(ua)) {
      result = {
        name: 'Googlebot'
      , googlebot: t
      , version: getFirstMatch(/googlebot\/(\d+(\.\d+))/i) || versionIdentifier
      }
    }
    else {
      result = {
        name: getFirstMatch(/^(.*)\/(.*) /),
        version: getSecondMatch(/^(.*)\/(.*) /)
     };
   }

    // set webkit or gecko flag for browsers based on these engines
    if (!result.msedge && /(apple)?webkit/i.test(ua)) {
      if (/(apple)?webkit\/537\.36/i.test(ua)) {
        result.name = result.name || "Blink"
        result.blink = t
      } else {
        result.name = result.name || "Webkit"
        result.webkit = t
      }
      if (!result.version && versionIdentifier) {
        result.version = versionIdentifier
      }
    } else if (!result.opera && /gecko\//i.test(ua)) {
      result.name = result.name || "Gecko"
      result.gecko = t
      result.version = result.version || getFirstMatch(/gecko\/(\d+(\.\d+)?)/i)
    }

    // set OS flags for platforms that have multiple browsers
    if (!result.msedge && (android || result.silk)) {
      result.android = t
    } else if (iosdevice) {
      result[iosdevice] = t
      result.ios = t
    } else if (mac) {
      result.mac = t
    } else if (xbox) {
      result.xbox = t
    } else if (windows) {
      result.windows = t
    } else if (linux) {
      result.linux = t
    }

    // OS version extraction
    var osVersion = '';
    if (result.windowsphone) {
      osVersion = getFirstMatch(/windows phone (?:os)?\s?(\d+(\.\d+)*)/i);
    } else if (iosdevice) {
      osVersion = getFirstMatch(/os (\d+([_\s]\d+)*) like mac os x/i);
      osVersion = osVersion.replace(/[_\s]/g, '.');
    } else if (android) {
      osVersion = getFirstMatch(/android[ \/-](\d+(\.\d+)*)/i);
    } else if (result.webos) {
      osVersion = getFirstMatch(/(?:web|hpw)os\/(\d+(\.\d+)*)/i);
    } else if (result.blackberry) {
      osVersion = getFirstMatch(/rim\stablet\sos\s(\d+(\.\d+)*)/i);
    } else if (result.bada) {
      osVersion = getFirstMatch(/bada\/(\d+(\.\d+)*)/i);
    } else if (result.tizen) {
      osVersion = getFirstMatch(/tizen[\/\s](\d+(\.\d+)*)/i);
    }
    if (osVersion) {
      result.osversion = osVersion;
    }

    // device type extraction
    var osMajorVersion = osVersion.split('.')[0];
    if (
         tablet
      || nexusTablet
      || iosdevice == 'ipad'
      || (android && (osMajorVersion == 3 || (osMajorVersion >= 4 && !mobile)))
      || result.silk
    ) {
      result.tablet = t
    } else if (
         mobile
      || iosdevice == 'iphone'
      || iosdevice == 'ipod'
      || android
      || nexusMobile
      || result.blackberry
      || result.webos
      || result.bada
    ) {
      result.mobile = t
    }

    // Graded Browser Support
    // http://developer.yahoo.com/yui/articles/gbs
    if (result.msedge ||
        (result.msie && result.version >= 10) ||
        (result.yandexbrowser && result.version >= 15) ||
		    (result.vivaldi && result.version >= 1.0) ||
        (result.chrome && result.version >= 20) ||
        (result.samsungBrowser && result.version >= 4) ||
        (result.firefox && result.version >= 20.0) ||
        (result.safari && result.version >= 6) ||
        (result.opera && result.version >= 10.0) ||
        (result.ios && result.osversion && result.osversion.split(".")[0] >= 6) ||
        (result.blackberry && result.version >= 10.1)
        || (result.chromium && result.version >= 20)
        ) {
      result.a = t;
    }
    else if ((result.msie && result.version < 10) ||
        (result.chrome && result.version < 20) ||
        (result.firefox && result.version < 20.0) ||
        (result.safari && result.version < 6) ||
        (result.opera && result.version < 10.0) ||
        (result.ios && result.osversion && result.osversion.split(".")[0] < 6)
        || (result.chromium && result.version < 20)
        ) {
      result.c = t
    } else result.x = t

    return result
  }

  var bowser = detect(typeof navigator !== 'undefined' ? navigator.userAgent || '' : '')

  bowser.test = function (browserList) {
    for (var i = 0; i < browserList.length; ++i) {
      var browserItem = browserList[i];
      if (typeof browserItem=== 'string') {
        if (browserItem in bowser) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Get version precisions count
   *
   * @example
   *   getVersionPrecision("1.10.3") // 3
   *
   * @param  {string} version
   * @return {number}
   */
  function getVersionPrecision(version) {
    return version.split(".").length;
  }

  /**
   * Array::map polyfill
   *
   * @param  {Array} arr
   * @param  {Function} iterator
   * @return {Array}
   */
  function map(arr, iterator) {
    var result = [], i;
    if (Array.prototype.map) {
      return Array.prototype.map.call(arr, iterator);
    }
    for (i = 0; i < arr.length; i++) {
      result.push(iterator(arr[i]));
    }
    return result;
  }

  /**
   * Calculate browser version weight
   *
   * @example
   *   compareVersions(['1.10.2.1',  '1.8.2.1.90'])    // 1
   *   compareVersions(['1.010.2.1', '1.09.2.1.90']);  // 1
   *   compareVersions(['1.10.2.1',  '1.10.2.1']);     // 0
   *   compareVersions(['1.10.2.1',  '1.0800.2']);     // -1
   *
   * @param  {Array<String>} versions versions to compare
   * @return {Number} comparison result
   */
  function compareVersions(versions) {
    // 1) get common precision for both versions, for example for "10.0" and "9" it should be 2
    var precision = Math.max(getVersionPrecision(versions[0]), getVersionPrecision(versions[1]));
    var chunks = map(versions, function (version) {
      var delta = precision - getVersionPrecision(version);

      // 2) "9" -> "9.0" (for precision = 2)
      version = version + new Array(delta + 1).join(".0");

      // 3) "9.0" -> ["000000000"", "000000009"]
      return map(version.split("."), function (chunk) {
        return new Array(20 - chunk.length).join("0") + chunk;
      }).reverse();
    });

    // iterate in reverse order by reversed chunks array
    while (--precision >= 0) {
      // 4) compare: "000000009" > "000000010" = false (but "9" > "10" = true)
      if (chunks[0][precision] > chunks[1][precision]) {
        return 1;
      }
      else if (chunks[0][precision] === chunks[1][precision]) {
        if (precision === 0) {
          // all version chunks are same
          return 0;
        }
      }
      else {
        return -1;
      }
    }
  }

  /**
   * Check if browser is unsupported
   *
   * @example
   *   bowser.isUnsupportedBrowser({
   *     msie: "10",
   *     firefox: "23",
   *     chrome: "29",
   *     safari: "5.1",
   *     opera: "16",
   *     phantom: "534"
   *   });
   *
   * @param  {Object}  minVersions map of minimal version to browser
   * @param  {Boolean} [strictMode = false] flag to return false if browser wasn't found in map
   * @param  {String}  [ua] user agent string
   * @return {Boolean}
   */
  function isUnsupportedBrowser(minVersions, strictMode, ua) {
    var _bowser = bowser;

    // make strictMode param optional with ua param usage
    if (typeof strictMode === 'string') {
      ua = strictMode;
      strictMode = void(0);
    }

    if (strictMode === void(0)) {
      strictMode = false;
    }
    if (ua) {
      _bowser = detect(ua);
    }

    var version = "" + _bowser.version;
    for (var browser in minVersions) {
      if (minVersions.hasOwnProperty(browser)) {
        if (_bowser[browser]) {
          // browser version and min supported version.
          return compareVersions([version, minVersions[browser]]) < 0;
        }
      }
    }

    return strictMode; // not found
  }

  /**
   * Check if browser is supported
   *
   * @param  {Object} minVersions map of minimal version to browser
   * @param  {Boolean} [strictMode = false] flag to return false if browser wasn't found in map
   * @param  {String}  [ua] user agent string
   * @return {Boolean}
   */
  function check(minVersions, strictMode, ua) {
    return !isUnsupportedBrowser(minVersions, strictMode, ua);
  }

  bowser.isUnsupportedBrowser = isUnsupportedBrowser;
  bowser.compareVersions = compareVersions;
  bowser.check = check;

  /*
   * Set our detect method to the main bowser object so we can
   * reuse it to test other user agents.
   * This is needed to implement future tests.
   */
  bowser._detect = detect;

  return bowser
});

},{}],6:[function(require,module,exports){

/**
 * This is the web browser implementation of `debug()`.
 *
 * Expose `debug()` as the module.
 */

exports = module.exports = require('./debug');
exports.log = log;
exports.formatArgs = formatArgs;
exports.save = save;
exports.load = load;
exports.useColors = useColors;
exports.storage = 'undefined' != typeof chrome
               && 'undefined' != typeof chrome.storage
                  ? chrome.storage.local
                  : localstorage();

/**
 * Colors.
 */

exports.colors = [
  'lightseagreen',
  'forestgreen',
  'goldenrod',
  'dodgerblue',
  'darkorchid',
  'crimson'
];

/**
 * Currently only WebKit-based Web Inspectors, Firefox >= v31,
 * and the Firebug extension (any Firefox version) are known
 * to support "%c" CSS customizations.
 *
 * TODO: add a `localStorage` variable to explicitly enable/disable colors
 */

function useColors() {
  // is webkit? http://stackoverflow.com/a/16459606/376773
  return ('WebkitAppearance' in document.documentElement.style) ||
    // is firebug? http://stackoverflow.com/a/398120/376773
    (window.console && (console.firebug || (console.exception && console.table))) ||
    // is firefox >= v31?
    // https://developer.mozilla.org/en-US/docs/Tools/Web_Console#Styling_messages
    (navigator.userAgent.toLowerCase().match(/firefox\/(\d+)/) && parseInt(RegExp.$1, 10) >= 31);
}

/**
 * Map %j to `JSON.stringify()`, since no Web Inspectors do that by default.
 */

exports.formatters.j = function(v) {
  return JSON.stringify(v);
};


/**
 * Colorize log arguments if enabled.
 *
 * @api public
 */

function formatArgs() {
  var args = arguments;
  var useColors = this.useColors;

  args[0] = (useColors ? '%c' : '')
    + this.namespace
    + (useColors ? ' %c' : ' ')
    + args[0]
    + (useColors ? '%c ' : ' ')
    + '+' + exports.humanize(this.diff);

  if (!useColors) return args;

  var c = 'color: ' + this.color;
  args = [args[0], c, 'color: inherit'].concat(Array.prototype.slice.call(args, 1));

  // the final "%c" is somewhat tricky, because there could be other
  // arguments passed either before or after the %c, so we need to
  // figure out the correct index to insert the CSS into
  var index = 0;
  var lastC = 0;
  args[0].replace(/%[a-z%]/g, function(match) {
    if ('%%' === match) return;
    index++;
    if ('%c' === match) {
      // we only are interested in the *last* %c
      // (the user may have provided their own)
      lastC = index;
    }
  });

  args.splice(lastC, 0, c);
  return args;
}

/**
 * Invokes `console.log()` when available.
 * No-op when `console.log` is not a "function".
 *
 * @api public
 */

function log() {
  // this hackery is required for IE8/9, where
  // the `console.log` function doesn't have 'apply'
  return 'object' === typeof console
    && console.log
    && Function.prototype.apply.call(console.log, console, arguments);
}

/**
 * Save `namespaces`.
 *
 * @param {String} namespaces
 * @api private
 */

function save(namespaces) {
  try {
    if (null == namespaces) {
      exports.storage.removeItem('debug');
    } else {
      exports.storage.debug = namespaces;
    }
  } catch(e) {}
}

/**
 * Load `namespaces`.
 *
 * @return {String} returns the previously persisted debug modes
 * @api private
 */

function load() {
  var r;
  try {
    r = exports.storage.debug;
  } catch(e) {}
  return r;
}

/**
 * Enable namespaces listed in `localStorage.debug` initially.
 */

exports.enable(load());

/**
 * Localstorage attempts to return the localstorage.
 *
 * This is necessary because safari throws
 * when a user disables cookies/localstorage
 * and you attempt to access it.
 *
 * @return {LocalStorage}
 * @api private
 */

function localstorage(){
  try {
    return window.localStorage;
  } catch (e) {}
}

},{"./debug":7}],7:[function(require,module,exports){

/**
 * This is the common logic for both the Node.js and web browser
 * implementations of `debug()`.
 *
 * Expose `debug()` as the module.
 */

exports = module.exports = debug;
exports.coerce = coerce;
exports.disable = disable;
exports.enable = enable;
exports.enabled = enabled;
exports.humanize = require('ms');

/**
 * The currently active debug mode names, and names to skip.
 */

exports.names = [];
exports.skips = [];

/**
 * Map of special "%n" handling functions, for the debug "format" argument.
 *
 * Valid key names are a single, lowercased letter, i.e. "n".
 */

exports.formatters = {};

/**
 * Previously assigned color.
 */

var prevColor = 0;

/**
 * Previous log timestamp.
 */

var prevTime;

/**
 * Select a color.
 *
 * @return {Number}
 * @api private
 */

function selectColor() {
  return exports.colors[prevColor++ % exports.colors.length];
}

/**
 * Create a debugger with the given `namespace`.
 *
 * @param {String} namespace
 * @return {Function}
 * @api public
 */

function debug(namespace) {

  // define the `disabled` version
  function disabled() {
  }
  disabled.enabled = false;

  // define the `enabled` version
  function enabled() {

    var self = enabled;

    // set `diff` timestamp
    var curr = +new Date();
    var ms = curr - (prevTime || curr);
    self.diff = ms;
    self.prev = prevTime;
    self.curr = curr;
    prevTime = curr;

    // add the `color` if not set
    if (null == self.useColors) self.useColors = exports.useColors();
    if (null == self.color && self.useColors) self.color = selectColor();

    var args = Array.prototype.slice.call(arguments);

    args[0] = exports.coerce(args[0]);

    if ('string' !== typeof args[0]) {
      // anything else let's inspect with %o
      args = ['%o'].concat(args);
    }

    // apply any `formatters` transformations
    var index = 0;
    args[0] = args[0].replace(/%([a-z%])/g, function(match, format) {
      // if we encounter an escaped % then don't increase the array index
      if (match === '%%') return match;
      index++;
      var formatter = exports.formatters[format];
      if ('function' === typeof formatter) {
        var val = args[index];
        match = formatter.call(self, val);

        // now we need to remove `args[index]` since it's inlined in the `format`
        args.splice(index, 1);
        index--;
      }
      return match;
    });

    if ('function' === typeof exports.formatArgs) {
      args = exports.formatArgs.apply(self, args);
    }
    var logFn = enabled.log || exports.log || console.log.bind(console);
    logFn.apply(self, args);
  }
  enabled.enabled = true;

  var fn = exports.enabled(namespace) ? enabled : disabled;

  fn.namespace = namespace;

  return fn;
}

/**
 * Enables a debug mode by namespaces. This can include modes
 * separated by a colon and wildcards.
 *
 * @param {String} namespaces
 * @api public
 */

function enable(namespaces) {
  exports.save(namespaces);

  var split = (namespaces || '').split(/[\s,]+/);
  var len = split.length;

  for (var i = 0; i < len; i++) {
    if (!split[i]) continue; // ignore empty strings
    namespaces = split[i].replace(/\*/g, '.*?');
    if (namespaces[0] === '-') {
      exports.skips.push(new RegExp('^' + namespaces.substr(1) + '$'));
    } else {
      exports.names.push(new RegExp('^' + namespaces + '$'));
    }
  }
}

/**
 * Disable debug output.
 *
 * @api public
 */

function disable() {
  exports.enable('');
}

/**
 * Returns true if the given mode name is enabled, false otherwise.
 *
 * @param {String} name
 * @return {Boolean}
 * @api public
 */

function enabled(name) {
  var i, len;
  for (i = 0, len = exports.skips.length; i < len; i++) {
    if (exports.skips[i].test(name)) {
      return false;
    }
  }
  for (i = 0, len = exports.names.length; i < len; i++) {
    if (exports.names[i].test(name)) {
      return true;
    }
  }
  return false;
}

/**
 * Coerce `val`.
 *
 * @param {Mixed} val
 * @return {Mixed}
 * @api private
 */

function coerce(val) {
  if (val instanceof Error) return val.stack || val.message;
  return val;
}

},{"ms":8}],8:[function(require,module,exports){
/**
 * Helpers.
 */

var s = 1000;
var m = s * 60;
var h = m * 60;
var d = h * 24;
var y = d * 365.25;

/**
 * Parse or format the given `val`.
 *
 * Options:
 *
 *  - `long` verbose formatting [false]
 *
 * @param {String|Number} val
 * @param {Object} options
 * @return {String|Number}
 * @api public
 */

module.exports = function(val, options){
  options = options || {};
  if ('string' == typeof val) return parse(val);
  return options.long
    ? long(val)
    : short(val);
};

/**
 * Parse the given `str` and return milliseconds.
 *
 * @param {String} str
 * @return {Number}
 * @api private
 */

function parse(str) {
  str = '' + str;
  if (str.length > 10000) return;
  var match = /^((?:\d+)?\.?\d+) *(milliseconds?|msecs?|ms|seconds?|secs?|s|minutes?|mins?|m|hours?|hrs?|h|days?|d|years?|yrs?|y)?$/i.exec(str);
  if (!match) return;
  var n = parseFloat(match[1]);
  var type = (match[2] || 'ms').toLowerCase();
  switch (type) {
    case 'years':
    case 'year':
    case 'yrs':
    case 'yr':
    case 'y':
      return n * y;
    case 'days':
    case 'day':
    case 'd':
      return n * d;
    case 'hours':
    case 'hour':
    case 'hrs':
    case 'hr':
    case 'h':
      return n * h;
    case 'minutes':
    case 'minute':
    case 'mins':
    case 'min':
    case 'm':
      return n * m;
    case 'seconds':
    case 'second':
    case 'secs':
    case 'sec':
    case 's':
      return n * s;
    case 'milliseconds':
    case 'millisecond':
    case 'msecs':
    case 'msec':
    case 'ms':
      return n;
  }
}

/**
 * Short format for `ms`.
 *
 * @param {Number} ms
 * @return {String}
 * @api private
 */

function short(ms) {
  if (ms >= d) return Math.round(ms / d) + 'd';
  if (ms >= h) return Math.round(ms / h) + 'h';
  if (ms >= m) return Math.round(ms / m) + 'm';
  if (ms >= s) return Math.round(ms / s) + 's';
  return ms + 'ms';
}

/**
 * Long format for `ms`.
 *
 * @param {Number} ms
 * @return {String}
 * @api private
 */

function long(ms) {
  return plural(ms, d, 'day')
    || plural(ms, h, 'hour')
    || plural(ms, m, 'minute')
    || plural(ms, s, 'second')
    || ms + ' ms';
}

/**
 * Pluralization helper.
 */

function plural(ms, n, name) {
  if (ms < n) return;
  if (ms < n * 1.5) return Math.floor(ms / n) + ' ' + name;
  return Math.ceil(ms / n) + ' ' + name + 's';
}

},{}],9:[function(require,module,exports){
/*!
 * @name JavaScript/NodeJS Merge v1.2.0
 * @author yeikos
 * @repository https://github.com/yeikos/js.merge

 * Copyright 2014 yeikos - MIT license
 * https://raw.github.com/yeikos/js.merge/master/LICENSE
 */

;(function(isNode) {

	/**
	 * Merge one or more objects 
	 * @param bool? clone
	 * @param mixed,... arguments
	 * @return object
	 */

	var Public = function(clone) {

		return merge(clone === true, false, arguments);

	}, publicName = 'merge';

	/**
	 * Merge two or more objects recursively 
	 * @param bool? clone
	 * @param mixed,... arguments
	 * @return object
	 */

	Public.recursive = function(clone) {

		return merge(clone === true, true, arguments);

	};

	/**
	 * Clone the input removing any reference
	 * @param mixed input
	 * @return mixed
	 */

	Public.clone = function(input) {

		var output = input,
			type = typeOf(input),
			index, size;

		if (type === 'array') {

			output = [];
			size = input.length;

			for (index=0;index<size;++index)

				output[index] = Public.clone(input[index]);

		} else if (type === 'object') {

			output = {};

			for (index in input)

				output[index] = Public.clone(input[index]);

		}

		return output;

	};

	/**
	 * Merge two objects recursively
	 * @param mixed input
	 * @param mixed extend
	 * @return mixed
	 */

	function merge_recursive(base, extend) {

		if (typeOf(base) !== 'object')

			return extend;

		for (var key in extend) {

			if (typeOf(base[key]) === 'object' && typeOf(extend[key]) === 'object') {

				base[key] = merge_recursive(base[key], extend[key]);

			} else {

				base[key] = extend[key];

			}

		}

		return base;

	}

	/**
	 * Merge two or more objects
	 * @param bool clone
	 * @param bool recursive
	 * @param array argv
	 * @return object
	 */

	function merge(clone, recursive, argv) {

		var result = argv[0],
			size = argv.length;

		if (clone || typeOf(result) !== 'object')

			result = {};

		for (var index=0;index<size;++index) {

			var item = argv[index],

				type = typeOf(item);

			if (type !== 'object') continue;

			for (var key in item) {

				var sitem = clone ? Public.clone(item[key]) : item[key];

				if (recursive) {

					result[key] = merge_recursive(result[key], sitem);

				} else {

					result[key] = sitem;

				}

			}

		}

		return result;

	}

	/**
	 * Get type of variable
	 * @param mixed input
	 * @return string
	 *
	 * @see http://jsperf.com/typeofvar
	 */

	function typeOf(input) {

		return ({}).toString.call(input).slice(8, -1).toLowerCase();

	}

	if (isNode) {

		module.exports = Public;

	} else {

		window[publicName] = Public;

	}

})(typeof module === 'object' && module && typeof module.exports === 'object' && module.exports);
},{}],10:[function(require,module,exports){
module.exports={
  "name": "rtcninja",
  "version": "0.7.0",
  "description": "WebRTC API wrapper to deal with different browsers",
  "author": "Iñaki Baz Castillo <inaki.baz@eface2face.com> (http://eface2face.com)",
  "contributors": [
    "Jesús Pérez <jesus.perez@eface2face.com>"
  ],
  "license": "MIT",
  "main": "lib/rtcninja.js",
  "homepage": "https://github.com/eface2face/rtcninja.js",
  "repository": {
    "type": "git",
    "url": "https://github.com/eface2face/rtcninja.js.git"
  },
  "keywords": [
    "webrtc"
  ],
  "engines": {
    "node": ">=0.12.0"
  },
  "dependencies": {
    "bowser": "^1.4.6",
    "debug": "^2.2.0",
    "merge": "^1.2.0"
  },
  "devDependencies": {
    "browserify": "^13.1.0",
    "gulp": "git+https://github.com/gulpjs/gulp.git#4.0",
    "gulp-expect-file": "0.0.7",
    "gulp-filelog": "^0.4.1",
    "gulp-header": "^1.8.8",
    "gulp-jscs": "^3.0.2",
    "gulp-jscs-stylish": "^1.4.0",
    "gulp-jshint": "^2.0.1",
    "gulp-rename": "^1.2.2",
    "gulp-uglify": "^1.5.4",
    "jshint": "^2.9.3",
    "jshint-stylish": "^2.2.1",
    "vinyl-source-stream": "^1.1.0"
  }
}

},{}]},{},[3])(3)
});