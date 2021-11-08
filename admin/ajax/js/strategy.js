// Set active deals strategy
function a_update_max_active_deals_strategy(id , deals)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_max_active_deals_strategy&id=' + id + '&deals=' + deals,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            }
        }
    });
    
    return false;
}

// Set active deals strategy
function a_update_strategy_settings(id , type)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_strategy_settings&id=' + id + '&type=' + type,
        global: false,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            }
        }
    });
    
    return false;
}

// Set active deals strategy
function a_update_strat_name(id , name)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_strat_name&id=' + id + '&name=' + name,
        global: false,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            }
        }
    });
    
    return false;
}

// Set active deals strategy
function a_update_tf_label(id , label)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_tf_label&id=' + id + '&label=' + label,
        global: false,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            }
        }
    });
    
    return false;
}

// Set active deals strategy
function a_update_tf_description(id , description)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_tf_description&id=' + id + '&description=' + description,
        global: false,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            }
        }
    });
    
    return false;
}

// Set active deals strategy
function a_update_tf_valid_time(id , min)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_tf_valid_time&id=' + id + '&min=' + min,
        global: false,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            }
        }
    });
    
    return false;
}

function a_update_tf_valid_direction(id , direction)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_tf_valid_direction&id=' + id + '&direction=' + direction,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            }
        }
    });
    
    return false;
}



// Load all bots strategies
function a_load_bots_strategy(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=load_bots_strategy&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.strategies').append(response);
            }
        }
    });
    
    return false; 
}

// Load debug log
function a_load_strategies(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=load_strategies&id=' +id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.strategies').append(response);
            }
        }
    });
    
    return false; 
}

// Action to change bots for an set strategy
function a_change_bots_strategy()
{
    var change_bots_form = document.getElementById("change_bots_strategy");
    var fd = new FormData(change_bots_form);

    //alert(fd);

    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_bots_strategy',
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
                        $('#manage_bots_strategy').remove();
                }
                });
            }
        }
    });
    
    return false;
}

// Action to add an strategy
function a_add_strategy(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=add_strategy&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.strategies').empty();
                return a_load_strategies('strat_' + id.split(/[_]+/)[1]); 
         
            }
        }
    });
    
    return false;
}

// Action to add an strategy
function a_add_timeframe(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=add_timeframe&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.strategies').empty();
                return a_load_strategies('strat_' + id.split(/[_]+/)[1]);               
            }
        }
    });
    
    return false;
}

// Action to delete an account
function a_force_time_frame_status(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=force_timeframe_status&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {   
                location.reload();            
                //$('.strategies').empty();
                //return a_load_strategies('strat_' + id.split(/[_]+/)[1]);    
            }
        }
    });
    
    return false;
}

// Action to add an strategy
function a_add_matrix_row(id)
{
    
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=add_matrix_row&id=' + id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.strategies').empty();
                return a_load_strategies('strat_' + id.split(/[_]+/)[1]);               
            }
        }
    });
    
    return false;
}


// Action to delete an strategy
function a_delete_strategy(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=delete_strategy&id=' + id,
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
              
                        $('.strategies').empty();
                        return a_load_strategies('strat_' + id.split(/[_]+/)[1]);                            
                    }
                });
            }
        }
    });
    
    return false;
}

// Action to delete an account
function a_delete_time_frame(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=delete_time_frame&id=' + id,
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
              
                        $('.strategies').empty();
                        return a_load_strategies('strat_' + id.split(/[_]+/)[1]);                            
                    }
                });
            }
        }
    });
    
    return false;
}

// Action to delete an account
function a_delete_matrix_row(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=delete_matrix_row&id=' + id,
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
              
                        $('.strategies').empty();
                        return a_load_strategies('strat_' + id.split(/[_]+/)[1]);                            
                    }
                });
            }
        }
    });
    
    return false;
}

// Action to delete an account
function a_update_strat_setting_mode(id , value)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/strategy.php?action=update_strat_setting_mode&id=' + id + '&value=' + value,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {              
                $('.strategies').empty();
                return a_load_strategies('strat_' + id.split(/[_]+/)[1]);                            

            }
        }
    });
    
    return false;
}

