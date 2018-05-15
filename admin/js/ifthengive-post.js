(function ($) {
    'use strict';
        $( document ).ready( function () {
                //Require post title when adding/editing Project Summaries
                $( 'body' ).on( 'submit.edit-post', '#post', function () {
                        // If the title isn't set
                        if ( $( "#title" ).val().replace( / /g, '' ).length === 0 ) {
                                // Show the alert
                                //window.alert( 'Goal title is required.'); 
                                alertify.error('Goal title is required.');
                                
                                // Hide the spinner
				$( '#major-publishing-actions .spinner' ).hide();

				// The buttons get "disabled" added to them on submit. Remove that class.
				$( '#major-publishing-actions' ).find( ':button, :submit, a.submitdelete, #post-preview' ).removeClass( 'disabled' );
                                // Focus on the title field.
                                $( 'input[name="post_title"]' ).focus();
                                return false;
                        }
                        
                        if ( $('input[name="trigger_thing"]').val().replace( / /g, '' ).length === 0 ) {
                                // Show the alert                                
                                alertify.error('Goal label is required.');
                                
                                // Hide the spinner
				$( '#major-publishing-actions .spinner' ).hide();

				// The buttons get "disabled" added to them on submit. Remove that class.
				$( '#major-publishing-actions' ).find( ':button, :submit, a.submitdelete, #post-preview' ).removeClass( 'disabled' );
                                // Focus on the title field.
                                $( 'input[name="trigger_thing"]' ).focus();
                                return false;
                        }
                        
                });
        });
})(jQuery);