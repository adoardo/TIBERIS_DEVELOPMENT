require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {

    } else {
        $('.product-item ').hover(function(){
            // Получаем полное название
            var temp = $(this).find('.product-item-link').attr('name');
            // Записываем полное название в тег
            $(this).find('.product-item-link').html(temp);
        }).mouseleave(function(){
            // Получаем краткое название
            var temp2 = $(this).find('.product-item-link').attr('name2');
            $(this).find('.product-item-link').html(temp2);
        });
    }


});
