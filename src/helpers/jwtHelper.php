<?php
include_once '../../config/config.php';

class JwtHelper{

    private static $instance = null;
     
    private $secretKey;
    private $algorithm;
    private $expiration;

    public function __construct(){
        global $SECRET_KEY, $ALGORITHM, $EXPIRATION;
        $this->secretKey = $SECRET_KEY;
        $this->algorithm = $ALGORITHM;
        $this->expiration = $EXPIRATION;
    }

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new JwtHelper();
        }
        return self::$instance;
    }
    
    public function generate($username) {
        $time = time();
        $payload = array(
            'iat' => $time,
            'exp' => $time + $this->expiration,
            'data' => $username
        );
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    private function extract($token) {
        try {
            $decoded = JWT::decode($token, $this->secretKey, array($this->algorithm));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    public function validate($token) {
        $decoded = $this->extract($token);
        if ($decoded == null) {
            return null;
        }
        $time = time();
        if ($decoded->exp < $time) {
            return null;
        }
        return $decoded->data;
    }
        
}


?>