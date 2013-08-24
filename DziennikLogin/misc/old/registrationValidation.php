<?php
//function to check password
function checkPassword($password, $username = false) {
    $length = strlen($password);

    if ($length < 8) {
        return FALSE;
    } elseif ($length > 32) {
        return FALSE;
    } else {

//check for a couple of bad passwords:
        if ($username && strtolower($password) == strtolower($username)) {
        return FALSE;
        } elseif (strtolower($password) == 'password') {
        return FALSE;
        } else {

            preg_match_all("/(.)\1{2}/", $password, $matches);
            $consecutives = count($matches[0]);

            preg_match_all("/\d/i", $password, $matches);
            $numbers = count($matches[0]);

            preg_match_all("/[A-Z]/", $password, $matches);
            $uppers = count($matches[0]);

            preg_match_all("/[^A-z0-9]/", $password, $matches);
            $others = count($matches[0]);

//see if there are 3 consecutive chars (or more) and fail!
            if ($consecutives > 0) {
        return FALSE;
            } elseif ($others > 1 || ($uppers > 1 && $numbers > 1)) {
//bulletproof
        return TRUE;
            } elseif (($uppers > 0 && $numbers > 0) || $length > 14) {
//very strong
        return TRUE;
            } else if ($uppers > 0 || $numbers > 2 || $length > 9) {
//strong
        return TRUE;
            } else if ($numbers > 1) {
//fair
        return FALSE;
            } else {
//weak
        return FALSE;
            }
        }
    }
    return $returns;
}
//function to check password strength
function checkPasswordErrors($password, $username = false) {
    $returns = array(
        'strength' => 0,
        'error' => 0,
        'text' => ''
    );

    $length = strlen($password);

    if ($length < 8) {
        $returns['error'] = 1;
        $returns['text'] = 'The password is not long enough';
    } elseif ($length > 32) {
        $returns['error'] = 1;
        $returns['text'] = 'The password is too long';
    } else {

//check for a couple of bad passwords:
        if ($username && strtolower($password) == strtolower($username)) {
            $returns['error'] = 4;
            $returns['text'] = 'Password cannot be the same as your Username';
        } elseif (strtolower($password) == 'password') {
            $returns['error'] = 3;
            $returns['text'] = 'Password is too common';
        } else {

            preg_match_all("/(.)\1{2}/", $password, $matches);
            $consecutives = count($matches[0]);

            preg_match_all("/\d/i", $password, $matches);
            $numbers = count($matches[0]);

            preg_match_all("/[A-Z]/", $password, $matches);
            $uppers = count($matches[0]);

            preg_match_all("/[^A-z0-9]/", $password, $matches);
            $others = count($matches[0]);

//see if there are 3 consecutive chars (or more) and fail!
            if ($consecutives > 0) {
                $returns['error'] = 2;
                $returns['text'] = 'Too many consecutive characters';
            } elseif ($others > 1 || ($uppers > 1 && $numbers > 1)) {
//bulletproof
                $returns['strength'] = 5;
                $returns['text'] = 'Virtually Bulletproof';
            } elseif (($uppers > 0 && $numbers > 0) || $length > 14) {
//very strong
                $returns['strength'] = 4;
                $returns['text'] = 'Very Strong';
            } else if ($uppers > 0 || $numbers > 2 || $length > 9) {
//strong
                $returns['strength'] = 3;
                $returns['text'] = 'Strong';
            } else if ($numbers > 1) {
//fair
                $returns['strength'] = 2;
                $returns['text'] = 'Fair';
            } else {
//weak
                $returns['strength'] = 1;
                $returns['text'] = 'Weak';
            }
        }
    }
    return $returns;
}
?>