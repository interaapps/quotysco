<?php
namespace app\classes;

use modules\uloleorm\SQL;
use databases\QuotyscoSessionsTable;
use ulole\core\classes\util\Str;

/**
 * InteraAps Auth for Ulole-Framwork
 * 
 * @requires(ULOLE-ORM [uppm: uloleorm] )
 */
class User {
    
    public $key,
           $id,
           $userdata,
           $session;

    public function __construct($key, $id="") {
        $userData = self::getUserInformation($key);
        $this->key = str_replace('"', '\"', $key);
        $this->id  = $userData->id;
    }
    
    public function login() {
        $connection = SQL::$databases["main"]->con;
        $this->session = Str::random(100);

        $newLogin = new QuotyscoSessionsTable;
        $newLogin->session_id = $this->session;
        $newLogin->userid     = $this->id;
        $newLogin->user_key   = $this->key;
        $newLogin->save();
    }
    
    public function getUserData() {
        return getUserInformation($this->key);
    }


    public static function findUser($query, $limit = false) {
        global $ULOLE_CONFIG_ENV;
        $postdata = 
            [
                'key' => $ULOLE_CONFIG_ENV->Auth->interaapps->key,
                "query"=>json_encode($query)
            ];

        if ($limit !== false)
            $postdata["limit"] = $limit;


        $postdata = http_build_query($postdata);
    
        $opts = ['http' =>[
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            ]];
        $context  = stream_context_create($opts);
        $result = file_get_contents('https://accounts.interaapps.de/oauth_api/finduser', false, $context);
        $resultJson = json_decode($result);
        return json_decode($result);
    }

    public static function getUserInformation($user) {
        global $ULOLE_CONFIG_ENV;
        $postdata = http_build_query(
            ['key' => $ULOLE_CONFIG_ENV->Auth->interaapps->key,
                'userkey' => $user
            ]);
    
        $context = stream_context_create(['http' =>[
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            ]]);
        $result = file_get_contents('https://accounts.interaapps.de/oauth_api/getuserinformation', false, $context);
        $resultJson = json_decode($result);
    
        if ($resultJson->valid)
            return json_decode($result);
        else return false;
    }
    
    public static function loggedIn() {
        $connection = SQL::$databases["main"]->con;
        if (!isset($_COOKIE["InteraApps_auth"])) return false;
        
        $loggedIn = (new QuotyscoSessionsTable)
                        ->select('*')
                        ->where("session_id", $_COOKIE["InteraApps_auth"])
                        ->first();   
        return $loggedIn["id"] !== null;
    }
    
    
    public static function getUserObject() {
        $connection = SQL::$databases["main"]->con;
        return self::getUserInformation(    
            (new QuotyscoSessionsTable)
                        ->select('*')
                        ->where("session_id", $_COOKIE["InteraApps_auth"])
                        ->first()["user_key"]
        );
    }

    public static function usingIaAuth() {
        global $ULOLE_CONFIG_ENV;
        if (isset($ULOLE_CONFIG_ENV->Auth->using)) {
            return $ULOLE_CONFIG_ENV->Auth->using == "interaapps";
        }
        return false;
    }
    
}