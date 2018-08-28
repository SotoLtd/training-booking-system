
jQuery( function ( $ ) {
	var tbsOnlineBooking = {
		states: null,
		init: function() {
			if ( ! ( typeof tbs_onine_booking_params === 'undefined' || typeof tbs_onine_booking_params.countries === 'undefined' ) ) {
				/* State/Country select boxes */
				this.states = $.parseJSON( tbs_onine_booking_params.countries.replace( /&quot;/g, '"' ) );
			}

			$( '.js_field-country' ).selectWoo().change( this.change_country );
			$( '.js_field-country' ).trigger( 'change', [ true ] );
			$( document.body ).on( 'change', 'select.js_field-state', this.change_state );
            
            $('#tbs-edit-customer-details').on('click', function(e){
                e.preventDefault();
                if($(this).hasClass('tbs-active')){
                    $('#tbs-booking-details-edit').addClass('tbs-inactive');
                    $('#tbs-booking-details-view').removeClass('tbs-inactive');
                    $(this).removeClass('tbs-active');
                }else{
                    $('#tbs-booking-details-view').addClass('tbs-inactive');
                    $('#tbs-booking-details-edit').removeClass('tbs-inactive');
                    $(this).addClass('tbs-active');
                }
            });
            
            $('#tbs-edit-delegate-details').on('click', function(e){
                e.preventDefault();
                if($(this).hasClass('tbs-active')){
                    $('#tbs-delegate-details-edit').addClass('tbs-inactive');
                    $('#tbs-delegate-details-view').removeClass('tbs-inactive');
                    $(this).removeClass('tbs-active');
                }else{
                    $('#tbs-delegate-details-view').addClass('tbs-inactive');
                    $('#tbs-delegate-details-edit').removeClass('tbs-inactive');
                    $(this).addClass('tbs-active');
                }
            });
            $('#tbs-booking-details-edit form').on('submit', function(e){
                e.preventDefault();
                var $this = $(this);
                $('#tbs-billing-address-loader').addClass('tbs-active');
                $.ajax({
                    url: tbs_onine_booking_params.ajaxUrl,
                    method: "post",
                    data: $this.serialize(),
                    dataType: "json",
                    success: function(response){
                        $('#tbs-billing-address-loader').removeClass('tbs-active');
                        if(!response || !response.status || "OK" !== response.status){
                            alert('Failed!');
                            return;
                        }
                        alert('Customer billing details saved successfully!');
                        $('#tbs-booking-details-edit').addClass('tbs-inactive');
                        $('#tbs-booking-details-view').html(response.address_html).removeClass('tbs-inactive');
                        $('#tbs-edit-customer-details').removeClass('tbs-active');
                    }

                }).fail(function(){
                    $('#tbs-billing-address-loader').removeClass('tbs-active');
                    alert('Failed!');
                });
            });
            $('#tbs-delegate-details-edit form').on('submit', function(e){
                e.preventDefault();
                var $this = $(this);
                $('#tbs-delegate-details-loader').addClass('tbs-active');
                $.ajax({
                    url: tbs_onine_booking_params.ajaxUrl,
                    method: "post",
                    data: $this.serialize(),
                    dataType: "json",
                    success: function(response){
                        $('#tbs-delegate-details-loader').removeClass('tbs-active');
                        if(!response || !response.status || "OK" !== response.status){
                            alert('Failed!');
                            return;
                        }
                        alert('Delegates details saved successfully!');
                        $('#tbs-delegate-details-edit').addClass('tbs-inactive');
                        $('#tbs-delegate-details-view').html(response.delegates_html).removeClass('tbs-inactive');
                        $('#tbs-edit-delegate-details').removeClass('tbs-active');
                    }

                }).fail(function(){
                    $('#tbs-delegate-details-loader').removeClass('tbs-active');
                    alert('Failed!');
                });
            });
		},

		change_country: function( e, stickValue ) {
			// Check for stickValue before using it
			if ( typeof stickValue === 'undefined' ){
				stickValue = false;
			}

			var $this = $( this ),
				country = $this.val(),
				$state = $this.parents( 'div.booking-edit_address' ).find( ':input.js_field-state' ),
				$parent = $state.parent(),
				input_name = $state.attr( 'name' ),
				input_id = $state.attr( 'id' ),
				value = $this.data( 'woocommerce.stickState-' + country ) ? $this.data( 'woocommerce.stickState-' + country ) : $state.val(),
				placeholder = $state.attr( 'placeholder' );

			if ( stickValue ){
				$this.data( 'woocommerce.stickState-' + country, value );
			}

			// Remove the previous DOM element
			$parent.show().find( '.select2-container' ).remove();

			if ( ! $.isEmptyObject( tbsOnlineBooking.states[ country ] ) ) {
				var $states_select = $( '<select name="' + input_name + '" id="' + input_id + '" class="js_field-state select short" placeholder="' + placeholder + '"></select>' ),
					state = tbsOnlineBooking.states[ country ];

				$states_select.append( $( '<option value="">' + tbs_onine_booking_params.i18n_select_state_text + '</option>' ) );

				$.each( state, function( index ) {
					$states_select.append( $( '<option value="' + index + '">' + state[ index ] + '</option>' ) );
				} );

				$states_select.val( value );

				$state.replaceWith( $states_select );

				$states_select.show().selectWoo().hide().change();
			} else {
				$state.replaceWith( '<input type="text" class="js_field-state" name="' + input_name + '" id="' + input_id + '" value="' + value + '" placeholder="' + placeholder + '" />' );
			}
		},

		change_state: function() {
			// Here we will find if state value on a select has changed and stick it to the country data
			var $this = $( this ),
				state = $this.val(),
				$country = $this.parents( 'div.booking-edit_address' ).find( ':input.js_field-country' ),
				country = $country.val();

			$country.data( 'woocommerce.stickState-' + country, state );
		},

		edit_address: function( e ) {
			e.preventDefault();

			var $this          = $( this ),
				$wrapper       = $this.closest( '.order_data_column' ),
				$edit_address  = $wrapper.find( 'div.edit_address' ),
				$address       = $wrapper.find( 'div.address' ),
				$country_input = $edit_address.find( '.js_field-country' ),
				$state_input   = $edit_address.find( '.js_field-state' );

			$address.hide();
			$this.parent().find( 'a' ).toggle();

			if ( ! $country_input.val() ) {
				$country_input.val( woocommerce_admin_meta_boxes_order.default_country ).change();
			}

			if ( ! $state_input.val() ) {
				$state_input.val( woocommerce_admin_meta_boxes_order.default_state ).change();
			}

			$edit_address.show();
		}
	};

	tbsOnlineBooking.init();
});
