var tl;

function setAnimation(animation, delay) {
    if (typeof(tl) != 'undefined') {
        tl.pause(tl.duration()).kill();
        apContainer.css({opacity: 1, visibility: 'visible'});
        apForm.css({opacity: 1, visibility: 'visible'});
        // apInput.css({top: 0, opacity: 1, transform: 'unset'});
        // apButton.css({top: 0, opacity: 1, transform: 'unset'});
    }
    tl = new TimelineMax({repeat: -1});
    tl.delay(delay);
    tl.to(apCover, 0.00000001, {bottom: 0, autoAlpha: 1});
    tl.to(apBg, 0.00000001, {autoAlpha: 1});

    if (animation === 'tornado') {
        tl.repeatDelay(10);
        tl.staggerTo([apContainer,apInput,apButton], 1, {rotationY: 360}, 0.5);
        tl.to({}, 10, {});
        tl.staggerTo([apContainer,apInput,apButton], 1, {rotationY: 0}, 0.5);
    } else if (animation === 'creeper') {
        tl.repeatDelay(10);
        tl.to(apContainer, 4, {scale: 1.1, rotation: 0, ease: Power3.easeOut});
        tl.to(apContainer, 4, {left:-10, rotation: -8, scale: 1.8, ease: Power3.easeOut});
        tl.to(apContainer, 1, {left: 0, rotation: 0, scale: 1, ease: Elastic.easeOut});
    } else if (animation === 'gyroscope') {
        tl.repeatDelay(10);
        for (var i = 0; i < 5; i += 1) {
            tl.to(apContainer, 0.5, {top: 40, ease: Power0.easeIn});
            tl.to(apContainer, 0.25, {rotation: -15, ease: Power1.easeOut}, '-=0.5');
            tl.to(apContainer, 0.25, {rotation: 0, ease: Power1.easeIn}, '-=0.25');
            tl.to(apContainer, 0.5, {top: 0, ease: Power0.easeIn});
            tl.to(apContainer, 0.25, {rotation: 15, ease: Power1.easeOut}, '-=0.5');
            tl.to(apContainer, 0.25, {rotation: 0, ease: Power1.easeIn}, '-=0.25');
        }
    } else if (animation === 'trampoline') {
        tl.staggerFrom([apContainer,apInput,apButton], 2, {scale: 0.5, autoAlpha: 0, delay: 0.5, ease: Elastic.easeOut}, 0.2);
        tl.to({}, 10, {});
        tl.to(apContainer, 1, {autoAlpha: 0});
    }
}
