jQuery.noConflict();
jQuery(document).ready(function($){
	$('ul.menu li a[title]').tooltip({
        effect: 'slide',
        position: 'top center',
        direction: "down",
        bounce: true
    });
});
