<?php
// necessary to work
$mysql_host = 'localhost'; // your MySQL server IP
$mysql_user = 'root'; // your MySQL database user
$mysql_pass = 'root'; // your MySQL database password
$mysql_db = 'sims-craft_data'; // your MySQL database name
$mysql_encoding = 'utf8';  // this is recommended because the plugin creates all its tables with latin1 encoding
$prefix = 'stats_'; // plugin default is "Stats"


/**
 * Display-specific optional configs
 */
$page_title = 'Minecraft Pla2Craft';
$header_title = 'Pla2Craft';
$top_limit = 10;


/*########################
 optional stuff
########################*/
//--- show avatars
$show_avatars = true;

//--- show online/offline
$show_online_state = true;

//--- online/offline check
$server_ip = '127.0.0.1'; // can be a real ip or a dns alias
$server_port = 25565; // [default=25565]

//--- add a link to the online map
$link_to_map = "http://".$server_ip . ":8123";
// Don't want the link to be displayed? Uncomment the next line!
//$link_to_map = "";

//--- add custom links to the menu
$custom_links = array(
  //"Dynamap" => $link_to_map, //Uncommend this for Dynmap
  //"Mojang" => "http://mojang.com" //Example of how to add custom links!
);

//--- enable the server stats page [beta]
$enable_server_page = false; //BETA!! Needs some special rights your webserver might not have on the host system. This will not work on windows servers!

?>
