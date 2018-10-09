;
(function($){
    var ttsAddedToCartPopup = window.ttsAddedToCartPopup = {
        $el: false,
        tempalate: function(){
            return $('#tts-tpl-added-to-cart-lightbox').html();
        },
        create: function(){
            if(this.$el){
                return;
            }
            this.$el = $(this.tempalate());
            if( this.$el.length === 0 ){
                return;
            }
            $('body').append(this.$el);
            $('.tts-atc-ligtbox-close', this.$el).on('click', function(e){
                if($(this).is('.tts-atc-ligtbox-close')){
                    e.preventDefault();
                }
                ttsAddedToCartPopup.$el && ttsAddedToCartPopup.close();
            });
        },
        open: function(){
            this.$el && this.$el.addClass('tts-atc-lightbox-shown');
        },
        close: function(){
            this.$el.removeClass('tts-atc-lightbox-shown');
        }
    };
    $.fn.dataTable.ext.search.push(
        function( settings, searchData, index ) {
            var $filterCourse = $(settings.nTable).closest('.course-date-table-wrap').find('.course-table-filter-course'), 
                $filterMonth = $(settings.nTable).closest('.course-date-table-wrap').find('.course-table-filter-months'),
                $filterLocation = $(settings.nTable).closest('.course-date-table-wrap').find('.course-table-filter-locgroup'),
                filterCourseID, rowCourseID, filterMonthNum, rowMonthNum, filterLocationSlug, rowLocationSlug, 
                monthPassed = true, 
                coursePassed = true, 
                locationPassed = true;
            if(!$filterCourse.length && !$filterMonth.length){
                return true;
            }
            if($filterCourse.length ){
                filterCourseID = $filterCourse.find('option:selected').val();
                if(filterCourseID){
                    filterCourseID = parseInt(filterCourseID);
                    rowCourseID = parseInt(searchData[1]);
                    if( filterCourseID !== rowCourseID){
                        coursePassed = false;
                    }
                }
            }
            if($filterMonth.length ){
                filterMonthNum = $filterMonth.find('option:selected').val();
                rowMonthNum = parseInt(searchData[0]);
                if(typeof filterMonthNum === "undefiend"){
                    filterMonthNum = 12;
                }
                filterMonthNum = parseInt(filterMonthNum);
                if(!rowMonthNum){
                    rowMonthNum = 0;
                }
                if(filterMonthNum < rowMonthNum){
                    monthPassed = false;
                }
            }
            if($filterLocation.length ){
                filterLocationSlug = $filterLocation.find('option:selected').val();
                rowLocationSlug = searchData[2];
                if(typeof filterLocationSlug === "undefiend"){
                    filterLocationSlug = '';
                }
                rowLocationSlug = rowLocationSlug.split(',');
                if(filterLocationSlug && (rowLocationSlug.indexOf(filterLocationSlug) === -1) ){
                    locationPassed = false;
                }
            }
            return monthPassed && coursePassed && locationPassed;
        }
    );
    function isMobile(dw){
        var rw = 1024, ww = $(window).width();
        if(dw){
            rw = dw;
        }
        if(rw>=ww) return true;
        return false;
    }
    $(document).ready(function(){
        var tippedOp = {
            behavior: 'sticky',
            close: 'overlap',
            hideOn: false,
            hideOnClickOutside: true,
            hideOthers: true,
            maxWidth: 960,
            showDelay: 1500
        };
        $('.course-stiker-img').each(function(){
            Tipped.create($(this), $(this).next('p').html(),tippedOp);
        });
        $('.course-tooltip-image').each(function(){
            $(this).on('click', function(){
                return false;
            });
            var text = $(this).attr('href');
            if(!text){
                return;
            }
            Tipped.create($(this), '<img src="' + text + '"/>',tippedOp);
        });
        $('.course-date-table').each(function(){
            var $t = $(this),
                tableDom = $t.data('domtype'),
                targets = [0];
            if(!tableDom){
                tableDom = 'tip';
            }
            if($t.closest('.course-date-table-wrap').find('.course-table-filter-course').length){
                targets.push(1);
            }
            if($t.closest('.course-date-table-wrap').find('.course-table-filter-locgroup').length){
                targets.push(2);
            }
            var table = $t.DataTable({
                "dom": tableDom,
                "columnDefs": [
                    {
                        "targets": targets,
                        "visible": false,
                        "searchable": true
                    }
                ],
                "pageLength": 12,
                "infoCallback": function( settings, start, end, max, total, pre ) {
                    return 'Showing ' + start + ' to ' + end + ' of ' + total + ' dates.' ;
                },
                "language": {
                    "zeroRecords": "We definitely have this course for you, but it’s not listed right now.  Please ring 0117 971 1892 option 1 for more information."
                }
            });
            $t.data('tbsdatatble', table);
        });
        $('.course-table-filter-months').change(function(){
            $(this).closest('.course-date-table-wrap').find('.course-date-table').data('tbsdatatble').draw();
        });
        $('.course-table-filter-course').change(function(){
            $(this).closest('.course-date-table-wrap').find('.course-date-table').data('tbsdatatble').draw();
        });
        $('.course-table-filter-locgroup').change(function(){
            $(this).closest('.course-date-table-wrap').find('.course-date-table').data('tbsdatatble').draw();
        });
        $('.cd-accr-addr-enable').each(function(){
            var $t = $(this), $g = $t.closest('.accredited-checkout-fields').find('.accredited-address-fields-group');
            if(this.checked){
                $g.slideDown();
            }else{
                $g.slideUp();
            }
        }).on('change', function(){
            var $t = $(this), $g = $t.closest('.accredited-checkout-fields').find('.accredited-address-fields-group');
            if(this.checked){
                $g.slideDown();
            }else{
                $g.slideUp();
            }
        });
        $('.booker_is_delegate').on('change', function(){
            if(!this.checked){
                $('.df-first-name', $fields).prop('disabled', false);
                $('.df-last-name', $fields).prop('disabled', false);
                $('.df-email', $fields).prop('disabled', false);
                return;
            }
            var $t = $(this), $fields = $t.closest('.checkout-delegates-fields-wrap').find('.delegate-fields').eq(0);
            
            $('.df-first-name', $fields).val($('#billing_first_name').val()).prop('disabled', true);
            $('.df-last-name', $fields).val($('#billing_last_name').val()).prop('disabled', true);
            $('.df-email', $fields).val($('#billing_email').val()).prop('disabled', true);
           
        });
        $('#billing_first_name').on('change', function(){
            var $bf = $(this);
            $('.booker_is_delegate:checked').each(function(){
                var $t = $(this), $fields = $t.closest('.checkout-delegates-fields-wrap').find('.delegate-fields').eq(0);
                $('.df-first-name', $fields).val($bf.val());
            });
        });
        $('#billing_last_name').on('change', function(){
            var $bf = $(this);
            $('.booker_is_delegate:checked').each(function(){
                var $t = $(this), $fields = $t.closest('.checkout-delegates-fields-wrap').find('.delegate-fields').eq(0);
                $('.df-last-name', $fields).val($bf.val());
            });
        });
        $('#billing_email').on('change', function(){
            var $bf = $(this);
            $('.booker_is_delegate:checked').each(function(){
                var $t = $(this), $fields = $t.closest('.checkout-delegates-fields-wrap').find('.delegate-fields').eq(0);
                $('.df-email', $fields).val($bf.val());
            });
        });
        
        // Ajax Add to Cart
        ttsAddedToCartPopup.create();
        $('body').on('click', '.tbs-add-to-cart', function(e){
            var $thisbutton = $( this );
            if ( ! $thisbutton.attr( 'data-product_id' ) ) {
				return true;
			}

			e.preventDefault();
            $thisbutton.removeClass( 'tbs-added-to-cart' );
            $thisbutton.addClass( 'tbs-adding-to-cart' );
            
            var data = {};

			$.each( $thisbutton.data(), function( key, value ) {
				data[ key ] = value;
			});
            // Ajax action.
			wc_add_to_cart_params && $.post( wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' ), data, function( response ) {
				if ( ! response ) {
					return;
				}

				if ( response.error && response.product_url ) {
					window.location = wc_add_to_cart_params.cart_url;
					return;
				}
                if ( response.fragments ) {
                    $.each( response.fragments, function( key ) {
                        $( key )
                            .addClass( 'updating' )
                            .fadeTo( '400', '0.6' )
                            .block({
                                message: null,
                                overlayCSS: {
                                    opacity: 0.6
                                }
                            });
                    });
                    $thisbutton.removeClass( 'tbs-adding-to-cart' );
                    $thisbutton.addClass( 'tbs-added-to-cart' );
                    $.each( response.fragments, function( key, value ) {
                        $( key ).replaceWith( value );
                        $( key ).stop( true ).css( 'opacity', '1' ).unblock();
                    });

                    $( document.body ).trigger( 'wc_fragments_loaded' );
                    ttsAddedToCartPopup.open();
                }
			});
        });
        $( '#course_rating' ).hide().before( '<p class="tbs-stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a></span></p>' );
        $('body').on( 'click', '#respond p.tbs-stars a', function() {
			var $star   	= $( this ),
				$rating 	= $( this ).closest( '#respond' ).find( '#course_rating' ),
				$container 	= $( this ).closest( '.tbs-stars' );

			$rating.val( $star.text() );
			$star.siblings( 'a' ).removeClass( 'active' );
			$star.addClass( 'active' );
			$container.addClass( 'selected' );

			return false;
		} )
		.on( 'click', '#respond #submit', function() {
			var $rating = $( this ).closest( '#respond' ).find( '#course_rating' ),
				rating  = $rating.val();

			if ( $rating.length > 0 && ! rating && wc_single_product_params.review_rating_required === 'yes' ) {
				window.alert( wc_single_product_params.i18n_required_rating_text );

				return false;
			}
		} );
    });
})(jQuery);