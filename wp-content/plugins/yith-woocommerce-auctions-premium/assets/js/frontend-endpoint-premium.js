jQuery(document).ready(function($) {

    if ( object.time_check  > 0 ) {
        setInterval(live_auctions, object.time_check);
        function live_auctions() {
            $('.yith_wcact_my_auctions_table').block({message: null, overlayCSS: {background: "#fff", opacity: .6}});

            var post_data = {
                //security: object.search_post_nonce,
                action: 'yith_wcact_update_my_account_auctions'
            };

            $.ajax({
                type: "POST",
                data: post_data,
                url: object.ajaxurl,
                success: function (response) {
                    change_price_and_status(response);
                    $('.yith_wcact_my_auctions_table').unblock();
                },
                complete: function () {
                }
            });
        }
    }

    function change_price_and_status(response) {

        $('.yith-wcact-auction-endpoint').remove();
        for ( var i = 0; i<= Object.keys(response).length-1; i++) {
            console.log(response[i]);
            var row_td = '<td>'+response[i].image+'</td>' +
                         '<td><a href="'+response[i].product_url+'">'+response[i].product_name+'</a></td>' +
                         '<td>'+response[i].my_bid+'</td>' +
                         '<td>'+response[i].price+'</td>' +
                         '<td>'+response[i].status+'</td>';
            var row_tr = $('<tr class="yith-wcact-auction-endpoint"></tr>').append(row_td);
            $('.yith_wcact_my_auctions_table').append(row_tr);
        }
    }
});