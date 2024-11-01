(function($){

	var SimpleLeadGenerator = {

		/**
		 * Init
		 *
		 * @since 1.0.0
		 */
		init: function()
		{
			this._bind();
		},

		/**
		 * Binds events
		 *
		 * @since 1.0.0
		 */
		_bind: function()
		{
			$( document ).on('submit', '.slg-form', SimpleLeadGenerator.submit_form );
		},

		/**
		 * Submit form
		 *
		 * @since 1.0.0
		 * @param  object event Event object.
		 * @return void
		 */
		submit_form: function( event ) {
			event.preventDefault();

			var form = $( this );

			/** @todo Add post title support. */
			var post_title = form.find( '.slg-field-input[name="name"]' ).val() || '';

			var fields = [];

			// Get all fields with field types.
			form.find( '.slg-field-input' ).each( function( index, el ) {
				var field_type = $( el ).attr( 'field-type' ) || '';
				var name = $( el ).attr( 'name' ) || '';
				var value = $( el ).val() || '';
				fields.push({
					'type' : field_type,
					'name' : name,
					'value' : value,
				});
			} );

			form.find('.slg-field-submit').val( 'Submitting..' );

			$.ajax({
				url  : SimpleLeadGeneratorVars.ajaxurl,
				type : 'POST',
				data : {
					action : 'simple_lead_generator_submit',
					post_title : post_title,
					fields : fields,
					_ajax_nonce : SimpleLeadGeneratorVars._ajax_nonce,
				},
				// @todo show the message while handling the form.
				/* beforeSend: function() {
					console.log( 'Submitting..' );
				}, */
			})
			.fail(function( jqXHR ){
				// @todo manage fail case.
				// console.log( 'error', jqXHR );
		    })
			.done(function ( response ) {

				if( response.success ) {
					form.html( SimpleLeadGeneratorVars['success-message'] )

					// @todo handle the animation and show nice message.
					/* form.slideUp()
						.html( SimpleLeadGeneratorVars['success-message'] )
						.slideDown(); */
				} else {
					// @todo handle fail case.
				}
			});
		}

		/**
		 * Debugging
		 *
		 * @todo Use this for logging the process.
		 *
		 *
		_log: function( data, level ) {
			var date = new Date();
			var time = date.toLocaleTimeString();

			var color = '#444';

			switch( level ) {
				case 'emergency': 	// color = '#f44336';
				case 'critical': 	// color = '#f44336';
				case 'alert': 		// color = '#f44336';
		 		case 'error': 		// color = '#f44336';
		 			if (typeof data == 'object') {
		 				console.error( data );
		 			} else {
		 				console.error( data + ' ' + time );
		 			}
		 		break;
		 		case 'warning': 	// color = '#ffc107';
		 		case 'notice': 		// color = '#ffc107';
		 			if (typeof data == 'object') {
		 				console.warn( data );
		 			} else {
						console.warn( data + ' ' + time );
					}
				break;
				default:
					if (typeof data == 'object') {
						console.log( data );
					} else {
						console.log( data + ' ' + time );
					}
				break;
			}
		} */

	};

	/**
	 * Initialize SimpleLeadGenerator
	 */
	$(function(){
		SimpleLeadGenerator.init();
	});

})(jQuery);
