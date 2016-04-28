<?php
//uncomment to debug
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//const $table[']

include_once('state_settings.php');
include_once('state_player.php');
include_once('state_global.php');

class bonus_methods
{
    public $page_title;
    public $header_title;
    public $top_limit;

    public $map_link;
    public $tmotd;
    public $tmotd_headline;
    public $custom_links;
    public $enable_server_page;
    public $show_avatars;
    public $show_online_state;

    private $server_port;
    private $server_ip;
    public $max_players;
    public $online_players;

    function __construct(){
        //include __dir__.'/../config_bonus.php';
        include __dir__.'/../config.php';

        //$this->tmotd = $motd;
        //$this->tmotd_headline = $motd_headline;

        $this->page_title = empty($page_title) ? 'Minecraft WEBStatsX' : $page_title;
        $this->header_title = empty($header_title) ? 'WEBStatsX' : $header_title;
        $this->top_limit = empty($top_limit) || !is_int($top_limit) ? 10 : $top_limit;

        if(empty($link_to_map) || $link_to_map == ''){
            $this->map_link = '#';
        } else {
            $this->map_link = $link_to_map;
        }

        if($show_avatars == false){
            $this->show_avatars = 0;
        } else {
            $this->show_avatars = 1;
        }

        if($show_online_state == false){
            $this->show_online_state = 0;
        } else {
            $this->show_online_state = 1;
        }
        
        $this->custom_links = $custom_links;
        $this->server_ip = $server_ip;
        $this->server_port = $server_port;
        $this->enable_server_page = $enable_server_page;
    }

    public function get_custom_links(){
        $links = $this->custom_links;
        $prepared_links = "";
        foreach ($links as $name => $url) {
            $prepared_links .= "<li><a href='". $url ."'><i class='icon-arrow-left'></i><span class='hidden-tablet'> ". $name ."</span></a></li>" ;
        }
        return $prepared_links;
    }

    public function check_server(){
        if(empty($this->server_ip)){
            return false;
        }

        if ($sock = stream_socket_client('tcp://'.$this->server_ip.':'.$this->server_port, $errno, $errstr, 1)){

            fwrite($sock, "\xfe\x01");
            $data = fread($sock, 1024);
            fclose($sock);

            if($data == false AND substr($data, 0, 1) != "\xFF"){
                return false;
            }

            $data = substr($data, 9);
            $data = mb_convert_encoding($data, 'auto', 'UCS-2');
            $data = explode("\x00", $data);
            
            $this->online_players = (int) $data[3];
            $this->max_players = (int) $data[4];
            return true;

        } else {
            return false;
        }
    }

    public function get_map(){
        if($this->map_link == '#'){
            return '<div class="alert alert-info"><strong>No map!</strong> There is no link to a map in the config, so I can\'t include one. :(</div>';
        } else {
            return '<div id="map_frame"><iframe seamless="seamless" style="width:100%;height:100%;" src="'.$this->map_link.'"></iframe></div>';
        }
    }

    public function get_motd(){
        if(!empty($this->tmotd)){
            return '<div class="row-fluid"><div class="box span12"><div class="box-header well"><h2><i class="icon-info-sign"></i> Motd</h2><div class="box-icon"><a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a><a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a></div></div><div class="box-content"><h1>'.$this->tmotd_headline.'</h1>'.$this->tmotd.'<div class="clearfix"></div></div></div></div>';
        }
    }
}
?>
