<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 120);
error_reporting(E_ALL);
require './login.php';
require_once './assets/config.php';

if ($_SESSION['api_user']->hasMember === false) {
  header('Location: error.php?eCode=no_role');
  die();
}

echo "<script>console.log('User Info retrieved from Verifier API:')</script>";
echo "<script>console.log('" . json_encode($_SESSION['api_user']) . "')</script>";
echo "<script>console.log('User info retrieved from Discord OAuth2 grant:')</script>";
echo "<script>console.log('" . json_encode($_SESSION['oauth_user']) . "')</script>";
$psql = pg_connect("$db->host $db->port $db->name $db->credentials");
$already_voted = voteStatus($psql, $_SESSION['api_user']->id);

if (empty($already_voted)) {
  echo "<script>console.log('User has not voted yet!');</script>";
} else {
  header('Location: error.php?eCode=already_voted');
  die();
}

if ('2021-06-25' < date('Y-m-d')) {
  header('Location: error.php?eCode=concluded');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASE | Rösta på representanter</title>
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
    <section id="userinfo">
      <div class="user-container">
        <div class="userimg">
          <img src="<?=$_SESSION['api_user']->avatar?>" alt="Din profilbild på Discord">
        </div>
        <div class="usercreds">
          <h1 class="username"><?=$_SESSION['api_user']->user?></h1>
          <p>Discord status: <?=$_SESSION['api_user']->presence?></p>
          <p>Discord ID: <?=$_SESSION['api_user']->id?></p>
        </div>
      </div>
      <div id="logout">
        <a href="./login.php?action=logout" id="confirm-btn">Logga ut</a>
      </div>
    </section>
    <section id="choices">
      <h1>Rösta</h1>
      <form action="sendVote.php" id="votes" method="post">
        <p>Notera att fler val kan göras!<br><br></p>
        <div id="nomineeList">
          <?php
            $ret = pg_query($psql, "SELECT COUNT(*), nominee FROM votes GROUP BY nominee;");

            if (!$ret) {
              header('Location: error.php?eCode=db_err&eStack='.pg_last_error($psql));
            }

            while ($row = pg_fetch_row($ret)) {
              $nominee = apiRequest('http://verifier.obliv1on.space/v1/user/find/id/'. $row[1] . '/185178535059521537');
              if ($row[0] > 1) {
                $txt = "röster";
              } else {
                $txt = "röst";
              }
              echo "<input type='checkbox' id='{$row[1]}' name='{$row[1]}' value='{$row[1]}' class='toggle'>";
              if (empty($nominee->nickname)) {
                echo "<label for='{$row[1]}' >{$nominee->username} ({$nominee->user})</label><br><br>";
              } else {
                echo "<label for='{$row[1]}' >{$nominee->nickname} ({$nominee->user})</label><br><br>";
              }
            }
          ?>
        </div>
        <p class="center"> Jag har dubbelkollat mina val</p>
        <input type="checkbox" class="toggle toggle-center" id="confirm" onclick="showBtn()">
        <input type="submit" id="voteSend" value="Skicka in" style="margin: 0 auto; display: none">
        <br>
      </form>
      <div>
        <p style="font-weight: bold">Saknas medlemmen du vill nominera?</p>
        <p>Du kan lägga till en eller fler medlemmar i denna lista genom att föra in deras Discord-id i fältet nedan.<br><br></p>
        <div class="add-nominee">
          <input type="text" id="customId" name="customId" class="field" placeholder="Användar-id">
          <button type="button" id="addNominee" onclick="addNominee()">Lägg till</button>
        </div>
        <p><a href="https://www.youtube.com/watch?v=1T0L4c9hWTo" target="_blank">Mer information om hur du kan få ut användar-id</a></p><br><br>
      </div>
    </section>
    <nav class="footer">
      <p>Denna sida har skapats för <a href="https://armasweden.se">Arma Sweden</a></p> 
    </nav>
  </div>
</body>
</html>
<script>
  var nomineeLimit = 0;

  function showBtn() {
    var cb = document.getElementById('confirm');
    var btn = document.getElementById('voteSend');

    if (cb.checked == true) {
      btn.style.display = "block";
    } else {
      btn.style.display = "none";
    }
  }

  function addNominee() {
    var btn = document.getElementById('addNominee');
    var idField = document.getElementById('customId');
    var nl = document.getElementById('nomineeList');
    var check = document.getElementById(idField.value);
    console.log(nl);
    console.log(idField.value);

    if (idField.value == "" || idField.value == null) {
      alert('Fältet för att lägga till fler medlemmar kan ej vara tomt!');
      return
    } else if (check) {
      alert('Medlemmen finns redan i listan!');
      idField.value = "";
      return;
    } else if (idField.value == '231144147291996161' || idField.value == '213279544927322112' || idField.value == '157628163365666816') {
      alert('Du kan ej rösta på medlemmar som redan är en del av ledningen!');
      idField.value = "";
      return;
    } else if (idField.value == "<?=$_SESSION['api_user']->id?>") {
      alert('Du kan ej rösta på dig själv!');
      idField.value = "";
      return;
    }

    if (nomineeLimit >= 3) {
      alert('du får ej lägga till mer än tre nya kandidater.');
      idField.value = "";
      return;
    }

    fetch('http://verifier.obliv1on.space/v1/user/find/id/' + idField.value + '/185178535059521537')
      .then(response => response.json())
      .then(userObj => {
        if (userObj.error == null || userObj.error == undefined) {
    
          let name = "";
    
          if (userObj.nickname == null || userObj.nickname == undefined) {
            name = userObj.username;
          } else {
            name = userObj.nickname;
          }
    
          if (confirm(`Vill du verkligen lägga till ${name} (${userObj.user})? \nMedlemmen kommer ej att stanna kvar i listan om du ej röstar på dem.`)) {
            console.log('confirmed!')
            let inp = document.createElement('input');
            inp.type = 'checkbox';
            inp.id = userObj.id;
            inp.name = userObj.id;
            inp.value = userObj.id;
            inp.className  = 'toggle';
            inp.checked = true;

            let lbl = document.createElement('label');
            lbl.for = userObj.id;
            lbl.innerHTML = `${name} (${userObj.user})`;

            nl.appendChild(inp);
            nl.appendChild(lbl);
            nl.appendChild(document.createElement('br'));
            nl.appendChild(document.createElement('br'));
            nomineeLimit++;
            idField.value = "";
          } else {
            idField.value = "";
          }
        }
      }).catch(error => {
        console.log(error);
        alert(`Användare med ID "${idField.value}" kunde ej hittas`);
      })
  }
</script>
<link rel="stylesheet" href="./assets/css/style.css">