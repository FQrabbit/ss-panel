
/* Background Images
-------------------------------------------------------------------*/
var sections = $('.section-style');
for (var i = sections.length - 1; i >= 0; i--) {
  var section = sections[i];
  var imgUrl = section.dataset.backgroundImage;
  if (imgUrl) {
    section.style.backgroundImage = 'url('+ imgUrl + ')';
  }
}
/* Background Images End
-------------------------------------------------------------------*/



/* Document Ready function
-------------------------------------------------------------------*/
jQuery(document).ready(function($) {
	"use strict";


    /* Window Height Resize
    -------------------------------------------------------------------*/
    var windowheight = jQuery(window).height();
    if(windowheight > 650)
    {
     $('.pattern').removeClass('height-resize');
   }
    /* Window Height Resize End
    -------------------------------------------------------------------*/


    
	/* Main Menu   
	-------------------------------------------------------------------*/
	$('#main-menu #headernavigation').onePageNav({
		currentClass: 'active',
		changeHash: false,
		scrollSpeed: 750,
		scrollThreshold: 0.5,
		scrollOffset: 0,
		filter: '',
		easing: 'swing'
	});  

	/* Main Menu End  
	-------------------------------------------------------------------*/


/* Next-section Start 
-------------------------------------------------------------------*/
$("a").on('click', function(event) {
    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
      // Prevent default anchor click behavior
      event.preventDefault();
      $('html, body').stop();
      // Store hash
      var hash = this.hash;
      document.querySelector(hash).scrollIntoView({behavior:'smooth'});
    } // End if
  });
 /* Next-section End
 -------------------------------------------------------------------*/
/* Document Ready function End
-------------------------------------------------------------------*/
});

/* Preloder 
-------------------------------------------------------------------*/
$(window).load(function () {    
  "use strict";
  $("#loader").fadeOut();
  $("#preloader").delay(350).fadeOut("slow");
});

setTimeout(function(){
  $("#preloader").fadeOut("slow");
},5000);
 /* Preloder End
 -------------------------------------------------------------------*/

