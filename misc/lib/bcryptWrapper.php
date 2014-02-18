<?php
class Bcrypt {
	
    private $work_factor;
 
    public function __construct($work_factor = 8) {
        if (CRYPT_BLOWFISH != 1) {
            throw new Exception("Bcrypt not supported in this installation. See http://php.net/crypt");
        }
 
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new Exception('Bcrypt requires openssl PHP extension');
        }
 
        if ($work_factor < 4 || $work_factor > 31) {
            throw new Exception("Bcrypt only supports work factors between 4 and 31 inclusive");
        }
 
        $this->work_factor = $work_factor;
    }
 
    public function hash($password) {
        $salt = 
            '$2a$' . str_pad($this->work_factor, 2, '0', STR_PAD_LEFT) . '$' .
            substr(
                strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.'), 
                0, 22
            )
        ;
        return crypt($password, $salt);
    }
 
    public function check($password, $stored_hash, $legacy_handler = NULL) {
        if (self::is_legacy_hash($stored_hash)) {
            if ($legacy_handler) return call_user_func($legacy_handler, $password, $stored_hash);
            else throw new Exception('Unsupported hash format');
        }
 
        $hash = crypt($password, $stored_hash);
 
        if (strlen($hash) !== strlen($stored_hash)) {
            return false;
        }
 
        // check each and every character to prevent timing attack against == operator
        $result = 0;
        for ($i = 0, $j = strlen($hash); $i < $j; $i++) {
            $result |= ord($hash[$i]) ^ ord($stored_hash[$i]);
        }
 
        return $result == 0;
    }
}
?>