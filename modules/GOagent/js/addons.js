(function($) {
    $.fn.drags = function(opt) {

        opt = $.extend({handle:"",cursor:"move"}, opt);
        var $el = this;

        if(opt.handle !== "") {
            $el = this.find(opt.handle);
        }

        return $el.css('cursor', opt.cursor).on("mousedown", function(e) {
            var $drag = $(this).addClass('draggable');
            if(opt.handle !== "") {
                $drag = $(this).addClass('active-handle').parent().addClass('draggable');
            }
            var z_idx = $drag.css('z-index'),
                drg_h = $drag.outerHeight(),
                drg_w = $drag.outerWidth(),
                pos_y = $drag.offset().top + drg_h - e.pageY,
                pos_x = $drag.offset().left + drg_w - e.pageX;
            $drag.css('z-index', 1000).parents().on("mousemove", function(e) {
                $('.draggable').offset({
                    top:e.pageY + pos_y - drg_h,
                    left:e.pageX + pos_x - drg_w
                }).on("mouseup", function() {
                    var tTop = parseInt($(this).css('top'));
                    var tLeft = parseInt($(this).css('left'));
                    var tHeader = parseInt($("header").outerHeight()) + 10;
                    var tRight = parseInt($("div.wrapper").innerWidth());
                    var tBottom = parseInt($("div.wrapper").innerHeight());
                    var tPosX = tLeft + parseInt($(this).outerWidth());
                    var tPosY = tTop + parseInt($(this).outerHeight());
                    
                    if (tTop < tHeader) {
                        $(this).css('top', tHeader + 'px');
                    }
                    
                    if (tLeft < 0) {
                        $(this).css('left', '5px');
                    }
                    
                    if (tRight < tPosX) {
                        var tNewPosX = tRight - parseInt($(this).outerWidth()) - 5;
                        $(this).css('left', tNewPosX + 'px');
                    }
                    if (tBottom < tPosY) {
                        var tNewPosY = tBottom - parseInt($(this).outerHeight()) - 10;
                        $(this).css('top', tNewPosY + 'px');
                    }
                    
                    if(opt.zIndex) {z_idx = opt.zIndex;}
                    $(this).removeClass('draggable').css('z-index', z_idx);
                });
            });
            e.preventDefault(); // disable selection
        }).on("mouseup", function() {
            if(opt.handle === "") {
                $(this).removeClass('draggable');
            } else {
                $(this).removeClass('active-handle').parent().removeClass('draggable');
            }
        });

    };
})(jQuery);

jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}