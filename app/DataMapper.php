<?php

class DataMapper extends Core
{
    
    /**
     * insert_raw_tv_input
     *
     * @param  string $tv_input
     * @return void
     */
    public function insert_raw_tv_input($tv_input , $file_name) {

        try{
               
            $stmt = $this->dbh->prepare('INSERT INTO raw_tv_input (input , file_name) VALUES (:input , :file_name)');
            $stmt->bindParam(':input', $tv_input);
            $stmt->bindParam(':file_name', $file_name);
            $stmt->execute();

            $stmt = null;
            
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }
    
    /**
     * insert_log
     *
     * @param  int $account_id
     * @param  int $bot_id
     * @param  string $pair
     * @param  string $message
     * @return void
     */
    public function insert_log($account_id , $bot_id , $pair , $message) {

        try{
               
            $stmt = $this->dbh->prepare("INSERT INTO log (account_id , bot_id , pair ,  message) VALUES (:account_id , :bot_id , :pair ,  :message)");
            $stmt->bindParam(':account_id', $account_id);
            $stmt->bindParam(':bot_id', $bot_id);
            $stmt->bindParam(':pair', $pair);
            $stmt->bindParam(':message', $message);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    /**
     * Insert the debug log to test speed of the alert_processor
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function insert_debug_log($file_name , $alerts_processed , $errors_3c , $calls_3c , $time_passed) {

        
        try{
               
            $stmt = $this->dbh->prepare("INSERT INTO debug_log (file_name , alerts_processed , errors_3c , calls_3c , time_passed) VALUES (:file_name ,  :alerts_processed , :errors_3c , :calls_3c , :time_passed) ");
            $stmt->bindParam(':file_name', $file_name);
            $stmt->bindParam(':alerts_processed', $alerts_processed);
            $stmt->bindParam(':errors_3c', $errors_3c);
            $stmt->bindParam(':calls_3c', $calls_3c);
            $stmt->bindParam(':time_passed', $time_passed);
            $stmt->execute();

            $stmt = null;

            
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

        
    /**
     * insert_account
     *
     * @param  int $user_id
     * @param  int $bot_account_id
     * @param  string $account_name
     * @param  string $api_key
     * @param  string $api_secret
     * @return void
     */
    public function insert_account($user_id , $bot_account_id , $account_name , $api_key , $api_secret) {
        
        try{

            $stmt = $this->dbh->prepare("INSERT INTO accounts (user_id , bot_account_id ,  account_name , api_key ,  api_secret) VALUES (:user_id , :bot_account_id , :account_name , :api_key , :api_secret)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':bot_account_id', $bot_account_id);
            $stmt->bindParam(':account_name', $account_name);
            $stmt->bindParam(':api_key', $api_key);
            $stmt->bindParam(':api_secret', $api_secret);
            $stmt->execute();

            $stmt = null;

            $internal_account_id = $this->dbh->lastInsertId();
            
            $stmt = $this->dbh->prepare("INSERT INTO account_settings (internal_account_id , max_active_deals , active) VALUES (:internal_account_id , :max_active_deals , :active)");
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->bindValue (':max_active_deals', 0);
            $stmt->bindValue (':active', 0);
            $stmt->execute();
  
            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    
    /**
     * delete_account
     *
     * @param  int $user_id
     * @param  int $id
     * @return void
     */
    public function delete_account($user_id , $internal_account_id) {

        try{
               
            $stmt = $this->dbh->prepare("DELETE FROM accounts WHERE user_id = :user_id AND internal_account_id = :internal_account_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

            $stmt = $this->dbh->prepare("DELETE FROM account_settings WHERE internal_account_id = :internal_account_id");
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   

    }

    /**
     * enable/disable account
     *
     * @param  int $user_id
     * @param  int $id
     * @return void
     */
    public function enable_disable_account($internal_account_id , $setting) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE account_settings SET active = :setting WHERE internal_account_id = :internal_account_id");
            $stmt->bindParam(':setting', $setting);
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   

    }
    
    /**
     * Update max active deals
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_max_active_deals($internal_account_id , $deals) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE account_settings SET max_active_deals = :deals WHERE internal_account_id = :internal_account_id");
            $stmt->bindParam(':deals', $deals);
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   

    }

    /**
     * Update BO / Size
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_bo_size($internal_account_id , $size) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE account_settings SET bo_size = :size WHERE internal_account_id = :internal_account_id");
            $stmt->bindParam(':size', $size);
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   

    }


    /**
     * Update Telegram settings
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_telegram_settings($internal_account_id , $notify_telegram , $telegram_bot_hash , $telegram_chat_id) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE account_settings SET notify_telegram = :notify_telegram , telegram_bot_hash = :telegram_bot_hash , telegram_chat_id = :telegram_chat_id  WHERE internal_account_id = :internal_account_id");
            $stmt->bindParam(':notify_telegram', $notify_telegram);
            $stmt->bindParam(':telegram_bot_hash', $telegram_bot_hash);
            $stmt->bindParam(':telegram_chat_id', $telegram_chat_id);
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Update alerts , to not run it afterward
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_alert($input_id , $time_stamp) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE raw_tv_input SET processed = :processed , processed_time = :processed_time WHERE input_id = :input_id");
            $stmt->bindValue(':processed', 1);
            $stmt->bindParam(':processed_time', $time_stamp);
            $stmt->bindParam(':input_id', $input_id);
            $stmt->execute();

            $stmt = null;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Update alerts , set them in process so when 3c lags they aren't used again
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_alert_in_process($input_id) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE raw_tv_input SET processed = :processed  WHERE input_id = :input_id");
            $stmt->bindValue(':processed', 2);
            $stmt->bindParam(':input_id', $input_id);
            $stmt->execute();

            $stmt = null;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }
}
