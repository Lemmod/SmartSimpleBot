// Action to add an account
function a_add_account()
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/account.php?action=add_account',
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
        url: 'ajax/php/account.php?action=disable_account&id=' + id,
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
        url: 'ajax/php/account.php?action=enable_account&id=' + id,
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
        url: 'ajax/php/account.php?action=delete_account&id=' + id,
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
        url: 'ajax/php/account.php?action=update_max_active_deals&id=' + id + '&deals=' + deals,
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

// Update strategy
function a_update_strategy(id , strategy)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/account.php?action=update_strategy&id=' + id + '&strategy=' + strategy,
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

function a_update_use_ss(id , value)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/account.php?action=update_use_ss&id=' + id + '&value=' + value,
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

// Link to bot strategy
$(document).on("change", '.strategy', function() { 
    var id = $(this).attr("id");
    var strategy = $(this).val();

    confirm_res = confirm("Are you sure you want to update the strategy?");
    if(confirm_res) {
        return a_update_strategy(id , strategy);
    } 
});

// Link to bot strategy
$(document).on("change", '.use_ss', function() { 
    var id = $(this).attr("id");
    var value = $(this).val();

    confirm_res = confirm("Are you sure you want change the status?");
    if(confirm_res) {
        return a_update_use_ss(id , value);
    } 
});