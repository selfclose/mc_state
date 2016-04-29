<?php

class stats_player extends stats_settings {
    public $counter;
    public $player;
    public $playtime;
    public $arrows;
    public $xpgained;
    public $joins;
    public $fishcatch;
    public $damagetaken;
    public $timeskicked;
    public $toolsbroken;
    public $eggsthrown;
    public $itemscrafted;
    public $omnomnom;
    public $onfire;
    public $wordssaid;
    public $commandsdone;
    public $lastjoin;
    public $lastleave;

    function __construct($player){
        parent::__construct();
        var_dump('player: '.$player);
        //ทำให้ได้คนมา
        $res = mysqli_query($this->mysqli, 'SELECT * FROM '.$this->prefix.'player WHERE player_id = "'.mysqli_real_escape_string($this->mysqli, $player).'"');

        if(mysqli_num_rows($res) < 1){
            echo 'Error! No user with given name "'.$player.'"!';
            return NULL;
        } else {
            $row = mysqli_fetch_assoc($res);
            $this->counter = $row['counter'];
            $this->player = $row['player'];
            $this->playtime = $this->convert_playtime($row['playtime']);
            $this->arrows = $row['arrows'];
            $this->xpgained = $row['xpgained'];
            $this->joins = $row['joins'];
            $this->fishcatch = $row['fishcatch'];
            $this->damagetaken = $row['damagetaken'];
            $this->timeskicked = $row['timeskicked'];
            $this->toolsbroken = $row['toolsbroken'];
            $this->eggsthrown = $row['eggsthrown'];
            $this->itemscrafted = $row['itemscrafted'];
            $this->omnomnom = $row['omnomnom'];
            $this->onfire = $row['onfire'];
            $this->wordssaid = $row['wordssaid'];
            $this->commandsdone = $row['commandsdone'];
            $this->lastjoin = $row['lastjoin'];
            $this->lastleave = $row['lastleave'];
            #new ones
            $this->votes = $row['votes'];
            $this->teleports = $row['teleports'];
            $this->itempickups = $row['itempickups'];
            $this->bedenter = $row['bedenter'];
            $this->bucketfill = $row['bucketfill'];
            $this->bucketempty = $row['bucketempty'];
            $this->worldchange = $row['worldchange'];
            $this->itemdrops = $row['itemdrops'];
            $this->shear = $row['shear'];
        }
    }

    public static function convert_playtime($pt){
        $days = floor($pt / 86400);
        $hours = floor(($pt - $days*86400) / 3600);
        $mins = floor(($pt - $hours*3600 - $days*86400) / 60);
        $secs = floor($pt - $hours*3600 - $days*86400 - $mins*60);
        return $days.'d:'.$hours.'h:'.$mins.'m:'.$secs.'s';
    }

    // moving
    public function get_movement($type = 0){
        var_dump($type);
        if($type > 3 || $type < 0){
            return "Error! No movement of this type exists.";
        } else {
            $res = mysqli_query($this->mysqli, 'SELECT distance FROM '.$this->prefix.'move WHERE player_id = "'.$this->player.'" AND type = "'.$type.'"');
            $row = mysqli_fetch_assoc($res);

            if(mysqli_num_rows($res) < 1){
                return 0;
            } else {
                return $row['distance'];
            }
        }
    }

    public function get_total_movement(){
        $res = mysqli_query($this->mysqli, 'SELECT SUM(distance) as dis FROM '.$this->prefix.'move WHERE player_id = "'.$this->player.'"');
        $row = mysqli_fetch_assoc($res);

        if(mysqli_num_rows($res) < 1){
            return 0;
        } else {
            return $row['dis'];
        }
    }

