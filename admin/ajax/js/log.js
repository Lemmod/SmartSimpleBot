// Load telegram settings
function a_load_logbook(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/log.php?action=load_logbook&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.logbook').append(response);
                $('#logbook_table').DataTable({
                    stateSave: true ,
                    "pageLength": 10 ,
                    "order" : [0 , "desc"] ,
                });
                $('#logbook_table_system').DataTable({
                    stateSave: true ,
                    "pageLength": 10 ,
                    "order" : [0 , "desc"] ,
                });
            }
        }
    });
    
    return false; 
}

// Load debug log
function a_load_debug_log()
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/log.php?action=load_debuglog',
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.logbook').append(response);
                $('#debug_table').DataTable({
                    stateSave: true ,
                    "pageLength": 50 ,
                    "order" : [0 , "desc"] ,
                });
            }
        }
    });
    
    return false; 
}

// Link to logbook msg
$(document).on("click", '.logbook_link', function() { 
    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.logbook').empty();
    $('.logbook').show();

    return a_load_logbook(id);
});

// Link to logbook msg
$(document).on("click", '.debug_log_link', function() { 
    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.logbook').empty();
    $('.logbook').show();

    return a_load_debug_log();
});