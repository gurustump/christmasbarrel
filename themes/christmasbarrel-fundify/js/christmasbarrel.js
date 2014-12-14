jQuery( document ).ready(function( $ ) {
	// Hide wp admin bar
	var adminBarMove = $('#wpadminbar').outerHeight()-1
	$('#wpadminbar').animate({
		'top':'-='+adminBarMove
	}, 2000,function() {
	}).hover(
		function(){
			$('#wpadminbar').stop().css('top','0').toggleClass('wpadminbar-shown');
		},
		function(){
			$('#wpadminbar').animate({
				'top':'-='+adminBarMove
			}, 2000).toggleClass('wpadminbar-shown');
		}
	).append('<div class="wpadminbar-activator"></div>');
	
	$(window).resize(function() {
		if(this.resizeTO) {clearTimeout(this.resizeTO)}
		this.resizeTO = setTimeout(function() {
			$(this).trigger('resizeEnd')
		}, 150)
	})
	
	$(window).bind('resizeEnd',function() {
		mobileCalendarLayout($)
	})
	mobileCalendarLayout($)
});

function mobileCalendarLayout($) {
	if (mobileCheck(425)) {
		$('.vid-nav > li > .advent-number').each(function() {
			newHeight = Math.floor($(this).outerWidth() * .555)
			$(this).height(newHeight).css({
				'line-height':newHeight * 2 / 3 + 'px',
				'font-size':newHeight / 2 + 'px'
			})
		})
	} else {
		$('.vid-nav > li > .advent-number').removeAttr('style')
	}
}


function mobileCheck(maxWidth) {
	if (jQuery(window).width() > maxWidth) {
		return false
	} else {
		return true
	}
}