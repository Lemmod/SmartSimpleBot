<?php

/*****

Remember , script is under construction and not documented but the basics.

Use this script at your own risk!

It won't contain all possibilitys from the 3c API , mainly used for updating multiple bots at once

(c) 2021 - MileCrypto (Lemmod)

*/

namespace MC3Commas;

use Exception;

define ('BASE_URL' , 'https://api.3commas.io');

class threeCommas {

    private $debug_curl = false;

    function __construct($base_url , $api_key , $api_secret) {
        $this->base_url = $base_url;
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }
    
    /**
     * Send the request to 3 commas api
     *
     * @param  mixed $url
     * @param  mixed $params
     * @param  mixed $method
     * @return void
     */
    function signed_request($url , $params = [] , $method = 'GET') {

        if (function_exists('curl_init') === false) {
            die("Sorry , curl isn't installed");
        }

        // Add params to query
        $api_info_query = http_build_query( ['api_key' => $this->api_key , 'secret' => $this->api_secret] , '&');
        $param_query = http_build_query($params , '&');

        // Make a signature
        $request_url =  '/public/api' . $url . '?' . $api_info_query . '&' . $param_query;
        $signature = hash_hmac('sha256', $request_url, $this->api_secret);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_VERBOSE, $this->debug_curl);
        curl_setopt($curl, CURLOPT_URL, $this->base_url.$request_url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
        }

        if ($method == 'PATCH') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        }

        $headers = array();
        $headers[] = 'Apikey: '.$this->api_key;
        $headers[] = 'Signature: '.$signature;
        curl_setopt($curl , CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($curl);


        $json = json_decode($output, true);

        if (isset($json['error'])) {
            
            throw new Exception($output);
            
            return Exception; 
        }

        curl_close($curl);

        return $json;
    }
    
    /**
     * simple debug , nicer to view in browser
     *
     * @param  mixed $data , data to debug
     * @return void
     */
    function debug_info($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }


    /**
     * Ping the server
     *
     * @return void
     */
    function ping() {
        return $this->signed_request('/ver1/ping');
    }


    
    /***************************************************\ 
     *                                                  *
     *                     Accounts                     * 
     *                                                  *
     /**************************************************/

        
    /**
     * Get all accounts , see for more detailed information official 3 commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/accounts_api.md
     *
     * @return void
     */
    function get_all_accounts() {
        return $this->signed_request('/ver1/accounts');
    }

    function get_single_account($id) {
        return $this->signed_request('/ver1/accounts/'.$id);
    }

     /***************************************************\ 
     *                                                  *
     *                       Bots                       * 
     *                                                  *
     /**************************************************/

    
    /**
     * Get all the bots
     *
     * @param  mixed $params , refer to official 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
     * @return void
     */
    function get_all_bots($params = []) {
        return $this->signed_request('/ver1/bots' , $params , 'GET');
    }

    /**
     * Get the specific stats for bots
     *
     * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
     * @return void
     */
    function get_bot_stats($params = []) {
        return $this->signed_request('/ver1/bots/stats' , $params , 'GET');
    }

    /**
     * Gets the general black list for bots
     *
     * @return void
     */
    function get_pairs_black_list() {
        return $this->signed_request('/ver1/bots/pairs_black_list');
    }
  
    /**
     * Update the general black list for bots
     *
     * @param  mixed $params
     * @return void
     */
    function update_pairs_black_list($params) {
        return $this->signed_request('/ver1/bots/pairs_black_list' , $params , 'POST');
    }    

  
    /**
     * Get the strategy , eg. TV Custom Singals , Manual etc.
     *
     * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
     * @return void
     */
    function get_bots_strategy_list($params = []) {
        return $this->signed_request('/ver1/bots/strategy_list' , $params , 'GET');
    }

    /**
     * Disable / Stop the bot , deals will continue
     *
     * @param  mixed $id , specific bot id
     * @return void
     */
    function disable_bot($id) {
        return $this->signed_request('/ver1/bots/'.$id.'/disable' , [] , 'POST');
    }

    /**
     * Enable / start the bot
     *
     * @param  mixed $id , specific bot id
     * @return void
     */
    function enable_bot($id) {
        return $this->signed_request('/ver1/bots/'.$id.'/enable' , [] , 'POST');
    }

 
    /**
     * Start a deal on a specific bot
     *
     * @param  mixed $id , specific bot id
     * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
     * @return void
     */
    function start_deal_on_bot($id , $params = []) {
        return $this->signed_request('/ver1/bots/'.$id.'/start_new_deal' , $params , 'POST');
    }

    /**
     * Get detailed information for an bot
     *
     * @param  mixed $id , specific bot id
     * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
     * @return void
     */
    function get_bot_info($id , $params = []) {
        return $this->signed_request('/ver1/bots/'.$id.'/show' , $params , 'GET');
    }

    /**
     * Update a bot , set new parameters like SO and others
     *
     * @param  mixed $id , specific bot id
     * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/bots_api.md
     * @return void
     */
    function update_bot($id , $params = []) {
        return $this->signed_request('/ver1/bots/'.$id.'/update' , $params , 'PATCH');
    }

     /***************************************************\ 
     *                                                  *
     *                     GridBots                     * 
     *                                                  *
     /**************************************************/

    /**
     * 
     */    
    /**
     * Get all grid bots
     *
     * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/grid_bots_api.md
     * @return void
     */
    function get_grid_bots($params = []) {
        return $this->signed_request('/ver1/grid_bots' , $params , 'GET');
    }
  
    /**
     * Get all grid bot market order by specific grid bot {id} from get_grid_bots()
     *
     * @param  mixed $id , grid bot id
     * @return void
     */
    function get_grid_bot_market_orders ($id) {

        return $this->signed_request('/ver1/grid_bots/'.$id.'/market_orders' , [] , 'GET');
    }
    
     /***************************************************\ 
     *                                                  *
     *                       Deals                      * 
     *                                                  *
     /**************************************************/

     /**
      * Get deals (all bots)
      *
      * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/deals_api.md
      * @return void
      */
     function get_deals ($params = []) {
        return $this->signed_request('/ver1/deals' , $params , 'GET');
     }

    /***************************************************\ 
     *                                                  *
     *                  Smart-Trades                    * 
     *                                                  *
     /**************************************************/

     /**
      * Get all smart trades
      *
      * @param  mixed $params , refer to 3commas api https://github.com/3commas-io/3commas-official-api-docs/blob/master/smart_trades_v2_api.md
      * @return void
      */
     function get_smart_trades ($params = []) {
        return $this->signed_request('/v2/smart_trades' , $params , 'GET');
     }
}

