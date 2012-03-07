<?

class Facebook_album{


	var $user_table = 'upd8r_user_albums';
	var $CI;
	var $access_token;

	var $facebook;
	
	

	public function __construct($facebook,$access_token) {
	

		$this->facebook = $facebook;
		$this->facebook->setAccessToken($access_token);
		

	}
	
	/*
	*
	* returns facebook aid from the admin_album_id of the station
	*
	* @param string $admin_album_id 
	* @param string $user_id id of the user 
	* @param string $data
	*
	* @return string $album_id
	*/
	
	public function check_user_has_album($admin_album_id,$user_id,$data = array()){
		
		$result = $this->__get_album_id($admin_album_id,$user_id);
		
		if(!$result){			
			$album_result = $this->__create_album($data['album_name'],$data['album_description']);
			
			$album_id = $this->__store_album_id(array('facebook_album_id' => $album_result['id'],'user_id' => $user_id, 'admin_album_id' => $admin_album_id));
			return $album_result['id'];
		}
		else
		{
			return $result;
		}		
	}
	
	
	
	
	/**
	*
	* does the user have the album created?
	*
	* @param string $station_id 
	* @param string $user_id 
	*
	* @return mixed- returns id of album if created-returns false if not
	*/
	
	private function __get_album_id($admin_album_id,$user_id)
	{
	
		$this->CI =& get_instance();
		
		$result = $this->CI->db->get_where($this->user_table,array('user_id'=>$user_id,'admin_album_id'=>$admin_album_id));
		
		if($result->num_rows() > 0){
			$result = $result->row_array();
			return $result['facebook_album_id'];
		}
		return false;
	}
	
	
	/**
	 *
	 * Create album
	 *
	 * @param String $album_name 
	 * @return mixed The decoded response
	 */
	
	private function __create_album($album_name,$album_description){
	
		
		$this->facebook->setFileUploadSupport(true);
  
		//Create an album
		$album_details = array(
				'name'=> $album_name,
				'message'=> $album_description
		);

		return $this->facebook->api('/me/albums', 'post', $album_details);
	}
	
	/*
	* stores album id in the db
	*
	* @param String $album_id 
	* @return bool
	*/
	
	private function __store_album_id($data)
	{
		$this->CI =& get_instance();
		$data['dated'] = date('Y-m-d H:i:s');
		return $this->CI->db->insert($this->user_table, $data);
	}
	
	
	// ## check if user has album already created for likestation
	
	//if user has not- create album and return id
	// insert album id and like station id into the db somewhere
	
	
	
	

}

?>