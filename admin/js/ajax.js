// Action to add an account
function a_add_account()
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=add_account',
        data: $("#add_account").serialize(),
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
                        location.reload();
                }
                });
            }
        }
    });
    
    return false;
}

// Action to disable an account
function a_disable_account(id)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=disable_account&id=' + id,
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
                        location.reload();
                }
                });
            }
        }
    });
    
    return false;
}

// Action to enable an account
function a_enable_account(id)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=enable_account&id=' + id,
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
                        location.reload();
                }
                });
            }
        }
    });
    
    return false;
}

// Action to delete an account
function a_delete_account(id)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=delete_account&id=' + id,
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
                        location.reload();
                }
                });
            }
        }
    });
    
    return false;
}

// Set active deals
function a_update_max_active_deals(id , deals)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=update_max_active_deals&id=' + id + '&deals=' + deals,
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
                        location.reload();
                }
                });
            }
        }
    });
    
    return false;
}

// Set bo size
function a_update_bo_size(id , size)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=update_bo_size&id=' + id + '&size=' + size,
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
                        location.reload();
                }
                });
            }
        }
    });
    
    return false; 
}

// Load all bots
function a_load_bots(id)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=load_bots&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.manage_bots').append(response);
            }
        }
    });


    
    return false; 
}

// Load TV alerts settings
function a_load_tv_alerts(id)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=load_tv_alerts&id=' + id,
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

// Load telegram settings
function a_load_telegram_settings(id)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=load_telegram_settings&id=' + id,
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

// Load telegram settings
function a_load_logbook(id)
{
    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=load_logbook&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.logbook').append(response);
                $('#logbook_table').DataTable({
                    stateSave: true ,
                    "pageLength": 50 ,
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
        url: 'requesthandler.php?action=load_debuglog',
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


// Action to add an account
function a_change_bots()
{
    var change_bots_form = document.getElementById("change_bots");
    var fd = new FormData(change_bots_form);

    //alert(fd);

    $.ajax({
        type: 'post',
        url: 'requesthandler.php?action=change_bots',
        data: fd,
        cache: false,
        processData: false,
        contentType: false,
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
                        location.reload();
                }
                });
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
        url: 'requesthandler.php?action=change_telegram_settings',
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
        url: 'requesthandler.php?action=sent_telegram_msg&id=' + id,
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

// Link to Add account
$(document).on("click", '.add_account_link', function() { 
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.add_account').show();
});

// Link to Edit account
$(document).on("click", '.edit_account_link', function() { 
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.edit_account').show();
});

// Link to Delete account
$(document).on("click", '.delete_account_link', function() { 
    var id = $(this).attr("id");
    confirm_res = confirm("Are you sure you want to delete this account?");
    if(confirm_res) {
       return a_delete_account(id);
    } 
});

// Link to Disable account
$(document).on("click", '.disable_account_link', function() { 
    var id = $(this).attr("id");
    confirm_res = confirm("Are you sure you want to disable this account?");
    if(confirm_res) {
       return a_disable_account(id);
    } 
});

// Link to Enable account
$(document).on("click", '.enable_account_link', function() { 
    var id = $(this).attr("id");
    confirm_res = confirm("Are you sure you want to enable this account?");
    if(confirm_res) {
       return a_enable_account(id);
    } 
});

// Link to update max active deals
$(document).on("change", '.mad_dropdown', function() { 
    var id = $(this).attr("id");
    var deals = $(this).val();

    return a_update_max_active_deals(id , deals);
});

// Link to update bo size
$(document).on("change", '.bo_size', function() { 
    var id = $(this).attr("id");
    var size = $(this).val();

    confirm_res = confirm("Are you sure you want to update the BO size to " + size + "?");
    if(confirm_res) {
        alert('Update BO/SO to '+ size + '. This may take a while , don\'t close your browser!');
        return a_update_bo_size(id , size);
    } 


});

// Submit button manage bots
$(document).on("click", '.submit_mb', function() { 
    alert('Update bot settings. This may take a while , don\'t close your browser!');
});

// Link to Manage bots
$(document).on("click", '.manage_bots_link', function() { 
    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.manage_bots').empty();
    $('.manage_bots').show();

  
    return a_load_bots(id);
});

// Link to view trading_view alerts
$(document).on("click", '.tv_alerts_link', function() { 
    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.tv_alerts').empty();
    $('.tv_alerts').show();
    
    return a_load_tv_alerts(id);
});

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

// Link to Home
$(document).on("click", '.back_home_link', function() { 
    $('.home').hide();
    location.reload();
});


// JQuery for changes of differnt general bot settings , may be easier but jquery isn't my strongest asset ;)
$(document).on("change mousedown", '.so_type_all', function() { 
    $('.so_type_bots').val($(this).val());
});

$(document).on("change", '.max_so_all', function() { 
    $('.max_so_bots').val($(this).val());
});

$(document).on("change", '.act_so_all', function() { 
    $('.act_so_bots').val($(this).val());
});

$(document).on("change mousedown", '.size_type_all', function() { 
    $('.size_type_bots').val($(this).val());
});

$(document).on("change", '.bo_size_all', function() { 
    $('.bo_size_bots').val($(this).val());
});

$(document).on("change", '.so_size_all', function() { 
    $('.so_size_bots').val($(this).val());
});

$(document).on("change", '.so_perc_all', function() { 
    $('.so_perc_bots').val($(this).val());
});

$(document).on("change", '.so_volume_all', function() { 
    $('.so_volume_bots').val($(this).val());
});

$(document).on("change", '.so_step_all', function() { 
    $('.so_step_bots').val($(this).val());
});

$(document).on("change", '.tp_all', function() { 
    $('.tp_bots').val($(this).val());
});

$(document).on("change", '.ttp_all', function() { 
    $('.ttp_bots').val($(this).val());
});

$(document).on("change", '.ttp_deviation_all', function() { 
    $('.ttp_deviation_bots').val($(this).val());
});

$(document).on("change", '.cooldown_all', function() { 
    $('.cooldown_bots').val($(this).val());
});

$(document).on("change", '.lev_type_all', function() { 
    $('.lev_type_bots').val($(this).val());
});

$(document).on("change", '.lev_value_all', function() { 
    $('.lev_value_bots').val($(this).val());
});

$(document).on("change", '.is_enabled_all', function() { 
    $('.is_enabled_bots').val($(this).val());
});

// Form validatipon

// Restricts input for the set of matched elements to the given inputFilter function.
(function($) {
    $.fn.inputFilter = function(inputFilter) {
      return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
        if (inputFilter(this.value)) {
          this.oldValue = this.value;
          this.oldSelectionStart = this.selectionStart;
          this.oldSelectionEnd = this.selectionEnd;
        } else if (this.hasOwnProperty("oldValue")) {
          this.value = this.oldValue;
          this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
        } else {
          this.value = "";
        }
      });
    };
  }(jQuery));










