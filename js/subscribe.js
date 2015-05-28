// check cookie first
// localize needs enabled array and animation type

var noIdea = localize;
var subscribed = true;
for (var i = 0; i < noIdea.enabled.length; i += 1) {
    if (!jQuery.cookie('ap_' + noIdea.enabled[i])) {
        subscribed = false;
    }
}


// jQuery.expr[':'].focus = function(elem) {
//     return elem === document.activeElement && (elem.type || elem.href);
// };

jQuery(function () { // front end only code

    apBg.css('height', apContainer.css('height'));

    if (noIdea.popup_or_embed === 'popup') {
        jQuery('#' + noIdea.widget_id).css('margin', 0); // remove widget margin
        apCover.appendTo(document.body); // transplant to body

        jQuery(document).click(function (e) {
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

    setTimeout(function () {
        jQuery(document).mouseup(function (e) {
            var event = e || window.event;
            var target = event.target || event.srcElement;
            if (!apContainer.is(e.target)
            && apContainer.has(e.target).length === 0) {
                setAnimation(noIdea.animation, 0);
            }
        });
    }, 4000);

    jQuery(document).on('focusin', 'input', function (e) {
        var event = e || window.event;
        var target = event.target || event.srcElement;
        if (jQuery(target).hasClass('ap-input')) {
            setAnimation('none', 0);
        }
    });


    if (noIdea.enabled.length === 0 || !subscribed) {

        setAnimation(noIdea.animation, 4);

        apForm.submit(function (e) {
            e.preventDefault();
            jQuery.post(noIdea.ajaxurl, {
                email: apInput.val(),
                action: 'myajax-submit',
                subscribeNonce: noIdea.subscribeNonce
            }, function (data) {
                console.log(data);
                if (!result.validation) return;
                var result = JSON.parse(data);
                for (var i = 0; i < result.enabled.length; i += 1) {
                    jQuery.cookie('ap_' + result.enabled[i], 'true', {expires: 1825, path: '/'});
                }
            });
        });
    } else {
        apForm.submit(function (e) {
            e.preventDefault();
        });
    }

});
