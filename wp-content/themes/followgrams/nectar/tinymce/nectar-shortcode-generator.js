jQuery(document).ready(function($){
	
	$('.nectar-shortcode-generator').magnificPopup({
		type:'inline',
  		removalDelay: 500, 
		callbacks: {
		    beforeOpen: function() {
		       this.st.mainClass = this.st.el.attr('data-effect');
		    }
	    }
	});

});
