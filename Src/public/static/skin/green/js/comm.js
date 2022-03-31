$(document).ready(function() {
  
  
	//nav
    var obj=null;
    var As=document.getElementById('starlist').getElementsByTagName('a');
    obj = As[0];
    for(i=1;i<As.length;i++){if(window.location.href.indexOf(As[i].href)>=0)
    obj=As[i];}
    obj.id='selected';
	
	
	 //nav
    $("#mnavh").click(function() {
        $("#starlist").toggle();
        $("#mnavh").toggleClass("open");
    });

 //search
	$(".is-search,.go-left").click(function() {
        $(".search").toggle();
    });	

	   //scroll to top
        var offset = 300,
        offset_opacity = 1200,
        scroll_top_duration = 700,
        $back_to_top = $('.icon-top');
		
    $(window).scroll(function () {
        ($(this).scrollTop() > offset) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
        if ($(this).scrollTop() > offset_opacity) {
            $back_to_top.addClass('cd-fade-out');
        }
    });
	 $back_to_top.on('click', function (event) {
        event.preventDefault();
        $('body,html').animate({
                scrollTop: 0,
            }, scroll_top_duration
        );
    });
	
});

	