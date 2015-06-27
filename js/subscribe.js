// check cookie first
// localize needs enabled array and animation type

var subscribed = true;
for (var i = 0; i < apLocalize.enabled.length; i += 1) {
    if (!jQuery.cookie('ap_' + apLocalize.enabled[i])) {
        subscribed = false;
    }
}


// jQuery.expr[':'].focus = function(elem) {
//     return elem === document.activeElement && (elem.type || elem.href);
// };

jQuery(window).load(function() {
    apBg.css('height', apContainer.css('height'));
});


jQuery(function () { // front end only code
    apLocalize = window.apLocalize;

    if (apLocalize.popup_or_embed === 'popup') {
        jQuery('#' + apLocalize.widget_id).css('margin', 0); // remove widget margin
        apCover.appendTo(document.body); // transplant to body
        jQuery(document.body).css('position', 'relative');

        jQuery(document).on('click', function (e) {
            var event = e || window.event;
            var target = event.target || event.srcElement;
            if (target.id === 'popup-closer') {
                apCover.remove();
            }
        });

        setTimeout(function () {
            apBg.css('top', jQuery(window).height() / 2 + jQuery(window).scrollTop());
            jQuery(window).resize(function (e) {
                apBg.css('top', jQuery(window).height() / 2 + jQuery(window).scrollTop());
            });
            jQuery(window).scroll(function (e) {
                apBg.css('top', jQuery(window).height() / 2 + jQuery(window).scrollTop());
            });
        }, 4000);
    }

    var frozen = 0;
    jQuery(document).on('focusin', 'input', function (e) {
        var event = e || window.event;
        var target = event.target || event.srcElement;
        if (jQuery(target).hasClass('ap-input')) {
            setAnimation('none', 0);
            frozen = 1;
        }
    });

    setTimeout(function () {
        jQuery(document).mouseup(function (e) {
            if (frozen === 0) { return; }
            else if (frozen === 1) { frozen = 2; return; }
            // if froze is 2, then continue
            var event = e || window.event;
            var target = event.target || event.srcElement;
            if (!apContainer.is(e.target)
            && apContainer.has(e.target).length === 0) {
                setAnimation(apLocalize.animation, 0);
                frozen = 0;
            }
        });
    }, 4000);

    if (apLocalize.enabled.length === 0 || !subscribed) {

        setAnimation(apLocalize.animation, 4);

        apButton.on('click', function (e) {
            e.preventDefault();
            jQuery.post(apLocalize.ajaxurl, {
                email: apInput.val(),
                action: 'myajax-submit',
                subscribeNonce: apLocalize.subscribeNonce
            }, function (data) {
                console.log(data);
                var result = JSON.parse(data);
                if (!result.validation) return;
                for (var i = 0; i < result.enabled.length; i += 1) {
                    jQuery.cookie('ap_' + result.enabled[i], 'true', {expires: 1825, path: '/'});
                }
            });
        });
    } else {
        apButton.on('click', function (e) {
            e.preventDefault();
        });
    }

});
