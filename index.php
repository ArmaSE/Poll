<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASE | Röstinsamling för ny ledning</title>
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
    <section class="about">
      <h1>Vad är detta?</h1>
      <p class="text">
        Denna sida har skapats för att Arma Swedens medlemmar ska kunna på ett säkert sätt rösta fram nya medlemmar till ledningen.<br>
        <br>
        Lösning är byggd för att medlemmar ska kunna autentisera sig m.h.a sina Discord-konton. <b>OBS:</b> kontot måste ha rollen "medlem" tilldelad i ASE:s server. Om du saknar denna roll, kan du gå in på <a href="https://armasweden.se" target="_blank">hemsidan</a> där du kan logga in och få rollen tilldelad.<br>
        <br>
        Denna sida håller enbart koll på två listor. En lista där samtliga svar lagras (enbart svar!), samt en lista över medlemmar som redan har röstat. Din röst kommer alltså inte kunna spåras tillbaka till dig.<br>
        Av denna orsak är det möjligt att enbart lämna in sina val en gång. Därför är det <u>extremt</u> viktigt att du dubbelkollar dina val innan du skickar in dem.<br>
        <br>
        Om du vill säkerställa att denna röstinsamling sker på ett opartiskt sätt samt att den inte påverkas av tredje part är du mer än välkommen att undersöka <a href="https://github.com/ArmaSE/Poll" target="_blank">källkoden</a> för denna sida, som hålls publik för just detta syfte.<br>
        <br>
        Resultaten för röstinsamlingen kan undersökas omgående på <a href="results.php" target="_blank">resultatsidan</a>.
      </p>
    </section>
  
    <section class="prompt">
      <h1>Gå till röstningen</h1>
      <p class="question">Har du läst igenom allt och är redo att skicka in dina svar?</p>
      <p class="center">
        Jag är redo!
        <div class="switch">
          <input id="switch-1" type="checkbox" class="switch-input" onclick="showBtn()">
          <label for="switch-1" class="switch-label">Switch</label>
        </div>
      </p>
      
      <div id="vote-btn">
        <button onclick="window.location.href='vote.php?action=login'" id="confirm-btn" disabled>Rösta</button>
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
<script>
  function showBtn() {
    var cb = document.getElementById('switch-1');
    var btn = document.getElementById('confirm-btn');

    if (cb.checked == true) {
      btn.disabled = false;
    } else {
      btn.style.pointerEvents = true;
    }
  }
</script>
<link rel="stylesheet" href="assets/css/style.css">