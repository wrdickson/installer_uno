<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="lib/bootstrap-5.2.3.css" rel="stylesheet">
    <title>install_uno.php</title>
  </head>
  <body>
    
    <div class="container">
      <h1>Installer</h1>
      <div class="row">
        
          <?php
          require('../api/vendor/autoload.php');
          use \Brick\Money\Money;
          //TODO use brick/money (also get it in there with composer
          require('lib/init_sql.php');
          require('lib/Validate.php');
          
          echo "<p>Post variables: </p>";
          echo "<PRE>" . var_dump($_POST) . "</PRE>";

          //  TODO validate inputs!

          /**
           * test the the post data with try db connection
           */
          $db_user = $_POST['db_user'];
          $db_pass = $_POST['db_pass'];
          $db_name = $_POST['db_name'];
          $db_host = $_POST['db_host'];
          $admin_user = $_POST['admin_user'];
          $admin_user_email = $_POST['admin_user_email'];
          $admin_user_password_1 = $_POST['admin_user_password_1'];
          $admin_user_password_2 = $_POST['admin_user_password_2'];
          $jwt_key = $_POST['jwt_key'];
          $site_name = $_POST['site_name'];
          $default_locale = $_POST['default_locale'];
          $house_account_name = $_POST['house_account_name'];
          if(isset($_POST['currency_code'])){
            $cur_code = $_POST['currency_code'];
          } else {
            $cur_code = 'USD';
          }
          $time_zone = $_POST['tz'];

          $u1 = Money::of(1, $cur_code);
          $currency_code = $u1->getCurrency()->getCurrencyCode();
          $currency_fraction_digits = $u1->getCurrency()->getDefaultFractionDigits();
          $currency_numeric_code = $u1->getCurrency()->getNumericCode();
          $currency_name = $u1->getCurrency()->getName();
          echo "<hr>";
          echo "<p>Currency code: " . $currency_code . "</p>";
          echo "<p>Fraction digits: " . $currency_fraction_digits . "</p>";
          echo "<p>Numeric code: " . $currency_numeric_code . "</p>";
          echo "<p>Currency name: " . $currency_name . "</p>";
          echo "<hr>";

          echo "<p>Timezone:" . $time_zone . "</p>";
          echo "<hr>";


          try {
            $pdo = new PDO('mysql:host=' . $db_host .';dbname=' . $db_name, $db_user, $db_pass);
            //  see https://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php?rq=1
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo '<p>Connected to database.  Continue with installation . . . </p>';

            /**
             * if it passes, write the config file
             */
            echo "<p>Creating config directory . . .</p>";
            echo "<p>Writing config file . . .</p>";
            $f = fopen("../api/config/config.php", "w");
            fwrite( $f, "<?php\n");
            $s = "define('DB_HOST', '{$db_host}');\n";
            fwrite( $f, $s ); 
            $s ="define('DB_USER', '{$db_user}');\n";
            fwrite(  $f, $s);
            $s = "define('DB_PASS', '{$db_pass}');\n";
            fwrite( $f, $s );
            $s = "define('DB_NAME', '{$db_name}');\n";
            fwrite( $f, $s );
            $s = "define('SERVER_NAME', '$db_host');\n";
            fwrite( $f, $s);
            $s = "define('JWT_KEY', '{$jwt_key}');\n";
            fwrite( $f, $s );
            $s = "define('DEFAULT_LOCALE', '{$default_locale}');\n";
            fwrite( $f, $s );
            $s = "define('DEFAULT_TIMEZONE', '{$time_zone}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_CODE', '{$currency_code}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_FRACTION_DIGITS', '{$currency_fraction_digits}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_NUMERIC_CODE', '{$currency_numeric_code}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_NAME', '{$currency_name}');\n";
            fwrite( $f, $s );
            $s = "date_default_timezone_set(DEFAULT_TIMEZONE);\n";
            fwrite( $f, $s );


            //  run the query to dropthe accounts table
            $stmt = $pdo->prepare( $drop_accounts_sql );
            if( $execute = $stmt->execute() ) {
              echo "<p>Accounts table dropped . . .</p>";
            }

            //  run the query to build the accounts table
            $stmt = $pdo->prepare( $create_accounts_sql );
            if( $execute = $stmt->execute() ) {
              echo "<p>Accounts table created . . .</p>";
            }

            //  insert the admin user
            $password_hash = password_hash($admin_user_password_1, PASSWORD_DEFAULT);
            $sql = "INSERT INTO accounts (username, email, permission, roles, registered, last_activity, last_login, is_active, password) VALUES (:u, :e, '10', '[]', NOW(), NOW(), NOW(), '1', :pwd)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":u", $admin_user);
            $stmt->bindParam(":e", $admin_user_email);
            $stmt->bindParam(":pwd", $password_hash);
            if( $stmt->execute() ) {
              echo "<p>Admin account created . . .</p>";
            }

            /**
             * Create the rest of the tables
             */

            //  options
            $stmt = $pdo->prepare($drop_options_sql);
            if( $stmt->execute() ) {
              echo "<p>Options table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_options_sql);
            if( $stmt->execute() ) {
              echo "<p>Options table created . . .</p>";
            }

            //  TODO insert currency data into options

            // insert site_name into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'site_name';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $site_name);
            if( $stmt->execute() ) {
              echo "<p>Insert site_name into options . . .</p>";
            }

            // insert currency_code into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_code';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_code);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_code into options . . .</p>";
            }

            // insert currency_fraction_digits into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_fraction_digits';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_fraction_digits);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_fraction_digits into options . . .</p>";
            }

            // insert currency_numeric_code into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_numeric_code';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_numeric_code);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_code into options . . .</p>";
            }

            // insert currency_name into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_name';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_name);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_name into options . . .</p>";
            }

            //  insert default locale
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            $nnn = 'default_locale';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $default_locale);
            if( $stmt->execute() ) {
              echo"<p>Default locale inserted . . .</p>";
            }

            //  insert default timezone
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            $nnn = 'default_timezone';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $time_zone);
            if( $stmt->execute() ) {
              echo"<p>Default locale inserted . . .</p>";
            }


            //  customers
            $stmt = $pdo->prepare($drop_customers_sql);
            if( $stmt->execute() ) {
              echo "<p>Customers table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_customers_sql);
            if( $stmt->execute() ) {
              echo "<p>Customers table created . . .</p>";
            }

            //  insert house account customer
            $stmt = $pdo->prepare("INSERT INTO customers ( id, last_name, first_name ) VALUES ( 1, :ln, '' )");
            $stmt->bindParam(':ln', $house_account_name);
            if( $stmt->execute() ) {
              echo"<p>House account inserted into customers . . .</p>";
            }

            //  folios
            $stmt = $pdo->prepare($drop_folios_sql);
            if( $stmt->execute() ) {
              echo "<p>Folios table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_folios_sql);
            if( $stmt->execute() ) {
              echo "<p>Folios table created . . .</p>";
            }

            // create house account folio
            $stmt = $pdo->prepare("INSERT INTO folios ( id, customer ) VALUES ( 1, 1 )");
            if( $stmt->execute() ) {
              echo"<p>House account folio created . . .</p>";
            }

            //  create an option for house account folio
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( 'house_account_folio', 1, 1 )");
            if( $stmt->execute() ) {
              echo"<p>House folio option added . . .</p>";
            }

            //  payment_types
            $stmt = $pdo->prepare($drop_payment_types_sql);
            if( $stmt->execute() ) {
              echo "<p>Payment types table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_payment_types_sql);
            if( $stmt->execute() ) {
              echo "<p>Payment types table created . . .</p>";
            }

            //  payments
            $stmt = $pdo->prepare($drop_payments_sql);
            if( $stmt->execute() ) {
              echo "<p>Payments table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_payments_sql);
            if( $stmt->execute() ) {
              echo "<p>Payments table created . . .</p>";
            }

            //  reservations
            $stmt = $pdo->prepare($drop_reservations_sql);
            if( $stmt->execute() ) {
              echo "<p>Reservations table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_reservations_sql);
            if( $stmt->execute() ) {
              echo "<p>Reservations table created . . .</p>";
            }

            //  root_spaces
            $stmt = $pdo->prepare($drop_root_spaces_sql);
            if( $stmt->execute() ) {
              echo "<p>Root spaces table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_root_spaces_sql);
            if( $stmt->execute() ) {
              echo "<p>Root spaces table created . . .</p>";
            }
            /**
             * THERE IS ONE AND ONLY ONE ROOT SPACE WHERE is_unassigned = 1
             * but it is absolutely critical to the logic that we insert it here
             */
            $stmt=$pdo->prepare($insert_unassigned_space_sql);
            if( $stmt->execute() ) {
              echo "<p>Unassigned root space created</p>";
            }

            //  sale_items
            $stmt = $pdo->prepare($drop_sale_items_sql);
            if( $stmt->execute() ) {
              echo "<p>Sale items table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_sale_items_sql);
            if( $stmt->execute() ) {
              echo "<p>Sale items table created . . .</p>";
            }

            //  sale_type_groups
            $stmt = $pdo->prepare($drop_sale_type_groups_sql);
            if( $stmt->execute() ) {
              echo "<p>Sale type groups table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_sale_type_groups_sql);
            if( $stmt->execute() ) {
              echo "<p>Sale type groups table created . . .</p>";
            }

            //  sale_types
            $stmt = $pdo->prepare($drop_sale_types_sql);
            if( $stmt->execute() ) {
              echo"<p>Sale types table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_sale_types_sql);
            if( $stmt->execute() ) {
              echo("<p>Sale types table created . . .</p>");
            }

            //  space_types
            $stmt = $pdo->prepare($drop_space_types_sql);
            if( $stmt->execute() ) {
              echo "<p>Space types table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_space_types_sql);
            if( $stmt->execute() ) {
              echo "<p>Space types table created . . .</p>";
            }

            //  tax_types
            $stmt = $pdo->prepare($drop_tax_types_sql);
            if( $stmt->execute() ) {
              echo "<p>Tax types table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_tax_types_sql);
            if( $stmt->execute() ) {
              echo "<p>Tax types table created . . .</p>";
            }
            
          } catch ( PDOException $e ) {
              echo "Error creating tables: " . $e->getMessage() . "<br/>";
              die();
          } 
          ?>
      </div>
    </div>
    <style type="text/css">
      p {
        margin-bottom: 4px;
      }
    </style>

    <script src="lib/bootstrap.bundle.min.js"></script>
  </body>
</html>