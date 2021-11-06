<style>

.wal-pro
{
    opacity: 0.4;
}

.white-popup-block {
    display: inline-block;
    padding: 25px 40px !important;
}

.mfp-auto-cursor .mfp-content {
    text-align: left;
}

.mfp-wrap
{
	z-index: 100000;
	background: white;
	/*margin:25px 15px 15px 15px;
	padding:25px 15px 15px 15px;*/
    position: fixed;   /* Take it out of the flow of the document */
    left: 0;           /* Left edge at left for now */
    right: 0;          /* Right edge at right for now, so full width */ 
    top: 20px;         /* Move it down from top of window */
    width: 96%;      /* Give it the desired width */ 
	height: 96%;
    margin: auto;      /* Center it */
    max-width: 100%;   /* Make it fit window if under 500px */ 

}

.mfp-wrap #wpfooter
{
	display:none;
}

</style>

<script>

(function( $ ) {
	'use strict';



	$( window ).load(function() {
		//$('.wal-pro').css('background', 'red');
        $('.wal-pro, .wal-pro a, .wal-pro button, .wal-pro input').unbind();

        $('.wal-pro, .wal-pro a, .wal-pro button, .wal-pro input').on('click', actt_gopremium);
        
        $('.wal-pro, .wal-pro a, .wal-pro button, .wal-pro input').on('focus', function(){
            $(this).trigger('blur'); 
            return false;
        });
	});

	function actt_gopremium(){

		//alert('Premium version required');
	
		$.confirm({
			boxWidth: '400px',
			useBootstrap: false,
			title: 'Your version doesn\'t support this functionality, please upgrade',
			content: 'We constantly maintain compatibility and improving this plugin for living, please support us and purchase, we provide very reasonable prices and will always do our best to help you!',
			buttons: {
				cancel: function () {
					return true;
				},
				somethingElse: {
					text: 'Purchase Now',
					btnClass: 'btn-blue',
					keys: ['enter', 'shift'],
					action: function(){

                        window.location = '<?php menu_page_url( 'activitytime-addons', true ); ?>';

						return false;
					}
                }
			}
		});
	
		return false;
	}

})( jQuery );

</script>

