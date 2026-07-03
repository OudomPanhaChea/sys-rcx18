<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaves_model extends CI_Model
{ 
    public function __construct()
	{
		parent::__construct();
    }
    
    function delete($user_id, $id){
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->where('saas_id', $this->session->userdata('saas_id'));
        if($this->db->delete('leaves'))
            return true;
        else
            return false;
    }

    function get_leaves_by_id($id){
        $where = "";
        if($this->ion_auth->is_admin()){
            $where .= " WHERE id = $id ";
        }else{
            $where .= " WHERE user_id = ".$this->session->userdata('user_id');
            $where .= " AND id = $id ";
        }

        $where .= " AND saas_id = ".$this->session->userdata('saas_id');

        $query = $this->db->query("SELECT * FROM leaves ".$where);
    
        $results = $query->result_array();  

        return $results;
    }

    function get_leaves(){
 
        $offset = 0;$limit = 10;
        $sort = 'l.id'; $order = 'ASC';
        $get = $this->input->get();
        $where = '';
        if($this->ion_auth->is_admin()){
            if(isset($get['user_id']) && !empty($get['user_id'])){
                $where .= " WHERE l.user_id = ".$get['user_id'];
            }else{
                $where .= " WHERE l.id != '' ";
            }
        }else{
            $where .= " WHERE l.user_id = ".$this->session->userdata('user_id');
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
            $where .= " AND (l.id like '%".$search."%' OR u.first_name like '%".$search."%' OR u.last_name like '%".$search."%' OR l.starting_date like '%".$search."%' OR l.ending_date like '%".$search."%' OR l.leave_days like '%".$search."%' OR l.leave_reason like '%".$search."%' OR l.status like '%".$search."%')";
        }

        $where .= " AND l.saas_id = ".$this->session->userdata('saas_id');
        
		$LEFT_JOIN = " LEFT JOIN users u ON u.id=l.user_id ";

        $query = $this->db->query("SELECT COUNT('l.id') as total FROM leaves l $LEFT_JOIN ".$where);
    
        $res = $query->result_array();
        foreach($res as $row){
            $total = $row['total'];
        }
        
        $query = $this->db->query("SELECT l.*, CONCAT(u.first_name, ' ', u.last_name) as user FROM leaves l $LEFT_JOIN ".$where." ORDER BY ".$sort." ".$order." LIMIT ".$offset.", ".$limit);
    
        $results = $query->result_array();  
    
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($results as $result) {
				$tempRow = $result;

                if($result['status'] == 0){
                    $tempRow['status'] = '<div class="badge badge-info">'.($this->lang->line('pending')?htmlspecialchars($this->lang->line('pending')):'Pending').'</div>';
                }elseif($result['status'] == 1){
                    $tempRow['status'] = '<div class="badge badge-success">'.($this->lang->line('approved')?htmlspecialchars($this->lang->line('approved')):'Approved').'</div>';
                }else{
                    $tempRow['status'] = '<div class="badge badge-danger">'.($this->lang->line('rejected')?htmlspecialchars($this->lang->line('rejected')):'Rejected').'</div>';
                }

                $tempRow['starting_date'] = format_date($result['starting_date'],system_date_format());
                $tempRow['ending_date'] = format_date($result['ending_date'],system_date_format());
                if($this->ion_auth->is_admin()){
                    $tempRow['action'] = '<span class="d-flex"><a href="#" class="btn btn-icon btn-sm btn-primary mr-1 modal-edit-leaves" data-edit="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('edit')?htmlspecialchars($this->lang->line('edit')):'Edit').'"><i class="fas fa-pen"></i></a><a href="#" class="btn btn-icon btn-sm btn-danger mr-1 delete_leaves" data-id="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('delete')?htmlspecialchars($this->lang->line('delete')):'Delete').'"><i class="fas fa-trash"></i></a></span>';
                }else{
                    if($result['status'] == 0){
                        $tempRow['action'] = '<span class="d-flex"><a href="#" class="btn btn-icon btn-sm btn-primary mr-1 modal-edit-leaves" data-edit="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('edit')?htmlspecialchars($this->lang->line('edit')):'Edit').'"><i class="fas fa-pen"></i></a><a href="#" class="btn btn-icon btn-sm btn-danger mr-1 delete_leaves" data-id="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('delete')?htmlspecialchars($this->lang->line('delete')):'Delete').'"><i class="fas fa-trash"></i></a></span>';
                    }else{
                        $tempRow['action'] = '<span class="d-flex"><a href="#" class="btn btn-icon btn-sm btn-primary mr-1 disabled" data-toggle="tooltip" title="'.($this->lang->line('edit')?htmlspecialchars($this->lang->line('edit')):'Edit').'"><i class="fas fa-pen"></i></a><a href="#" class="btn btn-icon btn-sm btn-danger mr-1 disabled" data-toggle="tooltip" title="'.($this->lang->line('delete')?htmlspecialchars($this->lang->line('delete')):'Delete').'"><i class="fas fa-trash"></i></a></span>';

                    }
                }
                
                $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function create($data){
        if($this->db->insert('leaves', $data))
            return $this->db->insert_id();
        else
            return false; 
    }

    function edit($id, $data){
        $this->db->where('id', $id);
        $this->db->where('saas_id', $this->session->userdata('saas_id'));
        if($this->db->update('leaves', $data))
            return true;
        else
            return false;
    }

}