    // deaths
    public function get_deaths($cause = NULL){
        var_dump('death');
        if(empty($cause)){
            $res = mysqli_query($this->mysqli, 'SELECT SUM(amount) as amn FROM '.$this->prefix.'death WHERE player_id = "'.$this->player.'"');
            //$res = mysqli_query($this->mysqli, 'SELECT SUM(amount) as amn FROM '.$this->prefix.'death WHERE player = "'.$this->player.'"');
        } else {
            $res = mysqli_query($this->mysqli, 'SELECT SUM(amount) as amn FROM '.$this->prefix.'death WHERE player_id = "'.$this->player.'" and cause = "'.$cause.'"');
        }

        $row = mysqli_fetch_assoc($res);
        //prevent a "return NULL"
        if($row['amn'] > 0){
            return $row['amn'];
        } else {
            return 0;
        }
    }

    public function get_all_deaths($top = NULL){
        if(empty($top)){
            $res = mysqli_query($this->mysqli, 'SELECT cause, amount FROM '.$this->prefix.'death WHERE player_id = "'.$this->player.'"');
        } else {
            $res = mysqli_query($this->mysqli, 'SELECT cause, amount FROM '.$this->prefix.'death WHERE player_id = "'.$this->player.'" ORDER BY amount desc LIMIT 1');
        }
        
        if(mysqli_num_rows($res) <= 0){
            return array(array('NoDeaths', 1));
        }

        $return_arr = array();

        while($row = mysqli_fetch_assoc($res)){
                $return_arr[] = array($row['cause'], $row['amount']);
        }

        return $return_arr;
    }


    // kills
    public function get_kills($type = NULL){
        if(empty($type)){
            $res = mysqli_query($this->mysqli, 'SELECT SUM(amount) as amn FROM '.$this->prefix.'kill WHERE player_id = "'.$this->player.'"');
        } else {
            $res = mysqli_query($this->mysqli, 'SELECT SUM(amount) as amn FROM '.$this->prefix.'kill WHERE player_id = "'.$this->player.'" AND type = "'.$type.'"');
        }

        $row = mysqli_fetch_assoc($res);
        //prevent a "return NULL"
        if($row['amn'] > 0){
            return $row['amn'];
        } else {
            return 0;
        }
    }

    public function get_all_kills($top = NULL){
        if(empty($top)){
            $res = mysqli_query($this->mysqli, 'SELECT type, amount FROM '.$this->prefix.'kill WHERE player_id = "'.$this->player.'"');
        } else {
            $res = mysqli_query($this->mysqli, 'SELECT type, amount FROM '.$this->prefix.'kill WHERE player_id = "'.$this->player.'" ORDER BY amount desc LIMIT 1');
        }

        if(mysqli_num_rows($res) <= 0){
            return array(array('NoKills', 1));
        }

        $return_arr = array();

        while($row = mysqli_fetch_assoc($res)){
                $return_arr[] = array($row['type'], $row['amount']);
        }

        return $return_arr;
    }

    //blocks
    public function get_all_blocks($res_type = NULL){
        $res = mysqli_query($this->mysqli, 'SELECT sbo.blockID, q1.amn, q2.brk FROM (SELECT blockID FROM '.$this->prefix.'block WHERE player_id = "'.$this->player.'" GROUP BY blockID ORDER BY blockID asc) as sbo LEFT JOIN (SELECT blockID, SUM(amount) as amn FROM '.$this->prefix.'block WHERE player = "'.$this->player.'" AND break = 0 GROUP BY blockID ORDER BY blockID asc) as q1 ON sbo.blockID = q1.blockID LEFT JOIN (SELECT blockID, SUM(amount) as brk FROM '.$this->prefix.'block WHERE player = "'.$this->player.'" AND break = 1 GROUP BY blockID ORDER BY blockID asc) as q2 ON sbo.blockID = q2.blockID');

        if($res_type == 'mysql'){
            return $res;
        } else {
            $return_arr = array();

            while($row = mysqli_fetch_assoc($res)){
                    $return_arr[] = array($row['blockID'], $row['amn'], $row['brk']);
            }

            return $return_arr;
        }
        
    }
    
}
?>
