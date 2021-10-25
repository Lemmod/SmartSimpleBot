// Load TV alerts settings
function a_load_tv_alerts(id , strat)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/tradingview.php?action=load_tv_alerts&id=' + id + '&strat=' + strat,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.tv_alerts').append(response);
                $('#bot_spec_alerts').DataTable({

                    "pageLength": 50 ,
                    "order" : [1 , "desc"] ,
                    stateSave: true ,
                });
            }
        }
    });
    
    return false; 
}

// Set active deals
function a_load_alert_strategy(id , strat)
{
    
    $('.tv_alerts').empty();
    return a_load_tv_alerts(id , strat); 

    
    return false;
}
// Link to view trading_view alerts
$(document).on("click", '.tv_alerts_link', function() { 
    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.tv_alerts').empty();
    $('.tv_alerts').show();
    
    return a_load_tv_alerts(id , 0);
});

// Link to update max active deals
$(document).on("change", '.alert_strategy', function() { 
    var id = $(this).attr("id");

    var strat = $(this).val();

    return a_load_alert_strategy(id , strat);
});
