<?php
define('BOT_TOKEN', '7833092763:AAEgqsSBP12Inqb34JERSW8hTBu5Nw1-uL0'); // place bot token of your bot here

//print_r($_REQUEST);
function checkTelegramAuthorization($auth_data) {
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);


    $data_check_string = implode("\n", $data_check_arr);

    $secret_key = hash_hmac('sha256', BOT_TOKEN, 'WebAppData', true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    if (strcmp($hash, $check_hash) !== 0) {
        throw new Exception('Data is NOT from Telegram');
    }
    if ((time() - $auth_data['auth_date']) > 864000) {
        throw new Exception('Data is outdated');
    }
    return $auth_data;
}
function saveTelegramUserData($auth_data) {
    $auth_data_json = json_encode($auth_data);

    setcookie('tg_user', $auth_data_json);
}


try {
    $auth_data = checkTelegramAuthorization($_GET);
    saveTelegramUserData($auth_data);
    $arr = ['auth' => true];
    echo json_encode($arr);
} catch (Exception $e) {
    die ($e->getMessage());
}



