/*
 ### jQuery Multiple File Selection Plugin v2.2.1 - 2015-03-23 ###
 * Home: http://www.fyneworks.com/jquery/multifile/
 * Code: http://code.google.com/p/jquery-multifile-plugin/
 *
	* Licensed under http://en.wikipedia.org/wiki/MIT_License
 ###
*/
/*# AVOID COLLISIONS #*/
;
if (window.jQuery)(function ($) {
	"use strict";
	/*# AVOID COLLISIONS #*/

	// size label function (shows kb and mb where accordingly)
	function sl(x) {
		return x > 1048576 ? (x / 1048576).toFixed(1) + 'Mb' : (x==1024?'1Mb': (x / 1024).toFixed(1) + 'Kb' )
	};
	// utility function to return an array of
	function FILE_LIST(x){
		return ((x.files&&x.files.length) ? x.files : null) || [{
			name: x.value,
			size: 0,
			type: ((x.value || '').match(/[^\.]+$/i) || [''])[0]
		}];
	};

	// plugin initialization
	$.fn.MultiFile = function (options) {
		if (this.length == 0) return this; // quick fail

		// Handle API methods
		if (typeof arguments[0] == 'string') {
			// Perform API methods on individual elements
			if (this.length > 1) {
				var args = arguments;
				return this.each(function () {
					$.fn.MultiFile.apply($(this), args);
				});
			};
			// Invoke API method handler (and return whatever it wants to return)
			return $.fn.MultiFile[arguments[0]].apply(this, $.makeArray(arguments).slice(1) || []);
		};

		// Accept number
		if (typeof options == 'number') {
			options = {max: options};
		};

		// Initialize options for this call
		var options = $.extend({} /* new object */ ,
			$.fn.MultiFile.options /* default options */ ,
			options || {} /* just-in-time options */
		);

		// Empty Element Fix!!!
		// this code will automatically intercept native form submissions
		// and disable empty file elements
		$('form')
			.not('MultiFile-intercepted')
			.addClass('MultiFile-intercepted')
			.submit($.fn.MultiFile.disableEmpty);

		//### http://plugins.jquery.com/node/1363
		// utility method to integrate this plugin with others...
		if ($.fn.MultiFile.options.autoIntercept) {
			$.fn.MultiFile.intercept($.fn.MultiFile.options.autoIntercept /* array of methods to intercept */ );
			$.fn.MultiFile.options.autoIntercept = null; /* only run this once */
		};

		// loop through each matched element
		this
			.not('.MultiFile-applied')
			.addClass('MultiFile-applied')
			.each(function () {
				//#####################################################################
				// MAIN PLUGIN FUNCTIONALITY - START
				//#####################################################################

				// BUG 1251 FIX: http://plugins.jquery.com/project/comments/add/1251
				// variable group_count would repeat itself on multiple calls to the plugin.
				// this would cause a conflict with multiple elements
				// changes scope of variable to global so id will be unique over n calls
				window.MultiFile = (window.MultiFile || 0) + 1;
				var group_count = window.MultiFile;

				// Copy parent attributes - Thanks to Jonas Wagner
				// we will use this one to create new input elements
				var MultiFile = {
					e: this,
					E: $(this),
					clone: $(this).clone()
				};

				//===

				//# USE CONFIGURATION
				var o = $.extend({},
					$.fn.MultiFile.options,
					options || {}, ($.metadata ? MultiFile.E.metadata() : ($.meta ? MultiFile.E.data() : null)) || {}, /* metadata options */ {} /* internals */
				);
				// limit number of files that can be selected?
				if (!(o.max > 0) /*IsNull(MultiFile.max)*/ ) {
					o.max = MultiFile.E.attr('maxlength');
				};
				if (!(o.max > 0) /*IsNull(MultiFile.max)*/ ) {
					o.max = (String(MultiFile.e.className.match(/\b(max|limit)\-([0-9]+)\b/gi) || ['']).match(/[0-9]+/gi) || [''])[0];
					if (!(o.max > 0)) o.max = -1;
					else o.max = String(o.max).match(/[0-9]+/gi)[0];
				};
				o.max = new Number(o.max);
				// limit extensions?
				o.accept = o.accept || MultiFile.E.attr('accept') || '';
				if (!o.accept) {
					o.accept = (MultiFile.e.className.match(/\b(accept\-[\w\|]+)\b/gi)) || '';
					o.accept = new String(o.accept).replace(/^(accept|ext)\-/i, '');
				};
				// limit total pay load size
				o.maxsize = o.maxsize>0?o.maxsize:null || MultiFile.E.data('maxsize') || 0;
				if (!(o.maxsize > 0) /*IsNull(MultiFile.maxsize)*/ ) {
					o.maxsize = (String(MultiFile.e.className.match(/\b(maxsize|maxload|size)\-([0-9]+)\b/gi) || ['']).match(/[0-9]+/gi) || [''])[0];
					if (!(o.maxsize > 0)) o.maxsize = -1;
					else o.maxsize = String(o.maxsize).match(/[0-9]+/gi)[0];
				};
				// limit individual file size
				o.maxfile = o.maxfile>0?o.maxfile:null || MultiFile.E.data('maxfile') || 0;
				if (!(o.maxfile > 0) /*IsNull(MultiFile.maxfile)*/ ) {
					o.maxfile = (String(MultiFile.e.className.match(/\b(maxfile|filemax)\-([0-9]+)\b/gi) || ['']).match(/[0-9]+/gi) || [''])[0];
					if (!(o.maxfile > 0)) o.maxfile = -1;
					else o.maxfile = String(o.maxfile).match(/[0-9]+/gi)[0];
				};

				//===

				// size options are accepted in kylobytes, so multiple them by 1024
				if(o.maxfile>1) o.maxfile = o.maxfile * 1024;
				if(o.maxsize>1) o.maxsize = o.maxsize * 1024;

				//===

				// HTML5: enforce multiple selection to be enabled
				if(o.max>1) MultiFile.E.attr('multiple','multiple').prop('multiple',true);

				//===

				// APPLY CONFIGURATION
				$.extend(MultiFile, o || {});
				MultiFile.STRING = $.extend({}, $.fn.MultiFile.options.STRING, MultiFile.STRING);

				//===

				//#########################################
				// PRIVATE PROPERTIES/METHODS
				$.extend(MultiFile, {
					n: 0, // How many elements are currently selected?
					slaves: [],
					files: [],
					instanceKey: MultiFile.e.id || 'MultiFile' + String(group_count), // Instance Key?
					generateID: function (z) {
						return MultiFile.instanceKey + (z > 0 ? '_F' + String(z) : '');
					},
					trigger: function (event, element, MultiFile, files) {
						var rv, handler = MultiFile[event] || MultiFile['on'+event] ;
						if (handler){
							files = files || MultiFile.files || FILE_LIST(this);
							;
							$.each(files,function(i, file){
								// execute function in element's context, so 'this' variable is current element
								rv = handler.apply(MultiFile.wrapper, [element, file.name, MultiFile, file]);
							});
							return rv;
						};
					}
				});

				//===

				// Setup dynamic regular expression for extension validation
				// - thanks to John-Paul Bader: http://smyck.de/2006/08/11/javascript-dynamic-regular-expresions/
				if (String(MultiFile.accept).length > 1) {
					MultiFile.accept = MultiFile.accept.replace(/\W+/g, '|').replace(/^\W|\W$/g, '');
					MultiFile.rxAccept = new RegExp('\\.(' + (MultiFile.accept ? MultiFile.accept : '') + ')$', 'gi');
				};

				//===

				// Create wrapper to hold our file list
				MultiFile.wrapID = MultiFile.instanceKey;// + '_wrap'; // Wrapper ID?
				MultiFile.E.wrap('<div class="MultiFile-wrap" id="' + MultiFile.wrapID + '"></div>');
				MultiFile.wrapper = $('#' + MultiFile.wrapID + '');

				//===

				// MultiFile MUST have a name - default: file1[], file2[], file3[]
				MultiFile.e.name = MultiFile.e.name || 'file' + group_count + '[]';

				//===

				if (!MultiFile.list) {
					// Create a wrapper for the list
					// * OPERA BUG: NO_MODIFICATION_ALLOWED_ERR ('list' is a read-only property)
					// this change allows us to keep the files in the order they were selected
					MultiFile.wrapper.append('<div class="MultiFile-list" id="' + MultiFile.wrapID + '_list"></div>');
					MultiFile.list = $('#' + MultiFile.wrapID + '_list');
				};
				MultiFile.list = $(MultiFile.list);

				//===

				// Bind a new element
				MultiFile.addSlave = function (slave, slave_count) {
					//if(window.console) console.log('MultiFile.addSlave',slave_count);
					
					// Keep track of how many elements have been displayed
					MultiFile.n++;
					// Add reference to master element
					slave.MultiFile = MultiFile;

					// BUG FIX: http://plugins.jquery.com/node/1495
					// Clear identifying properties from clones
					slave.id = slave.name = '';

					// Define element's ID and name (upload components need this!)
					//slave.id = slave.id || MultiFile.generateID(slave_count);
					slave.id = MultiFile.generateID(slave_count);
					//FIX for: http://code.google.com/p/jquery-multifile-plugin/issues/detail?id=23
					//CHANGE v2.2.1 - change ID of all file elements, keep original ID in wrapper

					// 2008-Apr-29: New customizable naming convention (see url below)
					// http://groups.google.com/group/jquery-dev/browse_frm/thread/765c73e41b34f924#
					slave.name = String(MultiFile.namePattern
						/*master name*/
						.replace(/\$name/gi, $(MultiFile.clone).attr('name'))
						/*master id */
						.replace(/\$id/gi, $(MultiFile.clone).attr('id'))
						/*group count*/
						.replace(/\$g/gi, group_count) //(group_count>0?group_count:''))
						/*slave count*/
						.replace(/\$i/gi, slave_count) //(slave_count>0?slave_count:''))
					);

					// If we've reached maximum number, disable input slave
					var disable_slave;
					if ((MultiFile.max > 0) && ((MultiFile.files.length) > (MultiFile.max))) {
						slave.disabled = true;
						disable_slave = true;
					};

					// Remember most recent slave
					MultiFile.current = slave;
					
					// We'll use jQuery from now on
					slave = $(slave);

					// Clear value
					slave.val('').attr('value', '')[0].value = '';

					// Stop plugin initializing on slaves
					slave.addClass('MultiFile-applied');

					// Triggered when a file is selected
					slave.change(function (a, b, c) {
						//if(window.console) console.log('MultiFile.slave.change',slave_count);
						//if(window.console) console.log('MultiFile.slave.change',this.files);

						// Lose focus to stop IE7 firing onchange again
						$(this).blur();

						//# NEW 2014-04-14 - accept multiple file selection, HTML5
						var e = this,
								prevs = MultiFile.files || [],
								files = this.files || [{
									name: this.value,
									size: 0,
									type: ((this.value || '').match(/[^\.]+$/i) || [''])[0]
								}],
								newfs = [],
								newfs_size = 0,
								total_size = MultiFile.total_size || 0/*,
								html5_multi_mode = this.files && $(this).attr('multiple')*/
							;

						// recap
						//console.log('START '+ prevs.length + ' files @ '+ sl(total_size) +'.', prevs);

						//# Retrive value of selected file from element
						var ERROR = []; //, v = String(this.value || '');

						// make a normal array
						$.each(files, function (i, file) {
							newfs[newfs.length] = file;
						});

						//# Trigger Event! onFileSelect
						MultiFile.trigger('FileSelect', this, MultiFile, newfs);
						//# End Event!

						// validate individual files
						$.each(files, function (i, file) {

							// pop local variables out of array/file object
							var v = file.name,
									s = file.size,
									p = function(z){
										return z
											.replace('$ext', String(v.match(/[^\.]+$/i) || ''))
											.replace('$file', v.match(/[^\/\\]+$/gi))
											.replace('$size', sl(s) + ' > ' + sl(MultiFile.maxfile))
									}
							;

							// check extension
							if (MultiFile.accept && v && !v.match(MultiFile.rxAccept)) {
								ERROR[ERROR.length] = p(MultiFile.STRING.denied);
								MultiFile.trigger('FileInvalid', this, MultiFile, [file]);
							};

							// Disallow duplicates
							$(MultiFile.wrapper).find('input[type=file]').not(e).each(function(){
								// go through each file in each slave
								$.each(FILE_LIST(this), function (i, file) {
									if(file.name){
										//console.log('MultiFile.debug> Duplicate?', file.name, v);
										var x = (file.name || '').replace(/^C:\\fakepath\\/gi,'');
										if ( v == x || v == x.substr(x.length - v.length)) {
											ERROR[ERROR.length] = p(MultiFile.STRING.duplicate);
											MultiFile.trigger('FileDuplicate', e, MultiFile, [file]);
										};
									};
								});
							});

							// limit the max size of individual files selected
							if (MultiFile.maxfile>0 && s>0 && s>MultiFile.maxfile) {
								ERROR[ERROR.length] = p(MultiFile.STRING.toobig);
								MultiFile.trigger('FileTooBig', this, MultiFile, [file]);
							};

							// check extension
							var customError = MultiFile.trigger('FileValidate', this, MultiFile, [file]);
							if(customError && customError!=''){
								ERROR[ERROR.length] = p(customError);
							};
								
							// add up size of files selected
							newfs_size += file.size;

						});
						
						// add up total for all files selected (existing and new)
						total_size += newfs_size;

						// put some useful information in the file array
						newfs.size = newfs_size;
						newfs.total = total_size;
						newfs.total_length = newfs.length + prevs.length;

						// limit the number of files selected
						if (MultiFile.max>0 && prevs.length + files.length > MultiFile.max) {
							ERROR[ERROR.length] = MultiFile.STRING.toomany.replace('$max', MultiFile.max);
							MultiFile.trigger('FileTooMany', this, MultiFile, newfs);
						};

						// limit the max size of files selected
						if (MultiFile.maxsize > 0 && total_size > MultiFile.maxsize) {
							ERROR[ERROR.length] = MultiFile.STRING.toomuch.replace('$size', sl(total_size) + ' > ' + sl(MultiFile.maxsize));
							MultiFile.trigger('FileTooMuch', this, MultiFile, newfs);
						};

						// Create a new file input element
						var newEle = $(MultiFile.clone).clone(); // Copy parent attributes - Thanks to Jonas Wagner
						//# Let's remember which input we've generated so
						// we can disable the empty ones before submission
						// See: http://plugins.jquery.com/node/1495
						newEle.addClass('MultiFile');

						// Handle error
						if (ERROR.length > 0) {

							// Handle error
							MultiFile.error(ERROR.join('\n\n'));

							// 2007-06-24: BUG FIX - Thanks to Adrian Wróbel <adrian [dot] wrobel [at] gmail.com>
							// Ditch the trouble maker and add a fresh new element
							MultiFile.n--;
							MultiFile.addSlave(newEle[0], slave_count);
							slave.parent().prepend(newEle);
							slave.remove();
							return false;

						}
						else { // if no errors have been found

							// remember total size
							MultiFile.total_size = total_size;

							// merge arrays
							files = prevs.concat(newfs);

							// put some useful information in the file array
							files.size = total_size;
							files.size_label = sl(total_size);

							// recap
							//console.log('NOW '+ files.length + ' files @ '+ sl(total_size) +'.', files);

							// remember files
							MultiFile.files = files;

							// Hide this element (NB: display:none is evil!)
							$(this).css({
								position: 'absolute',
								top: '-3000px'
							});

							// Add new element to the form
							slave.after(newEle);

							// Bind functionality
							MultiFile.addSlave(newEle[0], slave_count + 1);

							// Update list
							MultiFile.addToList(this, slave_count, newfs);

							//# Trigger Event! afterFileSelect
							MultiFile.trigger('afterFileSelect', this, MultiFile, newfs);
							//# End Event!

						}; // no errors detected

					}); // slave.change()

					// point to wrapper
					$(slave).data('MultiFile-wrap', MultiFile.wrapper);

					// store contorl's settings and file info in wrapper
					$(MultiFile.wrapper).data('MultiFile',MultiFile);

					// disable?
					if(disable_slave) $(slave).attr('disabled','disabled').prop('disabled',true);

				}; // MultiFile.addSlave
				// Bind a new element



				// Add a new file to the list
				MultiFile.addToList = function (slave, slave_count, files) {
					//if(window.console) console.log('MultiFile.addToList',slave_count);

					//# Trigger Event! onFileAppend
					MultiFile.trigger('FileAppend', slave, MultiFile, files);
					//# End Event!
					
					var names = $('<span/>');
					$.each(files, function (i, file) {
						var v = String(file.name || '' ),
								S = MultiFile.STRING,
								n = S.label || S.file || S.name,
								t = S.title || S.tooltip || S.selected,
								p = '<img class="MultiFile-preview" style="'+ MultiFile.previewCss+'"/>',
								label =	$(
										(
											'<span class="MultiFile-label" title="' + t + '">'+
												'<span class="MultiFile-title">'+ n +'</span>'+
												(MultiFile.preview || $(slave).is('.with-preview') ? p : '' )+
											'</span>'
										)
										.replace(/\$(file|name)/gi, (v.match(/[^\/\\]+$/gi)||[v])[0])
										.replace(/\$(ext|extension|type)/gi, (v.match(/[^\.]+$/gi)||[''])[0])
										.replace(/\$(size)/gi, sl(file.size || 0))
										.replace(/\$(preview)/gi, p)
										.replace(/\$(i)/gi, i)
								);
						
						// now supports preview via locale string.
						// just add an <img class='MultiFile-preview'/> anywhere within the "file" string
						label.find('img.MultiFile-preview').each(function(){
							var t = this;
							var oFReader = new FileReader();
							oFReader.readAsDataURL(file);
							oFReader.onload = function (oFREvent) {
								t.src = oFREvent.target.result;
							};
						});

						// append file label to list
						if(i>1) names.append(', ');
						names.append(label);

						var v = String(file.name || '' );
						names[names.length] =
							(
								'<span class="MultiFile-title" title="' + MultiFile.STRING.selected + '">'
									+ MultiFile.STRING.file +
								'</span>'
							)
							.replace(/\$(file|name)/gi, (v.match(/[^\/\\]+$/gi)||[v])[0])
							.replace(/\$(ext|extension|type)/gi, (v.match(/[^\.]+$/gi)||[''])[0])
							.replace(/\$(size)/gi, sl(file.size || 0))
							.replace(/\$(i)/gi, i)
						;
					});

					//$.each(files, function (i, file) {
						// Create label elements
						var
							r = $('<div class="MultiFile-label"></div>'),
							b = $('<a class="MultiFile-remove" href="#' + MultiFile.wrapID + '">' + MultiFile.STRING.remove + '</a>')
								
								// ********
								// TODO:
								// refactor this as a single event listener on the control's
								// wrapper for better performance and cleaner code
								// ********
								.click(function () {

									// get list of files being removed
									var files_being_removed = FILE_LIST(slave);
									
									//# Trigger Event! onFileRemove
									MultiFile.trigger('FileRemove', slave, MultiFile, files_being_removed);
									//# End Event!

									MultiFile.n--;
									MultiFile.current.disabled = false;

									// remove the relevant <input type="file"/> element
									$(slave).remove();

									// remove the relevant label
									$(this).parent().remove();

									// Show most current element again (move into view) and clear selection
									$(MultiFile.current).css({
										position: '',
										top: ''
									});
									$(MultiFile.current).reset().val('').attr('value', '')[0].value = '';

									// point to currently visible element (always true, not necessary)
									//MultiFile.current = MultiFile.wrapper.find('[type=file]:visible');

									// rebuild array with the files that are left.
									var files_remaining = [], remain_size = 0;
									// go through each slave
									$(MultiFile.wrapper).find('input[type=file]').each(function(){
										// go through each file in each slave
										$.each(FILE_LIST(this), function (i, file) {
											if(file.name){
												//console.log('MultiFile.debug> FileRemove> remaining file', file.size, file);
												// fresh file array
												files_remaining[files_remaining.length] = file;
												// fresh size count
												remain_size += file.size;
											};
										});
									});

									// update MultiFile object
									MultiFile.files = files_remaining;
									MultiFile.total_size = remain_size;
									MultiFile.size_label = sl(remain_size);

									// update current control's reference to MultiFile object
									$(MultiFile.wrapper).data('MultiFile', MultiFile);

									//# Trigger Event! afterFileRemove
									MultiFile.trigger('afterFileRemove', slave, MultiFile, files_being_removed);
									//# End Event!

									//# Trigger Event! onFileChange
									MultiFile.trigger('FileChange', MultiFile.current, MultiFile, files_remaining);
									//# End Event!

									return false;
								});

						// Insert label
						MultiFile.list.append(
							r.append(b, ' ', names)
						);

					//}); // each file?

					//# Trigger Event! afterFileAppend
					MultiFile.trigger('afterFileAppend', slave, MultiFile, files);
					//# End Event!

					//# Trigger Event! onFileChange
					MultiFile.trigger('FileChange', slave, MultiFile, MultiFile.files);
					//# End Event!

				}; // MultiFile.addToList
				// Add element to selected files list



				// Bind functionality to the first element
				if (!MultiFile.MultiFile) MultiFile.addSlave(MultiFile.e, 0);

				// Increment control count
				//MultiFile.I++; // using window.MultiFile
				MultiFile.n++;

				// deprecated: contorl's data now stored in wrapper because it is never removed.
				// improved performance and lower memory comsumption
				// Save control to element
				//MultiFile.E.data('MultiFile', MultiFile);


				//#####################################################################
				// MAIN PLUGIN FUNCTIONALITY - END
				//#####################################################################
			}); // each element
	};

	/*--------------------------------------------------------*/

	/*
	### Core functionality and API ###
	*/
	$.extend($.fn.MultiFile, {


		/**
		 * This method exposes the all the control's data
		 *
		 * Returns an object with various settings and properties of the selected files
		 * for this particular instance of the control. stored in the control's wrapper
		 *
		 * @name data
		 * @type Object
		 * @cat Plugins/MultiFile
		 * @author Diego A. (http://www.fyneworks.com/)
		 *
		 * @example $('#selector').MultiFile('data');
		 */
		data: function () {
			
			// analyse this element
			var e = $(this), b = e.is('.MultiFile-wrap');
			
			// get control wrapper
			var wp = b ? e : e.data('MultiFile-wrap');
			if(!wp || !wp.length)
				return !console.error('Could not find MultiFile control wrapper');
			
			// get control data from wrapper
			var mf = wp.data('MultiFile');
			if(!mf)
				return !console.error('Could not find MultiFile data in wrapper');
			
			// return data
			return mf || {};
		},


		/**
		 * This method removes all selected files
		 *
		 * Returns a jQuery collection of all affected elements.
		 *
		 * @name reset
		 * @type jQuery
		 * @cat Plugins/MultiFile
		 * @author Diego A. (http://www.fyneworks.com/)
		 *
		 * @example $.fn.MultiFile.reset();
		 */
		reset: function () {
			var mf = this.MultiFile('data');
			if (mf) $(mf.list).find('a.MultiFile-remove').click();
			return $(this);
		},


		/**
		 * This method exposes the array of selected files
		 *
		 * Returns an array of file objects
		 *
		 * @name files
		 * @type Array
		 * @cat Plugins/MultiFile
		 * @author Diego A. (http://www.fyneworks.com/)
		 *
		 * @example $('#selector').MultiFile('files');
		 */
		files: function () {
			var mf = this.MultiFile('data');
			if(!mf) return !console.log('MultiFile plugin not initialized');
			return mf.files || [];
		},


		/**
		 * This method exposes the plugin's sum of the sizes of all files selected
		 *
		 * Returns size (in bytes) of files selected
		 *
		 * @name size
		 * @type Number
		 * @cat Plugins/MultiFile
		 * @author Diego A. (http://www.fyneworks.com/)
		 *
		 * @example $('#selector').MultiFile('size');
		 */
		size: function () {
			var mf = this.MultiFile('data');
			if(!mf) return !console.log('MultiFile plugin not initialized');
			return mf.total_size || 0;
		},


		/**
		 * This method exposes the plugin's tally of how many files have been selected
		 *
		 * Returns number (a count) of files selected
		 *
		 * @name count
		 * @type Number
		 * @cat Plugins/MultiFile
		 * @author Diego A. (http://www.fyneworks.com/)
		 *
		 * @example $('#selector').MultiFile('size');
		 */
		count: function () {
			var mf = this.MultiFile('data');
			if(!mf) return !console.log('MultiFile plugin not initialized');
			return mf.files ? mf.files.length || 0 : 0;
		},


		/**
		 * This utility makes it easy to disable all 'empty' file elements in the document before submitting a form.
		 * It marks the affected elements so they can be easily re-enabled after the form submission or validation.
		 *
		 * Returns a jQuery collection of all affected elements.
		 *
		 * @name disableEmpty
		 * @type jQuery
		 * @cat Plugins/MultiFile
		 * @author Diego A. (http://www.fyneworks.com/)
		 *
		 * @example $.fn.MultiFile.disableEmpty();
		 * @param String class (optional) A string specifying a class to be applied to all affected elements - Default: 'mfD'.
		 */
		disableEmpty: function (klass) {
			klass = (typeof (klass) == 'string' ? klass : '') || 'mfD';
			var o = [];
			$('input:file.MultiFile').each(function () {
				if ($(this).val() == '') o[o.length] = this;
			});

			// automatically re-enable for novice users
			window.clearTimeout($.fn.MultiFile.reEnableTimeout);
			$.fn.MultiFile.reEnableTimeout = window.setTimeout($.fn.MultiFile.reEnableEmpty, 500);
			
			return $(o).each(function () {
				this.disabled = true
			}).addClass(klass);
		},


		/**
		 * This method re-enables 'empty' file elements that were disabled (and marked) with the $.fn.MultiFile.disableEmpty method.
		 *
		 * Returns a jQuery collection of all affected elements.
		 *
		 * @name reEnableEmpty
		 * @type jQuery
		 * @cat Plugins/MultiFile
		 * @author Diego A. (http://www.fyneworks.com/)
		 *
		 * @example $.fn.MultiFile.reEnableEmpty();
		 * @param String klass (optional) A string specifying the class that was used to mark affected elements - Default: 'mfD'.
		 */
		reEnableEmpty: function (klass) {
			klass = (typeof (klass) == 'string' ? klass : '') || 'mfD';
			return $('input:file.' + klass).removeClass(klass).each(function () {
				this.disabled = false
			});
		},


		/**
		* This method will intercept other jQuery plugins and disable empty file input elements prior to form submission
		*

		* @name intercept
		* @cat Plugins/MultiFile
		* @author Diego A. (http://www.fyneworks.com/)
		*
		* @example $.fn.MultiFile.intercept();
		* @param Array methods (optional) Array of method names to be intercepted
		*/
		intercepted: {},
		intercept: function (methods, context, args) {
			var method, value;
			args = args || [];
			if (args.constructor.toString().indexOf("Array") < 0) args = [args];
			if (typeof (methods) == 'function') {
				$.fn.MultiFile.disableEmpty();
				value = methods.apply(context || window, args);
				//SEE-http://code.google.com/p/jquery-multifile-plugin/issues/detail?id=27
				setTimeout(function () {
					$.fn.MultiFile.reEnableEmpty()
				}, 1000);
				return value;
			};
			if (methods.constructor.toString().indexOf("Array") < 0) methods = [methods];
			for (var i = 0; i < methods.length; i++) {
				method = methods[i] + ''; // make sure that we have a STRING
				if (method)(function (method) { // make sure that method is ISOLATED for the interception
					$.fn.MultiFile.intercepted[method] = $.fn[method] || function () {};
					$.fn[method] = function () {
						$.fn.MultiFile.disableEmpty();
						value = $.fn.MultiFile.intercepted[method].apply(this, arguments);
						//SEE http://code.google.com/p/jquery-multifile-plugin/issues/detail?id=27
						setTimeout(function () {
							$.fn.MultiFile.reEnableEmpty()
						}, 1000);
						return value;
					}; // interception
				})(method); // MAKE SURE THAT method IS ISOLATED for the interception
			}; // for each method
		} // $.fn.MultiFile.intercept

	});

	/*--------------------------------------------------------*/

	/*
	### Default Settings ###
	eg.: You can override default control like this:
	$.fn.MultiFile.options.accept = 'gif|jpg';
	*/
	$.fn.MultiFile.options = { //$.extend($.fn.MultiFile, { options: {
		accept: '', // accepted file extensions
		max: -1, // maximum number of selectable files
		maxfile: -1, // maximum size of a single file
		maxsize: -1, // maximum size of entire payload

		// name to use for newly created elements
		namePattern: '$name', // same name by default (which creates an array)
		/*master name*/ // use $name
		/*master id */ // use $id
		/*group count*/ // use $g
		/*slave count*/ // use $i
		/*other	 */ // use any combination of he above, eg.: $name_file$i

		// previews
		preview: false,
		previewCss: 'max-height:100px; max-width:100px;',

		// STRING: collection lets you show messages in different languages
		STRING: {
			remove: 'x',
			denied: 'You cannot select a $ext file.\nTry again...',
			file: '$file',
			selected: 'File selected: $file',
			duplicate: 'This file has already been selected:\n$file',
			toomuch: 'The files selected exceed the maximum size permited ($size)',
			toomany: 'Too many files selected (max: $max)',
			toobig: '$file is too big (max $size)'
		},

		// name of methods that should be automcatically intercepted so the plugin can disable
		// extra file elements that are empty before execution and automatically re-enable them afterwards
		autoIntercept: ['submit', 'ajaxSubmit', 'ajaxForm', 'validate', 'valid' /* array of methods to intercept */ ],

		// error handling function
		error: function (s) {

			if(typeof console != 'undefined') console.log(s);

			// TODO: add various dialog handlers here?
			alert(s);
		}
	}; //} });

	/*--------------------------------------------------------*/

	/*
	### Additional Methods ###
	Required functionality outside the plugin's scope
	*/

	// Native input reset method - because this alone doesn't always work: $(element).val('').attr('value', '')[0].value = '';
	$.fn.reset = $.fn.reset || function () {
		return this.each(function () {
			try {
				this.reset();
			} catch (e) {}
		});
	};

	/*--------------------------------------------------------*/

	/*
	### Default implementation ###
	The plugin will attach itself to file inputs
	with the class 'multi' when the page loads
	*/
	$(function () {
		//$("input:file.multi").MultiFile();
		$("input[type=file].multi").MultiFile();
	});



	/*# AVOID COLLISIONS #*/
})(jQuery);
/*# AVOID COLLISIONS #*/
