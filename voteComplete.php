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
      <h1>Dina val har nu skickats in</h1>
      <p class="text">
        <?php
          if (isset($_GET['editCode'])) {
            echo "Var vänlig spara din unika omröstningskod:";
          } else {
            echo 'Du har nu skickat in dina val.';
          }
        ?>
      </p>
      <?php 
        if (isset($_GET['editCode'])) {
          echo "<h1>{$_GET['editCode']}</h1>";
        }
      ?>
      <p class="text">Omröstningskoden är anonym, och används enbart ifall något har gått snett under omröstningen. Koden kan ej kopplas till ditt konto.</p>
    </section>
    <section class="prompt">
      <div id="vote-btn">
        <button onclick="window.location.href='results.php'" id="confirm-btn">Till resultatsida</button>
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