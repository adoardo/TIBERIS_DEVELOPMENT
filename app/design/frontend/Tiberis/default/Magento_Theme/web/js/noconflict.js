console.log('jquery noconklict DEFINE');
define(["jquery"], function($) {
    //drop the `true` if you want jQuery (but not $) to remain global
    return jQuery.noConflict(true);
});