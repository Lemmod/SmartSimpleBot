
// Load telegram settings
function a_load_telegram_settings(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/telegram.php?action=load_telegram_settings&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                
                $('.telegram_settings').append(response);
            }
        }
    });
    
    return false; 
}

function a_change_telegram_settings()
{
    var change_telegram_settings_form = document.getElementById("change_telegram_settings");
    var fd = new FormData(change_telegram_settings_form);

    $.ajax({
        type: 'post',
        url: 'ajax/php/telegram.php?action=change_telegram_settings',
        data: fd,
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            alert(response);
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $( "#dialog" ).dialog({
                    title : 'Notice',
                    autoOpen : true,
                    open: function() {
                        $(this).html(response);
                    } ,
                    close: function() {
                        //location.reload();
                }
                }); 
            }
        }
    });
}


function a_sent_telegram_message(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/stelegram.php?action=sent_telegram_msg&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $( "#dialog" ).dialog({
                    title : 'Notice',
                    autoOpen : true,
                    open: function() {
                        $(this).html(response);
                    } ,
                    close: function() {
                        //location.reload();
                }
                }); 
            }
        }
    });
    
    return false; 
}

// Link to view Telegram setting
$(document).on("click", '.telegram_settings_link', function() { 
    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.telegram_settings').empty();
    $('.telegram_settings').show();
    
    return a_load_telegram_settings(id);
});

// Link to test_telegram msg
$(document).on("click", '.test_message_link', function() { 
    var id = $(this).attr("id");
    
    return a_sent_telegram_message(id);
});