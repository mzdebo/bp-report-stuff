<?php
namespace bp_report_stuff;

/**
 * Class report-model handles data, permission checks, and other I/O for reports
 *
 * @author lordmatt
 * @todo Once table design is finalised, gets, sets, updates, and drops
 * 
 * This file is part of BP Report Stuff.
 * 
 * BP Report Stuff is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU General Public License as published by the Free Software 
 * Foundation, either version 2 of the License, or (at your option) any later 
 * version.
 * 
 * BP Report Stuff is distributed in the hope that it will be useful, but WITHOUT ANY 
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with 
 * datastore. If not, see http://www.gnu.org/licenses/.
 */
class report_model {
	
	# method params TBD
	public function new_report($data){
		$id = 0; // this indicates a fail state
		
		// insert row
		// get row id
		
		return $id;
	}
	
	public function add_comment_to_report($report_id,$comment,$user=NULL,$action=NULL){
		$id = 0; // this indicates a fail state
		if($user===NULL){
			$user = $this->get_current_user_id();
		}
		// insert row
		// get row id
		
		return $id;
	}
	
	public function get_available_actions($report_id){
		$temp_list = array('pending','warned','closed-not-offence','account-closed','close-other');
		return $temp_list;
	}
	
	public function get_comments($report_id){
		// SELECT * FROM comment_table WHERE report_id=$report_id;
	}
	
	public function get_raw_report($report_id){
		
	}
	/**
	 * Get a report object for the given ID with all comments.
	 * 
	 * @param type $report_id
	 * @return boolean|\bp_report_stuff\report
	 */
	public function get_report_and_comments($report_id){
		$report = $this->get_raw_report($report_id);
		$comments = $this->get_comments($report_id);
		if(!is_array($report)){
			return FALSE;
		}
		if(!is_array($comments)){
			$comments=array();
		}
		$report = new report($report,$comments,$this);
		return $report;
	}
	
	/**
	 * Get an array of reports made by a given user
	 * 
	 * @todo the actual get from db
	 * 
	 * @param int $user_id
	 * @return boolean|array
	 */
	public function get_reports_by_user($user_id){
		if(!$this->user_can_view_users_reports($this->get_current_user_id(),$user_id)){
			return FALSE;
		}
		// return list of user's reports (pos devided into open and closed)
		$reports = array();
		
		
		return $reports;
	}
	
	/**
	 * Records moderator warnings for the user in the user meta
	 * 
	 * @param int $user_id
	 * @param string $message
	 */
	public function record_user_warning($user_id,$message){
		$meta = get_user_meta($user_id, BP_REPORT_STUFF_META_WARNINGS_KEY, TRUE);
		if( !is_array( $meta )){
			$meta = array();
		}
		$warning = array();
		$warning['date']	= date('Y-m-d H:i:s');
		$warning['mod_id']	= $this->get_current_user_id();
		$warning['message']	= $message;
		$warning = apply_filters( 'bp_report_stuff_user_meta_warning', $warning );
		$meta[]				= $warning;
		update_user_meta( $user_id, BP_REPORT_STUFF_META_WARNINGS_KEY, $meta);
	}
	
	// helpers
	protected function get_current_user_id(){
		return \get_current_user_id(); // Directly map to the WP function
	}
	
	
	
	// permissions
	/**
	 * Enquire if the named action is available to a given user.
	 * 
	 * @param string $action
	 * @param null|int $user
	 * @param null|int $report_id
	 * @return boolean
	 */
	public function user_can_($action, $user=NULL, $report_id=NULL){
		if($user===NULL){
			$user = $this->get_current_user_id();
		}
		$method_name = "user_can_{$action}";
		if(  method_exists( $this, $method_name )){
			if($report_id!=NULL){
				return call_user_method($method_name, $this, $user, $report_id);
			}else{
				return call_user_method($method_name, $this, $user);
			}
		}else{
			// There's no native handler for this maybe it is a WP cap?
			$boo = FALSE;
			try {
				$boo = @current_user_can( $action );
				if( is_bool( $boo )){
					return $boo;
				}
				return FALSE;
			} catch (Exception $e) {
				// wow that went badly
				return FALSE;
			}
		}
		return FALSE; // should never be reached...
	}
	
	public function user_can_view_report($user, $report_id){
		//
	}	
	public function user_can_view_users_reports($user,$other_user){
		//
	}		
	public function user_can_edit_report($user, $report_id){
		//
	}	
	
	public function user_can_report($user){
		
	}
	
}
