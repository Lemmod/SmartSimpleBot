<?php


function insert_log($pdo , $account_id , $bot_id , $pair , $message) {

    // Try starting insert the log file
    try{
               
        $stmt = $pdo->prepare("INSERT INTO log (account_id , bot_id , pair ,  message) VALUES (:account_id , :bot_id , :pair ,  :message)");
        $stmt->bindParam(':account_id', $account_id);
        $stmt->bindParam(':bot_id', $bot_id);
        $stmt->bindParam(':pair', $pair);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    }
    catch (PDOExecption $e){
        echo $e->getMessage();
    }   
}

function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {

    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);

    if(version_compare(phpversion(), '5.4.0', '>=')) { 
        return json_decode($json, $assoc, $depth, $options);
    } elseif(version_compare(phpversion(), '5.3.0', '>=')) { 
        return json_decode($json, $assoc, $depth);
    } else {
        return json_decode($json, $assoc);
    }
}

function json_cleaner($json, $assoc = false, $depth = 512, $options = 0) {

    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    
    return $json;

}

// Create a dropdown with an start and end for use with integers
function create_dropdown_number($start , $end , $name , $class , $id , $current_value , $step_size = '1') {

    $html = '<select name="'.$name.'"  class="'.$class.'">';

    

    for($x = $start; $x <= $end; $x += $step_size) {

        $value = floor($x * 100) / 100;
        $selected = ( $value == $current_value) ? 'selected' : '';
        $html.= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
    }

    $html.= '</select>';

    return $html;
}

// Create a dropdown with an start and end for use with integers
function create_dropdown_number_with_id($start , $end , $name , $class , $id , $current_value , $step_size = '1') {

    $html = '<select name="'.$name.'" id="'.$id.'" class="'.$class.'">';

    for($x = $start; $x <= $end; $x += $step_size) {

        $value = floor($x * 100) / 100;
        $selected = ( $value == $current_value) ? 'selected' : '';
        $html.= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
    }

    $html.= '</select>';

    return $html;
}

function create_input_number($name , $class , $id , $current_value) {

    $html = '<input name="'.$name.'"  size="4" class="'.$class.' input_number" value="'.$current_value.'" />';


    return $html;
}

function create_input_float($name , $class , $id , $current_value) {

    $html = '<input name="'.$name.'"  size="4"  class="'.$class.' input_float" value="'.$current_value.'" />';


    return $html;
}

// Create a dropdown with an array for options
function create_dropdown_options($options , $name , $class , $id , $current_value , $use_different_key = false) {

    $html = '<select name="'.$name.'" id="'.$id.'"  class="'.$class.'">';

    foreach($options as $key => $opt) {
        if (!$use_different_key) {
            $selected = ($opt == $current_value) ? 'selected' : '';
            $html.= '<option '.$class.' value="'.$opt.'" '.$selected.'>'.$opt.'</option>';
        } else {
            $selected = ($key == $current_value) ? 'selected' : '';
            $html.= '<option '.$class.' value="'.$key.'" '.$selected.'>'.$opt.'</option>';
        }

    }

    $html.= '</select>';

    return $html;
}

function check_credentials($user_id) {


    // Terminate if the user is nog logged in
    if ($user_id != $_SESSION['user_id']) {
        echo 'ERROR_NOT_LOGGED_IN';
        die;
    }
}

function telegram($telegram_bot_id , $telegram_chat_id , $msg) {
    global $telegrambot,$telegramchatid;

    $url='https://api.telegram.org/bot'.$telegram_bot_id.'/sendMessage';$data=array('chat_id'=>$telegram_chat_id,'text'=>$msg);

    $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);

    $context=stream_context_create($options);

    $result=file_get_contents($url,false,$context);
    
    return $result;
}


// Building the query string to look for the corresponding strategy with current time_frames
function strat_query_builder($tf_data) {

    $query_string_join = '';
    $query_string_filter = '';
    $x = 0;
    $total = count($tf_data);
    foreach ($tf_data as $key => $data) {
        $query_string_join.= '(SELECT type FROM strategy_settings s'.$x.' WHERE s'.$x.'.combination_id = p.combination_id AND time_frame_id = '.$key.') AS tf_'.$key;
        if ($x < ( $total -1 )) {
            $query_string_join.= ',';
        }
        $query_string_filter.= ' AND tf_'.$key.' = "'.$data.'"';
        $x++;
    }

    return ['join' => $query_string_join , 'filter' => $query_string_filter];
}

function pr($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}