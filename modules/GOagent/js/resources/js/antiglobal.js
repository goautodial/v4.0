(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.antiglobal = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
var lastGlobals = getGlobals();
var doLog = true;
var doThrow = false;

function antiglobal()
{
	var globals = getGlobals(); // Get current globals
	var givenGlobals = Array.prototype.slice.call(arguments);
	var newGlobals = [];
	var removedGlobals = [];
	var changed = false;
	var i, len, elem;

	for (i=0, len=globals.length; i<len; i++)
	{
		elem = globals[i];

		if (lastGlobals.indexOf(elem) === -1 && givenGlobals.indexOf(elem) === -1)
		{
			newGlobals.push(elem);
			changed = true;
		}
	}

	for (i=0, len=lastGlobals.length; i<len; i++)
	{
		elem = lastGlobals[i];

		if (globals.indexOf(elem) === -1)
		{
			removedGlobals.push(elem);
			changed = true;
		}
	}

	// Update lastGlobals
	lastGlobals = globals.concat(givenGlobals);

	if (changed)
	{
		var msg = 'antiglobal() | globals do not match:';

		for (i=0, len=newGlobals.length; i<len; i++)
		{
			elem = newGlobals[i];
			msg = msg + '\n+ ' + elem;
		}

		for (i=0, len=removedGlobals.length; i<len; i++)
		{
			elem = removedGlobals[i];
			msg = msg + '\n- ' + elem;
		}

		if (doLog)
			console.error(msg);
		if (doThrow)
			throw new Error(msg);
	}

	return !changed;
}

/**
 * Reset current globals
 */
antiglobal.reset = function()
{
	lastGlobals = getGlobals();
};

/**
 * Public properties
 */
Object.defineProperties(antiglobal,
{
	log:
	{
		get: function()     { return doLog;          },
		set: function(bool) { doLog = Boolean(bool); }
	},
	throw:
	{
		get: function()     { return doThrow;          },
		set: function(bool) { doThrow = Boolean(bool); }
	}
});

/**
 * Private API
 */

function getGlobals()
{
	var globals = [];

	for (var key in global)
	{
		if (global.hasOwnProperty(key))
		{
			// Ignore this module
			if (key !== 'antiglobal')
				globals.push(key);
		}
	}

	return globals;
}

module.exports = antiglobal;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}]},{},[1])(1)
});