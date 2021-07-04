(function($) {
	$(function(){
	  $('.tags input').on('focusout',function(){    
		var txt= this.value.replace(/[^a-zA-Z0-9\+\-\.\#]/g,'');
		if(txt) {
		  $(this).before('<span class="tag">'+ txt.toLowerCase() +'</span>');
		}
		this.value="";
	  }).on('keyup',function( e ){
		if(/(188|13)/.test(e.which)) $(this).focusout(); 
	  });
	  $('.tags').on('click','.tag',function(){
		$(this).remove(); 
	  });

	});
})(jQuery);

jQuery(function($) {
	$("#seoautoform").submit(function(e) {
	  var self = this;
	  e.preventDefault();
	  $('.tags').each(function() {
		var strng = "";
		$(this).children('span').each(function(){
		  if(strng !== ''){
			strng = strng + ', ' + $(this).text();
		  }
		  else {
			strng = $(this).text();
		  }
		  $(this).remove();
		});
		$(this).children('input').val(strng);
	  });
	  self.submit();
	});
});

jQuery(document).ready(function($) {
	$('.tags').each(function() {
     if($(this).children('input').val() !== '') {
       var array = $(this).children('input').val().split(",");
       var self = $(this).children('input');
       $.each(array,function(i){
         $('<span class="tag">'+array[i]+'</span>').insertBefore(self);
         self.val('');
       });
     }
    });
});