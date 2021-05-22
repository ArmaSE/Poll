<?php
ini_set('max_execution_time', 300);
require './assets/config.php';

define('OAUTH2_CLIENT_ID', $OAuth2_ID);
define('OAUTH2_CLIENT_SECRET', $OAuth2_Secret);

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';
$revokeURL = 'https://discord.com/api/oauth2/token/revoke';

session_start();

// Begin login process for user.
if(get('action') == 'login') {
  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'http://polltest.obliv1on.com/vote.php',
    'response_type' => 'code',
    'scope' => 'identify guilds'
  );
  header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}

if(get('code')) {
  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => 'http://polltest.obliv1on.com/vote.php',
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;
}

if(session('access_token')) {
  $user = apiRequest($apiURLBase);
  $_SESSION['oauth_user'] = $user;
  $api_user = apiRequest('http://verifier.obliv1on.space/v1/user/find/id/'. $user->id . '/185178535059521537');
  $_SESSION['api_user'] = $api_user;
} else {
  header('Location: error.php?eCode=auth_err&eDesc=genericOAuthErr');
}

// Log out user's session token
if(get('action') == 'logout') {
  apiRequest($revokeURL, array(
      'token' => session('access_token'),
      'client_id' => OAUTH2_CLIENT_ID,
      'client_secret' => OAUTH2_CLIENT_SECRET,
    ));
  unset($_SESSION['access_token']);
  header('Location: index.php');
  die();
}

function apiRequest($url, $post=FALSE, $headers=array()) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  $response = curl_exec($ch);


  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if(session('access_token'))
    $headers[] = 'Authorization: Bearer ' . session('access_token');

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}

function get($key, $default=NULL) {
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}

function voteStatus($psql, $userId) {
  $check = pg_query($psql, "SELECT * from voter_list where user_snowflake = '{$userId}' limit 1");

  return pg_fetch_row($check);

}

?>