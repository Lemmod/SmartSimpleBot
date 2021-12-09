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

            return $this->dbh->lastInsertId();
            
          
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    /**
     * insert_account settings
     *
     * @param  int $internal_account_id
     * @return void
     */
    public function insert_account_settings($internal_account_id) {
        
        try{
          
            $stmt = $this->dbh->prepare("INSERT INTO account_settings (internal_account_id , max_active_deals , active) VALUES (:internal_account_id , '0' , '0')");
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $exec = $stmt->execute();
  
            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }


    /**
     * Add specific bot settings per strategy
     */

     public function insert_bot_strategy($strategy_id , $bot_id , $data) {

        try{

            $stmt = $this->dbh->prepare("INSERT INTO bot_settings (strategy_id , bot_id ,  label , value) VALUES (:strategy_id , :bot_id , :label , :value)");

            foreach ($data as $label => $value) {
                $stmt->bindParam(':strategy_id', $strategy_id);
                $stmt->bindParam(':bot_id', $bot_id);
                $stmt->bindParam(':label', $label);
                $stmt->bindParam(':value', $value);
                $stmt->execute();
            }

            $stmt = null;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }  
     }

     /**
     * Add specific bot settings per strategy
     */

    public function insert_strategy($internal_account_id) {
     
        try{

            $stmt = $this->dbh->prepare("INSERT INTO strategies (internal_account_id , strategy_name) VALUES (:internal_account_id , :strategy_name)");

            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->bindValue(':strategy_name', 'new_strategy');
            $stmt->execute();

            $stmt = null;

            return $this->dbh->lastInsertId();
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }  
     }

    /**
     * Add specific bot settings per strategy
     */

    public function insert_timeframe($internal_account_id) {
     
        try{

            $stmt = $this->dbh->prepare("INSERT INTO time_frames (internal_account_id , label , description) VALUES (:internal_account_id , :label , :description)");

            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->bindValue(':label', 'new_timeframe');
            $stmt->bindValue(':description', 'A new description');
            $stmt->execute();

            $stmt = null;

            return $this->dbh->lastInsertId();
         
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }  
     }

    /**
     * Add specific bot settings per strategy
     */

    public function insert_timeframe_status($internal_account_id , $time_frame_id , $type) {
     
        try{

            $stmt = $this->dbh->prepare("INSERT INTO time_frame_status (account_id , time_frame_id , type) VALUES (:internal_account_id , :time_frame_id , :type)");

            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->bindParam(':type', $type);
            $stmt->execute();

            $stmt = null;
         
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }  
     }

    /**
     * Add an timeframe to exestings settings
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function insert_strategy_settings_timeframe_id($time_frame_id , $internal_account_id) {

        try{
               
            $stmt = $this->dbh->prepare("INSERT INTO strategy_settings (combination_id , strategy_id , time_frame_id , type)
            SELECT DISTINCT combination_id , strategy_id , :time_frame_id , 'short' FROM strategy_settings WHERE strategy_id IN (SELECT strategy_id FROM strategies WHERE internal_account_id = :internal_account_id)");
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Add a new row to the matrix
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function insert_strategy_settings($internal_account_id) {

        try{
               
            $stmt = $this->dbh->prepare("INSERT INTO strategy_settings (combination_id , strategy_id , time_frame_id , type)
            SELECT (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings) + 1 , strategy_id , time_frame_id , 'short' FROM strategy_settings WHERE combination_id = (SELECT MAX(combination_id) FROM `strategy_settings` WHERE strategy_id IN (SELECT strategy_id FROM strategies WHERE internal_account_id = :internal_account_id))");
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Add default strategies for new / pre 1.0 accounts
     */

    public function insert_default_strategies($internal_account_id) {

        try{
               
            $stmt = $this->dbh->prepare("
            INSERT INTO strategies (internal_account_id , strategy_name , locked)
            VALUES 
                (:internal_account_id , 'disabled' , 1),
                (:internal_account_id , 'safe' , 0),
                (:internal_account_id , 'normal' , 0),
                (:internal_account_id , 'aggresive' , 0)
            ");
           
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Add default strategies for new / pre 1.0 accounts
     */

    public function insert_default_timeframes($internal_account_id) {

        try{
               
            $stmt = $this->dbh->prepare("
            INSERT INTO time_frames (internal_account_id , label , description)
            VALUES 
                (:internal_account_id , 'low_tf' , 'For small tf , like 15m'),
                (:internal_account_id , 'med_tf' , 'For medium tf , like 1h'),
                (:internal_account_id , 'high_tf' , 'For long tf , like 4h')
            ");
           
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

     /**
     * Add default strategies for new / pre 1.0 accounts
     */

    public function insert_default_strategy_settings($internal_account_id) {

        try{
               
            $stmt = $this->dbh->prepare("
            INSERT INTO strategy_settings (combination_id , strategy_id , time_frame_id , type)
            VALUES 
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'disabled') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'disabled') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'disabled') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'short') ,
                                
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'short') ,
                
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'short') ,
                
                
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'safe') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'long') ,
                
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'short') ,
                
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'long') ,
                
                
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'short') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'normal') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'long') ,
                
                
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1) + 1 , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'aggresive') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'low_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'aggresive') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'med_tf') , 'long') ,
                ( (SELECT COALESCE(MAX(combination_id) , 0) FROM strategy_settings ss1)  , (SELECT strategy_id FROM strategies st1 WHERE internal_account_id = :internal_account_id AND strategy_name = 'aggresive') ,  (SELECT time_frame_id FROM time_frames WHERE internal_account_id = :internal_account_id AND label = 'high_tf') , 'long') 
            ");
           
            $stmt->bindParam(':internal_account_id', $internal_account_id);
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
     * Add specific bot settings per strategy
     */

    public function delete_bot_settings($strategy_id ) {

        try{

            $stmt = $this->dbh->prepare("DELETE FROM bot_settings WHERE strategy_id = :strategy_id");
            $stmt->bindParam(':strategy_id', $strategy_id);
            $stmt->execute();

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
     * Update max active deals
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_max_active_deals_strategy($strategy_id , $deals) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE strategies SET max_active_deals = :deals WHERE strategy_id = :strategy_id");
            $stmt->bindParam(':deals', $deals);
            $stmt->bindParam(':strategy_id', $strategy_id);
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
    public function update_strategy_settings($combination_id , $time_frame_id , $type) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE strategy_settings SET type = :type WHERE combination_id = :combination_id AND time_frame_id = :time_frame_id");
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':combination_id', $combination_id);
            $stmt->bindParam(':time_frame_id', $time_frame_id);
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
    public function update_strat_setting_strategy($combination_id , $strategy_id) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE strategy_settings SET strategy_id = :strategy_id WHERE combination_id = :combination_id");
            $stmt->bindParam(':combination_id', $combination_id);
            $stmt->bindParam(':strategy_id', $strategy_id);
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
    public function update_strat_name($strategy_id , $name) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE strategies SET strategy_name = :name WHERE strategy_id = :strategy_id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':strategy_id', $strategy_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Update time frame label
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_tf_label($time_frame_id , $label) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE time_frames SET label = :label WHERE time_frame_id = :time_frame_id");
            $stmt->bindParam(':label', $label);
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

        /**
     * Update time frame description
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_tf_dscription($time_frame_id , $description) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE time_frames SET description = :description WHERE time_frame_id = :time_frame_id");
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }  
    }

      /**
     * Update time frame description
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_tf_valid_time($time_frame_id , $min) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE time_frames SET validation_time = :validation_time WHERE time_frame_id = :time_frame_id");
            $stmt->bindParam(':validation_time', $min);
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }  
    }

          /**
     * Update time frame description
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function update_tf_valid_direction($time_frame_id , $direction) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE time_frames SET validation_direction = :validation_direction WHERE time_frame_id = :time_frame_id");
            $stmt->bindParam(':validation_direction', $direction);
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }  
    }


    /**
     * Delete strategy
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function delete_strategy($strategy_id) {

        try{
               
            $stmt = $this->dbh->prepare("DELETE FROM strategies WHERE strategy_id = :strategy_id");
            $stmt->bindParam(':strategy_id', $strategy_id);
            $stmt->execute();

            $stmt = null;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Delete strategy
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function delete_time_frame($time_frame_id) {

        try{
               
            $stmt = $this->dbh->prepare("DELETE FROM time_frames WHERE time_frame_id = :time_frame_id");
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();

            $stmt = null;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Delete strategy
     *
     * @param  mixed $internal_account_id
     * @param  mixed $setting
     * @return void
     */
    public function delete_matrix_row($combination_id) {

        try{
               
            $stmt = $this->dbh->prepare("DELETE FROM strategy_settings WHERE combination_id = :combination_id");
            $stmt->bindParam(':combination_id', $combination_id);
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
     * Update Usage of smart strategy
     */
    public function update_use_ss($internal_account_id , $value) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE account_settings SET use_smart_strategy = :value WHERE internal_account_id = :internal_account_id");
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();

            $stmt = null;

        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }   
    }

    /**
     * Update Strategy
     *
     * @param  mixed $internal_account_id
     * @param  mixed $mode
     * @return void
     */
    public function update_strategy($internal_account_id , $strategy_id) {

        try{
               
            $stmt = $this->dbh->prepare("UPDATE account_settings SET strategy_id = :strategy_id WHERE internal_account_id = :internal_account_id");
            $stmt->bindParam(':strategy_id', $strategy_id);
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
