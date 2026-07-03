<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Broadcast_model extends CI_Model
{ 
    public function __construct()
	{
		parent::__construct();
    }


    function create($data){
        if($this->db->insert('broadcast', $data))
            return $this->db->insert_id();
        else
            return false; 
    }


    function get_broadcast(){
 
        $offset = 0;$limit = 10;
        $sort = 'a.id'; $order = 'ASC';
        $where = " WHERE a.from_id != ''";
        $get = $this->input->get();

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
            $where .= " AND (a.id like '%".$search."%' OR u.first_name like '%".$search."%' OR u.last_name like '%".$search."%' OR a.from_id like '%".$search."%' OR a.subject like '%".$search."%' OR a.created like '%".$search."%' OR a.to_whom like '%".$search."%' OR a.msg like '%".$search."%')";
        }
        if(isset($get['to_whom']) && !empty($get['to_whom'])){
            $search = strip_tags($get['to_whom']);
            $where .= " AND (a.to_whom like '%".$search."%')";
        }

        if(isset($get['from']) && !empty($get['from']) && isset($get['too']) && !empty($get['too'])){
            $where .= " AND DATE(a.created) BETWEEN '".format_date($get['from'],"Y-m-d")."' AND '".format_date($get['too'],"Y-m-d")."' ";
        }
    
		$LEFT_JOIN = " LEFT JOIN users u ON u.id=a.from_id ";

        $query = $this->db->query("SELECT COUNT('a.id') as total FROM broadcast a $LEFT_JOIN ".$where);
    
        $res = $query->result_array();
        foreach($res as $row){
            $total = $row['total'];
        }
        
        $query = $this->db->query("SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as from_user FROM broadcast a $LEFT_JOIN ".$where." ORDER BY ".$sort." ".$order." LIMIT ".$offset.", ".$limit);
    
        $results = $query->result_array();  
    
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($results as $result) {
				$tempRow = $result;

                $to_whom = json_decode($result['to_whom']);
                $to_whom_formated = array();
                foreach($to_whom as $key => $who){
                    if($who == 'all'){
                        $to_whom_formated[] = $this->lang->line('all')?htmlspecialchars($this->lang->line('all')):'All';
                    }elseif($who == 'saas_admins'){
                        $to_whom_formated[] = $this->lang->line('saas_admins')?htmlspecialchars($this->lang->line('saas_admins')):'SaaS Admins';
                    }elseif($who == 'subscribers'){
                        $to_whom_formated[] = $this->lang->line('subscribers')?htmlspecialchars($this->lang->line('subscribers')):'Subscribers';
                    }elseif($who == 'users'){
                        $to_whom_formated[] = $this->lang->line('users')?htmlspecialchars($this->lang->line('users')):'Users';
                    }else{
                        $to_whom_formated[] = $who;
                    }
                }
                
                $tempRow['to_whom'] = implode(', ', $to_whom_formated);

                $tempRow['created'] = format_date($result['created'],system_date_format()." ".system_time_format());
                
                $tempRow['action'] = '<span class="d-flex"><a href="#" class="btn btn-icon btn-sm btn-danger mr-1 delete_broadcast" data-id="'.$result['id'].'" data-toggle="tooltip" title="'.($this->lang->line('delete')?htmlspecialchars($this->lang->line('delete')):'Delete').'"><i class="fas fa-trash"></i></a></span>';

                $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }

    function delete($id = ''){
        if($id != ''){
            $this->db->where('id', $id);
        }else{
            return false;
        }

        if($this->db->delete('broadcast'))
            return true;
        else
            return false;
    }


}