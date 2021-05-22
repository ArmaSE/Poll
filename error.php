<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASE | Fel</title>
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
      <h1>Ett fel har uppstått (<?=$_GET['eCode']?>)</h1>
      <p class="text">
        <?php
          $hlp_str = ' Var god skicka följande information till obliv1on#1337 på Discord:<br><br>';
          $cont_str = ' Om du upplever detta som felaktigt, kan du meddela obliv1on#1337 på Discord för att åtgärda problemet.';

          if (isset($_GET['eCode'])) {
            switch ($_GET['eCode']) {
              case 'auth_err':
                echo 'Ett fel har skett vid autentiseringen.'.$hlp_str;
                echo '"' . $_GET['eDesc'] . '"';
                break;
              case 'already_voted':
                echo 'Ditt konto har redan använts för att rösta.'.$cont_str;
                break;
              case 'no_role':
                echo 'Du kan tyvärr inte rösta då du ej har medlemsrollen på servern. Verifiera ditt Discord-konto på hemsidan och försök igen.';
                break;
              case 'db_err':
                echo 'Ett fel har uppstått med databasen.'.$hlp_str;
                echo $_GET['eStack'];
                break;
              case 'concluded':
                echo 'Sista valdatum har nu passerat. Nya svar kan ej skickas in.';
                break;
              default:
                echo 'Ett okänt fel har uppstått. Kontakta obliv1on#1337 på Discord för mer informaiton';
                break;
            }
          } else {
            echo 'Ett okänt fel har uppstått. Kontakta obliv1on#1337 på Discord för mer informaiton';
          }
        ?>
      </p>
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