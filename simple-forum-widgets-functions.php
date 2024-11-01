<?php

define('JS_TIMEOUT', 24 * 60);

function WriteSFConnect($User, $Request, $ClientID, $Secret, $Secure = TRUE) {
    $User = array_change_key_case($User);
    $time = time();
    $sf_base_url = get_option('sf_base_url');
    $return_url = strpos($Request['return_url'], $sf_base_url);
    if ($Secure) {
        if (!isset($Request['client_id'])) {
            $Error = array('error' => 'invalid_request', 'message' => 'The client_id parameter is missing.');
        } elseif ($Request['client_id'] != $ClientID) {
            $Error = array('error' => 'invalid_client', 'message' => "Unknown client {$Request['client_id']}.");
        } elseif ($return_url === false) {
            $Error = array('error' => 'invalid_return_url', 'message' => "Invalied return url {$Request['return_url']}.");
        } elseif (!isset($Request['timestamp']) && !isset($Request['signature'])) {
            if (is_array($User) && count($User) > 0) {
                $Error = array('name' => $User['first_name']);
            } else {
                $Error = array('name' => '');
            }
        } elseif (!isset($Request['timestamp']) || !is_numeric($Request['timestamp'])) {
            $Error = array('error' => 'invalid_request', 'message' => 'The timestamp parameter is missing or invalid.');
        } elseif (!isset($Request['signature'])) {
            $Error = array('error' => 'invalid_request', 'message' => 'Missing  signature parameter.');
        } elseif (($Diff = abs($Request['timestamp'] - $time)) > JS_TIMEOUT) {
            $Error = array('error' => 'invalid_request', 'message' => 'The timestamp is invalid.');
        } else {
            $Signature = md5($Request['timestamp'].$Secret);
            if ($Signature != $Request['signature'])
            $Error = array('error' => 'access_denied', 'message' => 'Signature invalid.');
        }
    }

    if (isset($Error)) {
        $Result = $Error;
    } elseif (is_array($User) && count($User) > 0) {
        if ($Secure === NULL) {
            $Result = $User;
        } else {
            $Result = SignSFConnect($User, $ClientID, $Secret, TRUE);
        }
    } else {
        $Result = array('error' => TRUE);
    }
    $Json = json_encode($Result);

    if (isset($Request['callback'])) {
        return "{$Request['callback']}($Json);";
    } else {
        return $Json;
    }
}

function SignSFConnect($Data, $ClientID, $Secret, $ReturnData = FALSE) {
    $Data = array_change_key_case($Data);
    ksort($Data);

    foreach ($Data as $Key => $Value) {
        if ($Value === NULL)
        $Data[$Key] = '';
    }

    $String = http_build_query($Data);

    $Signature = md5($String.$Secret);

    if ($ReturnData) {
        $Data['client_id'] = $ClientID;
        $Data['signature'] = $Signature;

        return $Data;
    } else {
        return $Signature;
    }
}
