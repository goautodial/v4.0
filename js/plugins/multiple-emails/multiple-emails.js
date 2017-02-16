(function( $ ){
 
	$.fn.multiple_emails = function() {
		return this.each(function() {
			var $orig = $(this);
			$list = $('<ul class="multiple_emails-ul" />'); // create html elements - list of email addresses as unordered list

			if ($(this).val() != '' && IsJsonString($(this).val())) {
				$.each(jQuery.parseJSON($(this).val()), function( index, val ) {
					$list.append($('<li class="multiple_emails-email"><span class="email_name">' + val + '</span></li>')
					  .prepend($('<a href="#" class="multiple_emails-close" title="Remove"><span class="fa fa-remove"></span></a>')
						   .click(function(e) { $(this).parent().remove(); refresh_emails(); e.preventDefault(); })
					  )
					);
				});
			}
			
			var $input = $('<input id="'+$orig.attr('id')+'" placeholder="'+$orig.attr('placeholder')+'" type="text" class="multiple_emails-input text-left" />').on('keyup', function(event) { // input
				$(this).removeClass('multiple_emails-error');
				var input_length = $(this).val().length;
				
				//if(event.which == 8 && input_length == 0) { $list.find('li').last().remove(); }
				if(event.which == 9 || event.which == 13 || event.which == 32 || event.which == 188) { // key press is enter, space or comma
					display_email($(this));
				}
			}).on('blur', function(event){ 
				if ($(this).val() != '') { display_email($(this)); }
			});

			var $container = $('<div class="multiple_emails-container" />').click(function() { $input.focus(); } ); // container div
 
			$container.append($list).append($input).insertAfter($(this)); // insert elements into DOM

			function display_email(t) {
				var val = t.val().replace(/,/g , '').replace(/ /g , ''); // remove space/comma from value

				var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
				if (pattern.test(val) == true) {
					 $list.append($('<li class="multiple_emails-email"><span class="email_name">' + val + '</span></li>')
						  .prepend($('<a href="#" class="multiple_emails-close" title="Remove"><span class="glyphicon glyphicon-remove"></span></a>')
							   .click(function(e) { $(this).parent().remove(); refresh_emails(); e.preventDefault(); })
						  )
					);
					refresh_emails ();
					t.val('');
				}
				else { t.val(val).addClass('multiple_emails-error'); }
			}
			
			function refresh_emails () {
				var emails = new Array();
				$('.multiple_emails-email span.email_name').each(function() { emails.push($(this).html());	});
				$orig.val(JSON.stringify(emails)).trigger('change');
			}
			
			function IsJsonString(str) {
				try { JSON.parse(str); }
				catch (e) {	return false; }
				return true;
			}
			
			return $(this).hide();
 
		});
		
	};
	
})(jQuery);
