$(function() {
	//smartScroll($('.scroll-nav'))
	simpleScroll($('.SCROLL_CONTAINER'))
})

simpleScroll = function(scrollContainer) {
	var scrollPanel = scrollContainer.find('.SCROLL_NAV')
	scrollContainer.find('.SCROLL_CONTROL').click(function(e) {
		e.preventDefault()
		var dirSwitch = $(this).hasClass('SCROLL_PREV')? -1 : 1
		scrollPanel.animate({'scrollLeft':scrollPanel.scrollLeft()+(scrollPanel.width()+16)*dirSwitch}, 500, function() {
			console.log(scrollPanel.scrollLeft())
			console.log(scrollPanel.width())
			console.log(scrollPanel.find('.vid-nav').width())
			fadeControls()
		})
	})
	function fadeControls() {
		if (scrollPanel.scrollLeft() <= 0) {
			$('.SCROLL_PREV').fadeOut()
		} else {
			$('.SCROLL_PREV').fadeIn()
		}
		if (scrollPanel.scrollLeft() >= (scrollPanel.find('.vid-nav').width()-scrollPanel.width())) {
			$('.SCROLL_NEXT').fadeOut()
		} else {
			$('.SCROLL_NEXT').fadeIn()
		}
	}
	fadeControls()
}
smartScroll = function (scrollContainer) {
	scrollContainer.on('mouseenter',function(event){
		$(this).children().stop(true,true)//.fadeIn();
		var windowSize = $(window).width();
		var containerSize = scrollContainer.width();
		var thumbSize = scrollContainer.children().children().outerWidth();
		var carouselSize = thumbSize*scrollContainer.children().children().length;
		var nonScrollablePercent = thumbSize/containerSize;
		var scrollablePercent = 1 - (2*nonScrollablePercent);
		scrollContainer.children().css('width',carouselSize);
		var relativePositionX = event.clientX-scrollContainer.offset().left;
		var smartScroll = Math.round((((relativePositionX - nonScrollablePercent * containerSize) / (scrollablePercent * containerSize)) * (carouselSize - containerSize)));
		scrollContainer.animate({'scrollLeft':smartScroll},400, function(){
			scrollContainer.mousemove(function(event){
				var relativePositionX = event.clientX-scrollContainer.offset().left;
				var smartScroll = Math.round((((relativePositionX - nonScrollablePercent * containerSize) / (scrollablePercent * containerSize)) * (carouselSize - containerSize)));
				scrollContainer.scrollLeft(smartScroll);
			});
		});
	});
	scrollContainer.on('mouseleave',function(event){
		//$(this).unbind('mousemove').children().fadeOut();
	});
}
smartScrollSetter = function(scrollContainer,nonScrollablePercent,containerSize,scrollablePercent,carouselSize) {
	var relativePositionX = event.clientX-scrollContainer.offset().left;
	var smartScroll = Math.round((((relativePositionX - nonScrollablePercent * containerSize) / (scrollablePercent * containerSize)) * (carouselSize - containerSize)));
}