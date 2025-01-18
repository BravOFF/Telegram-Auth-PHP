<?php
require_once ('.env.php');
global $BotUserName;
define('BOT_USERNAME', $BotUserName); // place username of your bot here

//print_r($_SERVER);

function getTelegramUserData() {
    if (isset($_COOKIE['tg_user'])) {

//        print_r($_COOKIE);

        $auth_data_json = urldecode($_COOKIE['tg_user']);
        $auth_data = json_decode($auth_data_json, true);
        return json_decode($auth_data['user'], true);
    }
    return false;
}

if ($_GET['logout']) {
    setcookie('tg_user', '');
    header('Location: index.php');
}

$tg_user = getTelegramUserData();
if ($tg_user !== false) {
    $first_name = htmlspecialchars($tg_user['first_name']);
    $last_name = htmlspecialchars($tg_user['last_name']);
    if (isset($tg_user['username'])) {
        $username = htmlspecialchars($tg_user['username']);
        $html = "<h1>Hello, <a href=\"https://t.me/{$username}\">{$first_name} {$last_name}</a>!</h1>";
    } else {
        $html = "<h1>Hello, {$first_name} {$last_name}!</h1>";
    }
    if (isset($tg_user['photo_url'])) {
        $photo_url = htmlspecialchars($tg_user['photo_url']);
        $html .= "<img src=\"{$photo_url}\">";
    }
    $html .= "<p><a href=\"?logout=1\">Log out</a></p>";
} else {
    $bot_username = BOT_USERNAME;
    $html = <<<HTML
<h1>Hello, anonymous!</h1>
<script async src="https://telegram.org/js/telegram-widget.js?2" data-telegram-login="{$bot_username}" data-size="large" data-auth-url="https://t.13ip.ru/check_authorization.php"></script>
HTML;
}

echo <<<HTML
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Login Widget Example</title>
    
    <script type="module">
    let tg_user = document.cookie.match(/(tg_user)=(.+?)(;|$)/)

    // console.log(!tg_user)
    if (!tg_user){
        let u = window.location.hash.slice(1);
        let p = new URLSearchParams(u)
        if (p.get('tgWebAppData')){
            let g = u.replace('#', '');
            let url = 'https://t.13ip.ru/check_authorization.php?';
            
            // console.log(p.get('tgWebAppData'));
            // console.log(g);
            // console.log(url);
            
            let res = await fetch(url+p.get('tgWebAppData'));
            if (res.ok) { 
              let json = await res.json();
              
              if (json.auth){
                  location.reload()
              }
              
              console.log(json)
          
            } else {
              alert("Ошибка HTTP: " + res.status);
            }
        }
      }
    </script>
    
  </head>
  <body><center>{$html}</center></body>
</html>
HTML;

?>
