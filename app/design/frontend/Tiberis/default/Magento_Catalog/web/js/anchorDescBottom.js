require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    $('.desc-top-anchor').click(function (event) {
        event.preventDefault();
        var acnchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
        jQuery('html, body').animate({
            scrollTop: $('.' + acnchor).offset().top - 50
        }, 300);
    });
});
