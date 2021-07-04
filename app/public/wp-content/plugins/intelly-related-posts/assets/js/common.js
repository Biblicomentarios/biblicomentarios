//IntellyWP
jQuery('.wrap .updated.fade').remove();
jQuery('.woocommerce-message').remove();
jQuery('.error').remove();
jQuery('.info').remove();
jQuery('.update-nag').remove();

jQuery(function() {
    "use strict";
    //WooCommerce errors
    var removeWooUpdateTheme = setInterval(function () {
        if (jQuery('.wrap .updated.fade').length > 0) {
            jQuery('.wrap .updated.fade').remove();
            clearInterval(removeWooUpdateTheme);
        }
    }, 100);
    var removeWooMessage = setInterval(function () {
        if (jQuery('.woocommerce-message').length > 0) {
            jQuery('.woocommerce-message').remove();
            clearInterval(removeWooMessage);
        }
    }, 100);

    jQuery('.wrap .updated.fade').remove();
    jQuery('.woocommerce-message').remove();
    jQuery('.error').remove();
    jQuery('.info').remove();
    jQuery('.update-nag').remove();
});

function IRP_stripos(haystack, needle, offset) {
    //  discuss at: http://phpjs.org/functions/stripos/
    // original by: Martijn Wieringa
    //  revised by: Onno Marsman
    //   example 1: stripos('ABC', 'a');
    //   returns 1: 0

    var haystack = (haystack + '').toLowerCase();
    var needle = (needle + '').toLowerCase();
    var index = 0;

    if ((index = haystack.indexOf(needle, offset)) !== -1) {
        return index;
    }
    return false;
}
function IRP_val(name) {
    return jQuery('[name='+name+']').val();
}
function IRP_check(name) {
    return (jQuery('[name='+name+']').is(':checked') ? 1 : 0);
}
function IRP_radio(name) {
    return (jQuery('[name='+name+']:checked').val());
}
function IRP_visible(name, visible) {
    if(visible) {
        jQuery(name).hide();
    } else {
        jQuery(name).show();
    }
}
function IRP_aval(name) {
    var data={};
    jQuery("[name^='"+name+"']").each(function(i,v) {
        var $this=jQuery(this);
        var k=$this.attr('name');
        var v=$this.val();
        if($this.attr('type')=='checkbox') {
            v=IRP_check(k);
        } else if($this.attr('type')=='radio') {
            v=IRP_radio(k);
        }
        data[k]=v;
    });
    //console.log(data);
    return data;
}
function IRP_formatColorOption(option) {
    if (!option.id) {
        return option.text;
    }

    var color=jQuery(option.element).css('background-color');
    var font=jQuery(option.element).css('color');
    var $option = jQuery('<div></div>')
        .html(option.text)
        .css('background-color', color)
        .css('color', font)
        .addClass('irpColorSelectItem');
    return $option;
}
function IRP_hideShow(v) {
    var $source = jQuery(v);
    if ($source.attr('irp-hideIfTrue') && $source.attr('irp-hideShow')) {
        var $destination = jQuery('[name=' + $source.attr('irp-hideShow') + ']');
        if ($destination.length == 0) {
            $destination = jQuery('#' + $source.attr('irp-hideShow'));
        }
        if ($destination.length > 0) {
            var isChecked = $source.is(":checked");
            var hideIfTrue = ($source.attr('irp-hideIfTrue').toLowerCase() == 'true');

            if (isChecked) {
                if (hideIfTrue) {
                    $destination.hide();
                } else {
                    $destination.show();
                }
            } else {
                if (hideIfTrue) {
                    $destination.show();
                } else {
                    $destination.hide();
                }
            }
        }
    }
}

jQuery(function() {
    jQuery(".irp-hideShow").click(function () {
        IRP_hideShow(this);
    });
    jQuery(".irp-hideShow").each(function () {
        IRP_hideShow(this);
    });

    if(jQuery(".irpTags").length>0) {
        jQuery(".irpTags").select2({
            placeholder: "Type here..."
            , theme: "classic"
            , width: '300px'
        });
    }

    if(jQuery(".irpColorSelect").length>0) {
        jQuery(".irpColorSelect").select2({
            placeholder: "Type here..."
            , theme: "classic"
            , width: '300px'
            , formatResult: IRP_formatColorOption
            , formatSelection: IRP_formatColorOption
            , escapeMarkup: function(m) {
                return m;
            }
        });
    }

    if(jQuery('.irp-help').qtip) {

        jQuery.browser = {};
        (function () {
        jQuery.browser.msie = false;
        jQuery.browser.version = 0;
        if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
        }
        })();

        jQuery('.irp-help').qtip({
            position: {
                corner: {
                    target: 'topMiddle',
                    tooltip: 'bottomLeft'
                }
            },
            show: {
                when: {
                    event: 'mouseover'
                }
            },
            hide: {
                fixed: true,
                when: {
                    event: 'mouseout'
                }
            },
            style: {
                tip: 'bottomLeft',
                name: 'green'
            }
        });
    }
});
