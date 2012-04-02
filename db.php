<?php
	class Domain{
	}

	class DB{
		
		public $user="YOURS";
		public $password="YOURS";
		public $database="YOURS";
		
		private function getColumnNames($result){
			$names = array();
			$i=0;
			while ($i < @mysql_num_fields($result)) {
    			$meta = mysql_fetch_field($result, $i);
    			array_push($names,$meta->name);
    			$i++;
    		}
    		return $names;
		}
		
		private function bind_query_to_domain($query,$domain){
			$result = mysql_query($query);
			
			$names = $this->getColumnNames($result);
			
			$list = array();
			while($row = mysql_fetch_array($result)){
			
				$obj = new Domain();
				$obj->type = $domain;
				for($i=0; $i<count($names); $i++){
					$obj->$names[$i] = $row[$names[$i]];
				}
				array_push($list, $obj);
			}
			
			return $list;
		}
		
		public function insertDomain($object){

			$col_names = "id";
			$col_values = "NULL";
			
			foreach ($object as $name => $value) {
    			$col_names = "$col_names, $name";
    			$col_values = "$col_values, '$value'";
			}
				
			$query = "insert into ".$object->type." ($col_names)values($col_values)";//echo $query;
			mysql_query($query);
		}
				
		
		public function deleteDomain($object){
			$query = "delete  from ".$object->type." where id = ".$object->id;
			mysql_query($query);
		}
		
		public function getDomain($domain, $id){
			$query = "select * from ".$domain." where id = $id";
			$result = mysql_query($query);
			$names = $this->getColumnNames($result);
			$row = @mysql_fetch_array($result);
			
			$obj = new Domain();
			
			$obj->type = $domain;
			for($i=0; $i<count($names); $i++){
				$obj->$names[$i] = $row[$names[$i]];
			}
			return $obj;
		}
				
		public function randomListDomain($domain, $amount,$filter=""){
			$query = "select * from ".$domain." ".$filter." order by rand() limit $amount";
			return $this->bind_query_to_domain($query,$domain);
		}
		
		public function filterListDomain($domain, $filter, $limit){
			
			$query = "select * from ".$domain." ".$filter." order by id desc".($limit? " limit $limit" : "");
			return $this->bind_query_to_domain($query,$domain);
		}
		
		public function listDomain($domain, $page){
			return $this->filterListDomain($domain, "where parent = 0",($page*3).",3");
		}
		
		public function setup(){
			@mysql_connect(localhost,$this->user,$this->password);
			@mysql_select_db($this->database) or die( "db con err");
		}
		
		public function fin(){
			mysql_close();
		}
	}
?>
