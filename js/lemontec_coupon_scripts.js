(function ($, root, undefined) {
    $(function () {
        'use strict';
        // DOM ready, take it away     
        $('body').on( 'added_to_cart', function(){  
            $('.custom_cart_widget_wrapper').addClass('open');  
        });
        
        
        var slider = $("#lemontec_coupon_slider");
        console.log(slider);
        var output = $("#lemontec_coupon_slider_value");
        output.text(slider.val() + ' €');
        
        slider.on("change mousemove", function() {
            output.text($(this).val() + ' €');
        });
	});
    
    
	
})(jQuery, this);