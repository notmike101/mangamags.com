/* ==============================================
DYNAXEL DESIGN
TRANQUIL
PACKAGE.JS
- - - - - 
Last Updated: Nov 02/12
Author: Jessie Sanford
=============================================== */

$.noConflict();

jQuery(document).ready(function() {

var colorOne = "#687880"

jQuery("a.panel_toggle").toggle(
	function() {
	    jQuery(this).stop(true, true).animate({
		backgroundColor: "#bdd6df",
		borderBottomLeftRadius: 0, 
		borderBottomRightRadius: 0,
		color: "#76868b"
		}, 200);
		jQuery("#panel").stop(true, true).fadeIn();
		},
		
	function() {
	    jQuery(this).stop(true, true).animate({
		backgroundColor: "#a2b9c1",
		borderBottomLeftRadius: 2, 
		borderBottomRightRadius: 2,
		color: "#fff"
		}, 200);
		jQuery("#panel").stop(true, true).fadeOut();
		}
);

jQuery("tr.forum").hover(
    function() {
        jQuery(this).children("td").children("div").children(".forum_stats_wrap").stop(true, true).fadeIn(200);
		},
    function() {
        jQuery(this).children("td").children("div").children(".forum_stats_wrap").stop(true, true).fadeOut(200);
		}
); 

jQuery("div.expcolimage").toggle(
    function() {
        jQuery(this).parent("td.thead").parent("tr").parent("thead").parent("table").stop(true, true).animate({
		opacity: 0.5
		}, 200);
        jQuery(this).parent("td.thead").parent("tr").parent("thead").parent("table").children("tbody").children("tr").stop(true, true).fadeOut();
		},
    function() {
        jQuery(this).parent("td.thead").parent("tr").parent("thead").parent("table").stop(true, true).animate({
		opacity: 1
		}, 200);
        jQuery(this).parent("td.thead").parent("tr").parent("thead").parent("table").children("tbody").children("tr").stop(true, true).fadeIn();
		}
); 


/* MENU ============================================================ */

jQuery('.menu ul a').hover(
    function() {
        jQuery(this).not('#portal .portal, #forums .forums, #search .search, #memberlist .memberlist, #calendar .calendar, #help .help, #home .home').stop(true, true).animate({
		backgroundColor: "#fff",
		color: "#606060"
		}, 200);
		},
    function() {
        jQuery(this).not('#portal .portal, #forums .forums, #search .search, #memberlist .memberlist, #calendar .calendar, #help .help, #home .home').stop(true, true).animate({
		backgroundColor: "#f6f6f6",
		color: "#808080"
		}, 300);
    	}
); 

/* TEXTBOX/BUTTON ============================================================ */

jQuery(".adv_search").css({opacity: 0});

jQuery('.search_box input.textbox').focus(
    function() {
    	jQuery(this).stop(true, true).animate({
		width: "200px",
		backgroundColor: "#546167", 
		borderColor: "#4c575c"
		}, 300);
		jQuery(".adv_search").delay(300).animate({
		opacity: 1
		}, 400);
    	}
);
jQuery('.search_box input.textbox').blur(
    function() {
    	jQuery(this).stop(true, true).delay(300).animate({
		width: "120px",
		backgroundColor: "#5f6d74", 
		borderColor: "#525e64" 
		}, 300);
		jQuery(".adv_search").animate({
		opacity: 0
		}, 200);
    	}
);

jQuery('#panel input.textbox').focus(
    function() {
    	jQuery(this).stop().animate({
		backgroundColor: "#a2b7be", 
		borderColor: "#99acb3"
		}, 300);
    	}
);
jQuery('#panel input.textbox').blur(
    function() {
    	jQuery(this).stop().animate({
		backgroundColor: "#aec5cd", 
		borderColor: "#a5bac2" 
		}, 300);
    	}
);

jQuery(".css_button, .css_button_large").hover(
    function() {
	    jQuery(this).stop(true, true).animate({
		backgroundColor: "#a2b9c1",
		color: "#fff"
		}, 200);
		},
    function() {
	    jQuery(this).stop(true, true).animate({
		backgroundColor: "#dadada",
		color: "#808080"
		}, 200);
		}
); 




});