// Link to update max active deals per strategy
$(document).on("change", '.mad_dropdown_strategy', function() { 
    var id = $(this).attr("id");
    var deals = $(this).val();

    return a_update_max_active_deals_strategy(id , deals);
});

// Link to update time frame matrix
$(document).on("change", '.strategy_setting_type', function() { 
    var id = $(this).attr("id");

    if($(this).is(':checked')) {
        type = 'long' 
    } else {
        type = 'short';
    }
    return a_update_strategy_settings(id , type);
});

// Link to Manage bots
$(document).on("click", '.manage_bots_strategy_link', function() { 
    var id = $(this).attr("id");
    //$('.hide').hide();
    //$('.home').show();
    //$('.workspace').show();
    $('#manage_bots_strategy').remove();
    //$('.manage_bots').show();
  
    return a_load_bots_strategy(id);
});

// Link to Edit strategies
$(document).on("click", '.manage_strats_link', function() { 

    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.strategies').empty();
    $('.strategies').show();

    return a_load_strategies(id);
});

// Link to Delete account
$(document).on("click", '.add_strategy_link', function() { 
    var id = $(this).attr("id");

    return a_add_strategy(id);

});

// Link to Delete account
$(document).on("click", '.add_timeframe_link', function() { 
    var id = $(this).attr("id");

     return a_add_timeframe(id);

});

// Link to Delete account
$(document).on("click", '.force_time_frame_link', function() { 
    var id = $(this).attr("id");
    confirm_res = confirm("Are you sure you want to force this time frame?. If you are running alerts it may be overruled automatically , this is normally used on first setup.");
    if(confirm_res) {
       return a_force_time_frame_status(id);
    } 
});

// Link to Delete account
$(document).on("click", '.add_matrix_link', function() { 
    var id = $(this).attr("id");

     return a_add_matrix_row(id);

});

// Link to Delete account
$(document).on("click", '.delete_strategy_link', function() { 
    var id = $(this).attr("id");
    confirm_res = confirm("Are you sure you want to delete this strategy?");
    if(confirm_res) {
       return a_delete_strategy(id);
    } 
});

// Link to Delete account
$(document).on("click", '.delete_time_frame_link', function() { 
    var id = $(this).attr("id");
    confirm_res = confirm("Are you sure you want to delete this time frame?");
    if(confirm_res) {
       return a_delete_time_frame(id);
    } 
});

// Link to Delete account
$(document).on("click", '.delete_matrix_row_link', function() { 
    var id = $(this).attr("id");
    confirm_res = confirm("Are you sure you want to delete this row?");
    if(confirm_res) {
       return a_delete_matrix_row(id);
    } 
});

// Link to Delete account
$(document).on("change mouseout", '.strat_update', function() { 
    var id = $(this).attr("id");
    var name = $(this).val();
 
    return a_update_strat_name(id , name);

});

// Link to update time frame label
$(document).on("change mouseout", '.tf_update', function() { 
    
    var id = $(this).attr("id");
    var name = $(this).val();

    return a_update_tf_label(id , name);

});


// Link to update time frame description
$(document).on("change mouseout", '.tf_desc_update', function() { 
    var id = $(this).attr("id");
    var name = $(this).val();
 
    return a_update_tf_description(id , name);

});

// Link to Delete account
$(document).on("change mouseout", '.tf_time_update', function() { 
    
    var id = $(this).attr("id");
    var min = $(this).val();

    return a_update_tf_valid_time(id , min);

});

// Link to update max active deals per strategy
$(document).on("change", '.tf_direction_update', function() { 
    var id = $(this).attr("id");
    var direction = $(this).val();

    return a_update_tf_valid_direction(id , direction);
});

// Link to bot mode
$(document).on("change", '.strat_setting_mode', function() { 
    var id = $(this).attr("id");
    var value = $(this).val();

    return a_update_strat_setting_mode(id , value);

});