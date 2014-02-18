<?php
/*
$privateKey = openssl_pkey_new(array(
    'private_key_bits' => 1024,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
));
openssl_pkey_export($privateKey, $out);
echo $out.'<br>';
$a_key = openssl_pkey_get_details($privateKey);
echo $a_key['key'].'<br>';
openssl_free_key($privateKey);
 *
 */

$privateKey = openssl_pkey_new(array(
    'private_key_bits' => 1024,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
));
openssl_pkey_export_to_file($privateKey, 'private.key');
$a_key = openssl_pkey_get_details($privateKey);
file_put_contents('public.key', $a_key['key']);
openssl_free_key($privateKey);

echo'KEY GENERATED!';
?>
