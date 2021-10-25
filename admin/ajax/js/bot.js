
// Set bo size
function a_update_bo_size(id , size)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/bot.php?action=update_bo_size&id=' + id + '&size=' + size,
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
        url: 'ajax/php/bot.php?action=load_bots&id=' + id,
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



// Action to change bots on 3commas
function a_change_bots()
{
    var change_bots_form = document.getElementById("change_bots");
    var fd = new FormData(change_bots_form);

    //alert(fd);

    $.ajax({
        type: 'post',
        url: 'ajax/php/bot.php?action=change_bots',
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