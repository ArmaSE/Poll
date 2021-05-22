<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('max_execution_time', 120);
  error_reporting(E_ALL);
  require_once './assets/config.php';
  $psql = pg_connect("$db->host $db->port $db->name $db->credentials");

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
  
    // if(session('access_token'))
    //   $headers[] = 'Authorization: Bearer ' . session('access_token');
  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
    $response = curl_exec($ch);
    return json_decode($response);
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASE | Resultat</title>
</head>
<body>
  <div class="fullscreen-bg">
    <video loop muted autoplay class="fullscreen-bg__video">
        <source src="./assets/videos/background.mp4" type="video/mp4">
    </video>
  </div>
  <div id="content">
    <nav class="header">
      <h1>Arma Sweden</h1>
    </nav>
    <section class="msg">
      <h1>Nuvarande resultat</h1>
      <table>
          <tr>
            <th>Medlem</th>
            <th>Antal röster</th>
          </tr>
          <?php
            $ret = pg_query($psql, "SELECT COUNT(*) AS votecount, nominee FROM votes GROUP BY nominee ORDER BY votecount DESC;");
            if (!$ret) {
              header('Location: error.php?eCode=db_err&eStack='.pg_last_error($psql));
            }

            while ($row = pg_fetch_row($ret)) {
              $nominee = apiRequest('http://verifier.obliv1on.space/v1/user/find/id/'. $row[1] . '/185178535059521537');
              echo "<script>console.log('" . json_encode($row) . "')</script>";
              echo "<tr>";
              if (empty($nominee->nickname)) {
                echo "<td>{$nominee->username} ({$nominee->user})</td>";
              } else {
                echo "<td>{$nominee->nickname} ({$nominee->user})</td>";
              }
              echo "<td>{$row[0]}</td>";
              echo "</tr>";
            }
          ?>
      </table>
    </section>
    <section class="prompt">
      <div id="vote-btn">
        <button onclick="window.location.href='index.php'" id="confirm-btn">Tillbaka till startsida</button>
      </div>
    </section>
    <nav class="footer">
      <p>
        Denna sida har skapats för <a href="https://armasweden.se">Arma Sweden</a>
      </p>
    </nav>
  </div>
</body>
</html>
<link rel="stylesheet" href="assets/css/style.css">