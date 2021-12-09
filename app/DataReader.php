<?php

class DataReader extends Core
{

    public function get_account_info($bot_account_id) {

        try{
               
            $stmt = $this->dbh->prepare('SELECT * FROM accounts WHERE bot_account_id = :bot_account_id');
            $stmt->bindParam(':bot_account_id', $bot_account_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_account_info_internal($internal_account_id) {

        try{
               
            $stmt = $this->dbh->prepare('SELECT * FROM accounts WHERE internal_account_id = :internal_account_id');
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_account_settings($internal_account_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM account_settings WHERE internal_account_id = :internal_account_id');
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_user_credentials($user_name) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM users WHERE user_name = :user_name');
            $stmt->bindParam(':user_name', $user_name);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_user_accounts($user_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM accounts WHERE user_id = :user_id');
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_all_accounts() {

        try{

            $stmt = $this->dbh->prepare('SELECT internal_account_id , bot_account_id FROM accounts');
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_unprocessed_alerts($seconds_passed) {

        // Only fetch result not older then current_time - $seconds_passed seconds
        $start_time = date('Y-m-d H:i:s',time() - $seconds_passed);

        try{

            $stmt = $this->dbh->prepare('SELECT * , CASE WHEN input like \'%message%\' THEN 1 ELSE 2 END prio FROM raw_tv_input WHERE processed = :processed AND file_name = :file_name AND timestamp >= :start_time ORDER BY prio ASC');
            $stmt->bindValue(':processed', 0);
            $stmt->bindValue(':file_name', 'alert_handler.php');
            $stmt->bindParam(':start_time', $start_time);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_logbook($bot_account_id , $days = 7 , $system_message = 0) {

        $start_time = date('Y-m-d H:i:s',time() - (60 * 60 * 24 * $days));

        try{

            if ($system_message == 0) {
                $stmt = $this->dbh->prepare('SELECT * , unix_timestamp(timestamp) as ts FROM logbook WHERE account_id = :bot_account_id AND pair != "" AND timestamp >= :start_time ORDER BY log_id');
            } else {
                $stmt = $this->dbh->prepare('SELECT * , unix_timestamp(timestamp) as ts FROM logbook WHERE account_id = :bot_account_id AND pair = "" AND timestamp >= :start_time ORDER BY log_id');
            }
                $stmt->bindParam(':bot_account_id', $bot_account_id);
                $stmt->bindParam(':start_time', $start_time);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt = null;

                return $result;            
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_debuglog() {

        $start_time = date('Y-m-d H:i:s',time() - (60 * 60 * 24 * 1));

        try{

            $stmt = $this->dbh->prepare('SELECT * , unix_timestamp(time) as ts_hour FROM debug_calls WHERE time >= :start_time ORDER BY time DESC');
            $stmt->bindParam(':start_time', $start_time);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_strategies($internal_account_id , $skip_locked = false) {

        try{

            if (!$skip_locked) {
                $stmt = $this->dbh->prepare('SELECT * FROM strategies WHERE internal_account_id = :internal_account_id');
            } else {
                $stmt = $this->dbh->prepare('SELECT * FROM strategies WHERE internal_account_id = :internal_account_id AND locked != 1');
            }

            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_strategy_info($strategy_id) {

        try{


            $stmt = $this->dbh->prepare('SELECT * FROM strategies WHERE strategy_id = :strategy_id');
            
            $stmt->bindParam(':strategy_id', $strategy_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_strategy_id($internal_account_id , $value) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM strategies WHERE internal_account_id = :internal_account_id AND strategy_name = :value');
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result['strategy_id'];
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }


    public function get_time_frames($internal_account_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM time_frames WHERE internal_account_id = :internal_account_id');
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_time_frame_id($internal_account_id , $label) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM time_frames WHERE internal_account_id = :internal_account_id AND label = :label');
            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->bindParam(':label', $label);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result['time_frame_id'];
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_time_frame_status($time_frame_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM time_frame_status WHERE time_frame_id = :time_frame_id ORDER BY timestamp DESC LIMIT 1');
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result['type'];
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }
    }

    public function get_time_frame_last_update($time_frame_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT * , unix_timestamp(timestamp) as ts  FROM time_frame_status WHERE time_frame_id = :time_frame_id ORDER BY timestamp DESC LIMIT 1');
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return date("Y-m-d H:i:s" , $result['ts']);
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }
    }

    public function get_strategy_settings($internal_account_id) {

        try{

            $stmt = $this->dbh->prepare('
                SELECT DISTINCT combination_id , strategy_name , s.strategy_id FROM strategy_settings ss
                LEFT JOIN strategies s ON (ss.strategy_id = s.strategy_id)
                WHERE s.internal_account_id = :internal_account_id');

            $stmt->bindParam(':internal_account_id', $internal_account_id);
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_strategy_timeframe_type($combination_id , $time_frame_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT type FROM strategy_settings WHERE combination_id = :combination_id AND time_frame_id = :time_frame_id');
            $stmt->bindParam(':combination_id', $combination_id);
            $stmt->bindParam(':time_frame_id', $time_frame_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_strategy_setting_info($join , $filter) {


        try{

            $basic_query = "SELECT * FROM (
                SELECT
                combination_id,
                strategy_id,
                %s
                FROM strategy_settings p
                GROUP BY combination_id , strategy_id
                ) AS Q
                WHERE 1=1 %s";
  
            $query = sprintf($basic_query , $join , $filter);

            $stmt = $this->dbh->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    

    }

    public function get_set_bots($strategy_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT COUNT(*) as total FROM bot_settings WHERE strategy_id = :strategy_id');
            $stmt->bindParam(':strategy_id', $strategy_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result['total'];
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }            
    }
    

    public function get_bot_settings($bot_id , $strategy_id) {
        try{

            $stmt = $this->dbh->prepare('SELECT * FROM bot_settings WHERE bot_id = :bot_id AND strategy_id = :strategy_id');
            $stmt->bindParam(':bot_id', $bot_id);
            $stmt->bindParam(':strategy_id', $strategy_id);
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }
}