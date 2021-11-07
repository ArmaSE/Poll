<?php
  print_r($_POST);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('max_execution_time', 120);
  error_reporting(E_ALL);
  session_start();
  require_once './assets/config.php';
  $psql = pg_connect("$db->host $db->port $db->name $db->credentials");
  echo "<script>console.log('User Info retrieved from Verifier API:')</script>";
  echo "<script>console.log('" . json_encode($_SESSION['api_user']) . "')</script>";
  echo "<script>console.log('User info retrieved from Discord OAuth2 grant:')</script>";
  echo "<script>console.log('" . json_encode($_SESSION['oauth_user']) . "')</script>";

  $edit_code = generateCode(16);
  $votedList = [];
  foreach ($_POST as $nomineeId) {
    if (!in_array($nomineeId, $votedList)) {
      $insert = "INSERT INTO votes (nominee, edit_code) VALUES ('{$nomineeId}', '{$edit_code}')";
      $res = pg_query($psql, $insert);
      array_push($voted, $nomineeId);
    } else {
      echo "<script>console.log('Duplicate vote detected, omitting.')</script>";
    }

  }

  $voted = "INSERT INTO voter_list (user_snowflake) VALUES ('{$_SESSION['api_user']->id}')";
  $res = pg_query($psql, $voted);
  header("Location: voteComplete.php?editCode=".$edit_code);

  function generateCode($length) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charLength = strlen($chars);
    $code = '';
    for ($i = 0; $i < $length; $i++) {
      $code .= $chars[rand(0, $charLength - 1)];
    }

    return $code;
  }
?>