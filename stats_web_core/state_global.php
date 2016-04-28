<?php
class stats_global extends stats_settings {
	private $gp_res = false;

	public function get_players($by = NULL, $order = NULL, $limit = NULL){
		var_dump('get player' . $order);
		if($this->gp_res === false){
			if(empty($order)){
				$s = 'ORDER BY player_id ';
			} else {
				$s = 'ORDER BY '.mysqli_real_escape_string($this->mysqli, $by).' ';
			}

			if(!empty($order)){
				$s .= mysqli_real_escape_string($this->mysqli, $order);
			} else {
				$s .= 'asc';
			}

			if(!empty($limit)){
				$s .= ' LIMIT '.mysqli_real_escape_string($this->mysqli, $limit);
			}

			$this->gp_res = mysqli_query($this->mysqli, 'SELECT player_id FROM '.$this->prefix.'player '.$s);
		}

		if($row = mysqli_fetch_assoc($this->gp_res)){
		   	return new stats_player($row['player_id']);
		} else {
			$this->gp_res = false;
			return false;
		}
	}

	public function count_players(){
		$resource = mysqli_query($this->mysqli, 'SELECT COUNT(counter) as c FROM '.$this->prefix.'player');
		$count = mysqli_fetch_assoc($resource);
		return $count['c'];
	}

	public function get_total_distance_moved(){
		$res = mysqli_query($this->mysqli, 'SELECT SUM(distance) as dis FROM '.$this->prefix.'move');
		$row = mysqli_fetch_assoc($res);
		return $row['dis'];
	}

	public function get_total_kills(){
		$res = mysqli_query($this->mysqli, 'SELECT SUM(amount) as amn FROM '.$this->prefix.'kill');
		$row = mysqli_fetch_assoc($res);
		return $row['amn'];
	}

	public function get_total_deaths(){
		var_dump('total D');
		$res = mysqli_query($this->mysqli, 'SELECT SUM(amount) as amn FROM '.$this->prefix.'death');
		$row = mysqli_fetch_assoc($res);
		return $row['amn'];
	}

	public function get_kill_average(){
		$res = mysqli_query($this->mysqli, 'SELECT AVG(sk1.sumam) as amn FROM (SELECT SUM(amount) as sumam FROM '.$this->prefix.'kill GROUP BY player) as sk1');

		$row = mysqli_fetch_assoc($res);
		return $row['amn'];
	}

	public function get_death_average(){
		$res = mysqli_query($this->mysqli, 'SELECT AVG(sk1.sumam) as amn FROM (SELECT SUM(amount) as sumam FROM '.$this->prefix.'death GROUP BY player) as sk1');

		$row = mysqli_fetch_assoc($res);
		return $row['amn'];
	}


	// =============== top functions ===============//
	public function get_toplist_table($table, $sumField, $pageLimit = null)
	{
		$sql = 'SELECT name, SUM('.$sumField.') as amn FROM '.$this->prefix . $table . ', '.$this->prefix.'players as player WHERE ' . $this->prefix . $table . '.player_id = player.player_id GROUP BY name ORDER BY amn desc';
		if ($pageLimit) {
			$sql .= ' LIMIT ' . $pageLimit;
		}
		return $sql;
	}

	public function get_top_players_move($res_type = NULL, $limit = NULL){
		if(empty($limit) || !is_integer($limit)){
			$res = mysqli_query($this->mysqli, $this->get_toplist_table('move', 'distance'));
		} else {
			$res = mysqli_query($this->mysqli, $this->get_toplist_table('move', 'distance', $limit));
			
		}

		if($res_type == 'mysql'){
			return $res;
		} else {
			$return_arr = array();

			while($row = mysqli_fetch_assoc($res)){
					$return_arr[] = array($row['player'], $row['dis']);
			}

			return $return_arr;
		}
	}

	//ดึงคนตายมากที่สุด
	public function get_top_players_kill($res_type = NULL, $limit = NULL){
		if(empty($limit) || !is_integer($limit)){
			//$res = mysqli_query($this->mysqli, 'SELECT player, SUM(amount) as amn FROM '.$this->prefix.'kill GROUP BY player ORDER BY amn desc');
			$res = mysqli_query($this->mysqli, $this->get_toplist_table('kill', 'amount'));
		} else {
			$res = mysqli_query($this->mysqli, $this->get_toplist_table('kill', 'amount', $limit));
		}

		if($res_type == 'mysql'){
			return $res;
		} else {
			$return_arr = array();

			while($row = mysqli_fetch_assoc($res)){
					$return_arr[] = array($row['player'], $row['amn']);
			}

			return $return_arr;
		}
	}

	public function get_top_players_death($res_type = NULL, $limit = NULL){
		if(empty($limit) || !is_integer($limit)){
			$res = mysqli_query($this->mysqli, $this->get_toplist_table('death', 'amount'));
		} else {
			$res = mysqli_query($this->mysqli, $this->get_toplist_table('death', 'amount', $limit));			
		}

		if($res_type == 'mysql'){
			return $res;
		} else {
			$return_arr = array();

			while($row = mysqli_fetch_assoc($res)){
					$return_arr[] = array($row['player'], $row['amn']);
			}

			return $return_arr;
		}
	}
	
	public function get_top_player_block($isBreak = '0', $pageLimit)
	{
		$sql = 'SELECT name, SUM(amount) as amn FROM '.$this->prefix.'block, '.$this->prefix.'players WHERE break = '.$isBreak.' GROUP BY name ORDER BY amn desc';
		if ($pageLimit) {
			$sql .= ' LIMIT ' . $pageLimit;
		}
		return $sql;
	}

	public function get_top_players_blocks_placed($res_type = NULL, $limit = NULL){
		if(empty($limit) || !is_integer($limit)){
			$res = mysqli_query($this->mysqli, $this->get_top_player_block('0'));
		} else {
			$res = mysqli_query($this->mysqli, $this->get_top_player_block('0', $limit));
		}

		if($res_type == 'mysql'){
			return $res;
		} else {
			$return_arr = array();

			while($row = mysqli_fetch_assoc($res)){
					$return_arr[] = array($row['player'], $row['amn']);
			}

			return $return_arr;
		}
	}

	public function get_top_players_blocks_broken($res_type = NULL, $limit = NULL){
		if(empty($limit) || !is_integer($limit)){
			$res = mysqli_query($this->mysqli, $this->get_top_player_block('1'));
		} else {
			$res = mysqli_query($this->mysqli, $this->get_top_player_block('1', $limit));
		}

		if($res_type == 'mysql'){
			return $res;
		} else {
			$return_arr = array();

			while($row = mysqli_fetch_assoc($res)){
					$return_arr[] = array($row['player'], $row['amn']);
			}

			return $return_arr;
		}
	}


	public function get_players_state($player_list){
		$query = '';
		$return_arr = array();

		foreach ($player_list as $player) {
			$query .= 'player = "'.$player.'" OR ';
		}
var_dump('fff');
		$res = mysqli_query($this->mysqli, 'SELECT player_id, lastjoin, lastleave FROM '.$this->prefix.'player WHERE '.substr($query, 0, -4));
		var_dump(substr($query, 0, -4));
		while($row = mysqli_fetch_assoc($res)){
			if(strtotime($row['lastjoin']) > strtotime($row['lastleave'])){
				$return_arr[$row['player_id']] = 1;
			} else {
				$return_arr[$row['player_id']] = 0;			
			}
		}

		return $return_arr;
	}
}

?>