require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    if (window.location.hash == '#reviews') {
        $('#tab-label-reviews').click();
    }
});
