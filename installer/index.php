<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="lib/bootstrap-5.2.3.css" rel="stylesheet">
    <title>install_uno.php</title>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col">
          <h1>Installer</h1>
        </div>
      </div>
      <div class="row">
        <div class="col col-6">
          <form method="post" action="install.php">
            <label class="form-label" for="db_host">Host</label>
            <input class="form-control form-control-sm" type="text" id="db_host" name="db_host">
            <label class="form-label" for="db_name">Database name:</label>
            <input class="form-control form-control-sm" type="text" id="db_name" name="db_name">
            <label class="form-label" for="db_user">Database user:</label>
            <input class="form-control form-control-sm" type="text" id="db_user" name="db_user">
            <label class="form-label" for="db_pass">Database user's password:</label>
            <input class="form-control form-control-sm" type="text" id="db_pass" name="db_pass">
            <label class="form-label" for="instance_title">Site name:</label>
            <input class="form-control form-control-sm" type="text" id="site_name" name="site_name">
            <label class="form-label" for="admin_user">Admin username:</label>
            <input class="form-control form-control-sm" type="text" id="admin_user" name="admin_user">
            <label class="form-label" for="admin_user_email">Admin email:</label>
            <input class="form-control form-control-sm" type="text" id="admin_user_email" name="admin_user_email">
            <label class="form-label" for="admin_user_pwd_1">Admin user password:</label>
            <input class="form-control form-control-sm" type="text" id="admin_user_password_1" name="admin_user_password_1">
            <label class="form-label" for="admin_user_pwd_2">Admin user password:</label>
            <input class="form-control form-control-sm" type="text" id="admin_user_password_2" name="admin_user_password_2">
        </div>
        <div class="col col-6">
            <label class="form-label" for="jwt_key">Token salt:</label>
            <input class="form-control form-control-sm" type="text" id="jwt_key" name="jwt_key">
            <label class="form-label" for="house_account_name">House Account Name:</label>
            <input class="form-control form-control-sm" type="text" id="house_account_name" name="house_account_name">
            <label class="form-label" for="default_locale">Locale</label>
            <select class="form-select form-select-sm" aria-label="Default select example" name="default_locale" id="default_locale">
              <option value="en">English</option>
              <option value="es">Español</option>
            </select>
            <?php
              require 'lib/currency_codes.php';
              $currency_keys = array_keys($currency_codes);
              echo "<label class='form-label' for='currency_code'>Currency</label>";
              echo "<select class='form-select form-select-sm' aria-label='Select currency code' name='currency_code' id='currency_code'";
              foreach($currency_keys as $code){
                if($code == 'USD' ) {
                  echo "<option value='$code' selected />$code</option>";
                } else {
                  echo "<option value='$code'/>$code</option>";
                }
              }
              echo "</select>";

              $tz_arr =  DateTimeZone::listIdentifiers( DateTimeZone::ALL );
              echo "<label class='form-label' for='tz'>Time zone:</label>";
              echo "<select  class = 'form-select form-select-sm' id = 'tz' name = 'tz'>";
              foreach( $tz_arr as $tza ) {
                echo "<option value=" . $tza . ">" . $tza . "</option>";
                }
              
              echo "</select>";
              $tz_arr =  DateTimeZone::listIdentifiers( DateTimeZone::ALL );
            ?>
            <input class="btn btn-primary mt-2" type="submit" value="Install"/>
          
          </form>
        </div>
      </div>
    </div>
    <script src="lib/bootstrap.bundle.min.js"></script>
  </body>
</html>
