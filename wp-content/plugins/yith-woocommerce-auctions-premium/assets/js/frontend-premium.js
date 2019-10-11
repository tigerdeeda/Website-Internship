
jQuery(document).ready(function($) {
  var timer;



  var result = parseInt($('#timer_auction').data('remaining-time'));
    //Datetimeformat in product auction
    var utcSeconds =  parseInt($('#timer_auction').data('finish'));
    var d = new Date(0); // The 0 there is the key, which sets the date to the epoch
    d.setUTCSeconds(utcSeconds);

    $("#dateend").text(d.toLocaleString());



    //Timeleft
    timer = setInterval(function() {
    timeBetweenDates(result);
    result--
  }, 1000);

  function timeBetweenDates(result) {
    if (result <= 0) {

      // Timer done

      clearInterval(timer);
      window.location.reload(true);
    
    } else {
      
      var seconds = Math.floor(result);
      var minutes = Math.floor(seconds / 60);
      var hours = Math.floor(minutes / 60);
      var days = Math.floor(hours / 24);

      hours %= 24;
      minutes %= 60;
      seconds %= 60;

      $("#days").text(days);
      $("#hours").text(hours);
      $("#minutes").text(minutes);
      $("#seconds").text(seconds);
    }
  }

  //Button up or down bid
    var current = $('#time').data('current');
    $(".bid").click(function(e){
        e.preventDefault();
        var actual_bid = $('#_actual_bid').val();
        if($(this).hasClass("button_bid_add")){
            if(!actual_bid){
                actual_bid = current;
            }
            actual_bid++;
            $('#_actual_bid').val(actual_bid);
        } else {
            if(actual_bid){
                actual_bid--;
                if (actual_bid >= current){
                    $('#_actual_bid').val(actual_bid);
                }else{
                    $('#_actual_bid').val(current);
                }
            }
        }
    });

//Button bid
//
    $( document ).on( 'click', '.auction_bid', function( e ) {
    //var target = $( e.target ); // this code get the target of the click -->  $('.bid')
        $('#yith-wcact-form-bid').block({message:null, overlayCSS:{background:"#fff",opacity:.6}});

        var post_data = {
              'bid': $('#_actual_bid').val(),
              'product' : $('#time').data('product'),
              //security: object.search_post_nonce,
              action: 'yith_wcact_add_bid'
          };
    
        $.ajax({
               type    : "POST",
               data    : post_data,
               url     : object.ajaxurl,
               success : function ( response ) {
                    //console.log(response.url);
                   $('#yith-wcact-form-bid').unblock();
                   window.location = response.url;

                     //window.location.reload(true);
                   // On Success
               },
               complete: function () {
               }
            });
  } );

    //Disable enter in input
    $("#_actual_bid").keydown(function( event ) {
        if ( event.which == 13 ) {
            event.preventDefault();
        }
    });
    //Change the datetime format to locale
    $( '.yith_auction_datetime' ).each( function ( index ) {
        var current_date     = change_datetime_format($(this));
        $( this ).text( current_date );
    } );

    //Live auctions on product page
    if ( object.live_auction_product_page  > 0 ) {

        setInterval(live_auctions,object.live_auction_product_page);
        function live_auctions(){
            live_auctions_template();
        }

        function live_auctions_template() {
            $('#tab-Mytab').block({message:null, overlayCSS:{background:"#fff",opacity:.6}});

            var post_data = {
                //security: object.search_post_nonce,
                product: $(':hidden#yith-wcact-product-id').val(),
                action: 'yith_wcact_update_list_bids'
            };

            $.ajax({
                type    : "POST",
                data    : post_data,
                url     : object.ajaxurl,
                success : function ( response ) {

                    $('#tab-Mytab').empty();
                    $('#tab-Mytab').html( response['list_bids'] );
                    //Change the datetime format to locale
                    $( '.yith_auction_datetime' ).each( function ( index ) {
                        var current_date     = change_datetime_format($(this));
                        $( this ).text( current_date );
                    } );
                    $('#tab-Mytab').unblock();
                    $('p.price span:first-child').html( response['current_bid'] );
                    $('#yith-wcact-max-bidder').empty();
                    $('#yith-wcact-max-bidder').html( response['max_bid'] );
                    $('#yith_wcact_reserve_and_overtime').empty();
                    $('#yith_wcact_reserve_and_overtime').html( response['reserve_price_and_overtime'] );
                    
                    if( 'timeleft' in response ) {

                        $('#yith-wcact-auction-timeleft').html(response['timeleft']);
                        var utcSeconds =  parseInt($('#timer_auction').data('finish'));
                        var d = new Date(0); // The 0 there is the key, which sets the date to the epoch
                        d.setUTCSeconds(utcSeconds);
                        $("#dateend").text(d.toLocaleString());

                        //Clear interval and create a new interval with the new date
                        clearInterval(timer);
                        var result = parseInt($('#timer_auction').data('remaining-time'));
                        timer = setInterval(function() {
                            timeBetweenDates(result);
                            result--
                        }, 1000);
                    }
                },
                complete: function () {
                }
            });
        }
    }


    //Change the datetime format to locale
    function change_datetime_format($time) {

        var datetime     = $time.text();
        datetime = datetime+' UTC';
        datetime = datetime.replace(/-/g,'/');
        var current_date = new Date( datetime );
        return current_date.toLocaleString();
    }
    

});
