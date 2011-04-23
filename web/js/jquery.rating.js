function trim (str, charlist) {
    // Strips whitespace from the beginning and end of a string
    //
    // version: 1008.1718
    // discuss at: http://phpjs.org/functions/trim
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: mdsjack (http://www.mdsjack.bo.it)
    // +   improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
    // +      input by: Erkekjetter
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: DxGx
    // +   improved by: Steven Levithan (http://blog.stevenlevithan.com)
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // *     example 1: trim('    Kevin van Zonneveld    ');
    // *     returns 1: 'Kevin van Zonneveld'
    // *     example 2: trim('Hello World', 'Hdle');
    // *     returns 2: 'o Wor'
    // *     example 3: trim(16, 1);
    // *     returns 3: 6
    var whitespace, l = 0, i = 0;
    str += '';

    if (!charlist) {
        // default list
        whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    } else {
        // preg_quote custom list
        charlist += '';
        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
    }

    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(i);
            break;
        }
    }

    l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break;
        }
    }

    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}


(function($) {
    var buildRating = function(obj) {
        var rating = averageRating(obj),
            obj    = buildInterface(obj),
            stars  = $("div.star", obj),
            cancel = $("div.cancel", obj),
            disabled = obj.hasClass("disabled");

        var fill = function() {
            drain();

            $("a", stars).css("width", "100%");
            $('div.star:lt('+(stars.index(this) + 1)+')', obj).addClass("hover");
        },

        drain = function() {
            stars.removeClass("on").removeClass("hover");
        },

        reset = function() {
            drain();

            percent = rating[1] ? rating[1] : null;

            $('div.star:lt('+(rating[0])+')', obj).addClass("on");

            if(percent)
                stars.eq(rating[0]).addClass("on").children("a").css("width", (percent*10) + "%");
        },

        cancelOn = function() {
            drain();

            $(this).addClass("on");
        },

        cancelOff = function() {
            reset();

            $(this).removeClass("on")
        }

        if(disabled) {
            $("a", stars).css("cursor", "default").click(function() {
                return false;
            });
        } else {
            stars.hover(fill, reset).focus(fill).blur(reset)
                 .click(function() {
                    rating = [stars.index(this) + 1, 0];

                    $.post(obj.url, {
                                'rate[value]': $(this).text(),
                                'rate[id]': obj.note_id,
                                'rate[_csrf_token]': obj.note__csrf_token
                            }, function(data) {
                                //$('#anime_rate').attr('title', data);

                                //rating = data.split('.');
                                $('#rating_msg').html('A voté !').show().fadeOut(3000);

                                //reset();
                            }
                        );

                    reset();

                    stars.unbind().addClass("done");
                    $(this).css("cursor", "default");

                    return false;
                });
        }

        reset();

        return obj;
    }

    var buildInterface = function(form) {
        var container = $("<div></div>").attr({"title": form.title, "class": form.className});
        $.extend(container, {url: form.action});

        var optGroup  = $("option", $(form));
        var size      = optGroup.length;

        optGroup.each(function() {
            container.append($('<div class="star"><a href="#' + this.value + '" title="' + this.value
                               + '/'+ size +'">' + this.value + '</a></div>'));
        });
        
        container.append($('<span id="rating_msg"></span>').attr({"class": "hidden"}));
        
        $.extend(container, {
          note_id: $('#rate_id').val(),
          note__csrf_token: $('#rate__csrf_token').val()
        });

        $(form).after(container).remove();

        return container;
    }

    var averageRating = function(el) {
        return trim(el.title.split(":")[1]).split('.');
    }

    $.fn.rating = function() {
        return $($.map(this, function(i) { return buildRating(i)[0] }));
    }

    if ($.browser.msie) {
        try {
            document.execCommand("BackgroundImageCache", false, true);
        } catch(e) { }
    }
})(jQuery)


jQuery(document).ready(function(){
    jQuery('form.rating').rating().animate({opacity: 'show'}, 2000);
});
