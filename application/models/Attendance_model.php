<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_model extends CI_Model
{ 
    public function __construct()
	{
		parent::__construct();
    }
    
    function create($data){
        if($this->db->insert('attendance', $data))
            return $this->db->insert_id();
        else
            return false; 
    }

    function my_att_running($user_id){
 
        $where = " WHERE user_id = ".$user_id;

        $where .= " AND saas_id = ".$this->session->userdata('saas_id');

        $where .= " AND check_out IS NULL ";

        $query = $this->db->query("SELECT * FROM attendance ".$where);
    
        $results = $query->result_array();  

        return $results;
    }

    function get_attendance_by_id($id){
 
        $where = " WHERE a.id = ".$id;
        if(!$this->ion_auth->is_admin()){
            $where .= " AND a.user_id = ".$this->session->userdata('user_id');
        }

        $where .= " AND a.saas_id = ".$this->session->userdata('saas_id');

		$LEFT_JOIN = " LEFT JOIN users u ON u.id=a.user_id ";

        $query = $this->db->query("SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as user FROM attendance a $LEFT_JOIN ".$where);
    
        $results = $query->result_array();  

        return $results;
    }
        
    function get_attendance(){
 
        $offset = 0;$limit = 10;
        $sort = 'a.id'; $order = 'ASC';
        $get = $this->input->get();
        if($this->ion_auth->is_admin()){
            if(isset($get['user_id']) && !empty($get['user_id'])){
                $where = " WHERE a.user_id = ".$get['user_id'];
            }else{
                $where = " WHERE a.id IS NOT NULL ";
            }
        }else{
            $where = " WHERE a.user_id = ".$this->session->userdata('user_id');
        }
        if(isset($get['sort']))
            $sort = strip_tags($get['sort']);
        if(isset($get['offset']))
            $offset = strip_tags($get['offset']);
        if(isset($get['limit']))
            $limit = strip_tags($get['limit']);
        if(isset($get['order']))
            $order = strip_tags($get['order']);
        if(isset($get['search']) &&  !empty($get['search'])){
            $search = strip_tags($get['search']);
            $where .= " AND (a.id like '%".$search."%' OR u.first_name like '%".$search."%' OR u.last_name like '%".$search."%' OR a.check_in like '%".$search."%' OR a.check_out like '%".$search."%' OR a.note like '%".$search."%')";
        }

        $where .= " AND a.saas_id = ".$this->session->userdata('saas_id');

        if(isset($get['from']) && !empty($get['from']) && isset($get['too']) && !empty($get['too'])){
            $where .= " AND DATE(a.created) BETWEEN '".format_date($get['from'],"Y-m-d")."' AND '".format_date($get['too'],"Y-m-d")."' ";
        }
    
		$LEFT_JOIN = " LEFT JOIN users u ON u.id=a.user_id ";

        $query = $this->db->query("SELECT COUNT('a.id') as total FROM attendance a $LEFT_JOIN ".$where);
    
        $res = $query->result_array();
        foreach($res as $row){
            $total = $row['total'];
        }
        
        $query = $this->db->query("SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as user FROM attendance a $LEFT_JOIN ".$where." ORDER BY ".$sort." ".$order." LIMIT ".$offset.", ".$limit);
    
        $results = $query->result_array();  
    
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($results as $result) {
				$tempRow = $result;
                if($result['check_in'] && $result['check_out']){
                    $datetime1 = new DateTime($result['check_in']);
                    $datetime2 = new DateTime($result['check_out']);
                    $interval = $datetime1->diff($datetime2);
                    $total_time = $interval->format('%d')>0?"<div><strong>".($this->lang->line('days')?htmlspecialchars($this->lang->line('days')):'Days').":</strong> ".$interval->format('%d')."</div>":"";
                    $total_time .= $interval->format('%h')>0?"<div><strong>".($this->lang->line('hours')?htmlspecialchars($this->lang->line('hours')):'Hours').":</strong> ".$interval->format('%h')."</div>":"";
                    $total_time .= $interval->format('%i')>0?"<div><strong>".($this->lang->line('minutes')?htmlspecialchars($this->lang->line('minutes')):'Minutes').":</strong> ".$interval->format('%i')."</div>":"";
                    $tempRow['total_time'] = $total_time==""?"<strong>".($this->lang->line('hours')?htmlspecialchars($this->lang->line('hours')):'Hours').":</strong> 0":$total_time;
                }else{
                    $tempRow['total_time'] = '<div class="text-danger">'.($this->lang->line('running')?htmlspecialchars($this->lang->line('running')):'Running').'</div>';
                }

                $tempRow['check_in'] = format_date($result['check_in'],system_date_format()." ".system_time_format());

                $tempRow['check_out'] = format_date($result['check_out'],system_date_format()." ".system_time_format());
                
                if($this->ion_auth->is_admin()){
                    $tempRow['action'] = '<span class="d-flex"><a href="#" class="btn btn-icon btn-sm btn-primary mr-1 modal-edit-attendance" data-edit="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('edit')?htmlspecialchars($this->lang->line('edit')):'Edit').'"><i class="fas fa-pen"></i></a><a href="#" class="btn btn-icon btn-sm btn-danger mr-1 delete_attendance" data-id="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('delete')?htmlspecialchars($this->lang->line('delete')):'Delete').'"><i class="fas fa-trash"></i></a></span>';
                }else{

                    $tempRow['action'] = '<span class="d-flex"><a href="#" class="btn btn-icon btn-sm btn-primary mr-1 modal-edit-attendance" data-edit="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('edit')?htmlspecialchars($this->lang->line('edit')):'Edit').'"><i class="fas fa-pen"></i></a></span>';
                }
                

                $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function edit($data, $id = '', $user_id = '', $null = ''){
        if(!empty($id)){
            $this->db->where('id', $id);
        }
        if(!empty($user_id)){
            $this->db->where('user_id', $user_id);
        }
        if(!empty($null)){
            $this->db->where('check_out IS NULL');
        }
        $this->db->where('saas_id', $this->session->userdata('saas_id'));
        if($this->db->update('attendance', $data))
            return true;
        else
            return false;
    }

    function delete($id = '', $user_id = ''){
        if($id){
            $this->db->where('id', $id);
        }

        if($user_id){
            $this->db->where('user_id', $user_id);
        }

        if($id = '' && $user_id = ''){
            return false;
        }

        $this->db->where('saas_id', $this->session->userdata('saas_id'));
        if($this->db->delete('attendance'))
            return true;
        else
            return false;
    }

}