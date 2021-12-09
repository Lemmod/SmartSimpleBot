
// Load debug log
function a_load_deals(id)
{
    $.ajax({
        type: 'post',
        url: 'ajax/php/deals.php?action=load_deals&id=' +id,
        success: function (response) {
            if (response == 'ERROR_NOT_LOGGED_IN') {
                location.href = 'logout.php?response=incorrect_ajax_call';
            } else {
                $('.deals').append(response);
                $('#deals_table').DataTable({

                    "pageLength": 50 ,
                    "order" : [6 , "desc"] ,
                    stateSave: true ,
                });
            }
        }
    });
    
    return false; 
}



// Link to load deals
$(document).on("click", '.load_deals_link', function() { 

    var id = $(this).attr("id");
    $('.hide').hide();
    $('.home').show();
    $('.workspace').show();
    $('.deals').empty();
    $('.deals').show();

    return a_load_deals(id);
});
