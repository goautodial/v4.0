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
