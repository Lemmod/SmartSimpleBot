<?php

// Telegram function which you can call
function telegram($telegram_bot_id , $telegram_chat_id , $msg) {

        $url='https://api.telegram.org/bot'.$telegram_bot_id.'/sendMessage';$data=array('chat_id'=>$telegram_chat_id,'text'=>$msg);

        $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);

        $context=stream_context_create($options);

        $result=file_get_contents($url,false,$context);
        
        return $result;
}
?>
