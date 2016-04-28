<?php
abstract class stats_settings {
    public $prefix;
    public $mysqli;

    function __construct(){
        if(!(@include __dir__.'/../config.php')){
            exit('config.php could not be loaded! Check if you inserted all necessary data in the config_demo.php and renamed it to config.php!');
        }
        
        $this->prefix = $prefix;

        //connect to mysql, select correct db
        $this->mysqli = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);

        //set charset of tables (sadly not utf-8)
        if (!mysqli_set_charset($this->mysqli, $mysql_encoding)){
            printf('Error loading character set %s: %s<br/>mysqli_real_escape_string() might not work proper.', $mysql_encoding, $this->mysqli->error);
        }
        
        //no connection? End everything!
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
    }
}

?>

