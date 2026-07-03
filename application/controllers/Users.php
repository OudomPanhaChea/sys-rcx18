<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function get_team_list()
	{	
		if($this->ion_auth->logged_in()){
			
			$bulkData = array();

			$system_users = $this->ion_auth->users(array(1,2))->result();
			foreach ($system_users as $system_user) {
				if(isset($system_user->saas_id) && $this->session->userdata('saas_id') == $system_user->saas_id){
				$tempRow[] = $system_user;
				$tempRow['id'] = $system_user->user_id;
				$tempRow['email'] = $system_user->email;
				$tempRow['active'] = $system_user->active;
				$tempRow['first_name'] = $system_user->first_name.' '.$system_user->last_name;
				$tempRow['last_name'] = $system_user->last_name;
				
				$group = $this->ion_auth->get_users_groups($system_user->user_id)->result();
				if($group[0]->name == 'admin'){
					$tempRow['role'] = $this->lang->line('admin')?htmlspecialchars($this->lang->line('admin')):'Admin';
				}else{
					$tempRow['role'] = $this->lang->line('team_member')?htmlspecialchars($this->lang->line('team_member')):'Team Member';
				}
				$tempRow['group_id'] = $group[0]->id;
				$tempRow['projects_count'] = get_count('id','project_users','user_id='.$system_user->user_id);
				$tempRow['tasks_count'] = get_count('id','task_users','user_id='.$system_user->user_id);

				$tempRow['phone'] = $system_user->phone!=0?$system_user->phone:'';
				
				$tempRow['status'] = $system_user->active==1?('<span class="badge badge-success">'.($this->lang->line('active')?$this->lang->line('active'):'Active').'</span>'):('<span class="badge badge-danger">'.($this->lang->line('deactive')?$this->lang->line('deactive'):'Deactive').'</span>');

				$rows[] = $tempRow;
				}
			}

			$bulkData['rows'] = $rows;
			print_r(json_encode($bulkData));
		}else{
			return '';
		}
	}

	public function get_client_list()
	{	
		if($this->ion_auth->logged_in()){
			
			$bulkData = array();

			$system_users = $this->ion_auth->users(array(4))->result();
			foreach ($system_users as $system_user) {
				if(isset($system_user->saas_id) && $this->session->userdata('saas_id') == $system_user->saas_id){
				$tempRow[] = $system_user;
				$tempRow['id'] = $system_user->user_id;
				$tempRow['email'] = $system_user->email;
				$tempRow['active'] = $system_user->active;
				$tempRow['first_name'] = $system_user->first_name.' '.$system_user->last_name;
				$tempRow['last_name'] = $system_user->last_name;
				$tempRow['phone'] = $system_user->phone!=0?$system_user->phone:'';
				$tempRow['company'] = company_details('company_name', $system_user->user_id);
				$tempRow['projects_count'] = get_count('id','projects','client_id='.$system_user->user_id);
				$tempRow['status'] = $system_user->active==1?('<span class="badge badge-success">'.($this->lang->line('active')?$this->lang->line('active'):'Active').'</span>'):('<span class="badge badge-danger">'.($this->lang->line('deactive')?$this->lang->line('deactive'):'Deactive').'</span>');

				$rows[] = $tempRow;
				}
			}

			$bulkData['rows'] = $rows;
			print_r(json_encode($bulkData));
		}else{
			return '';
		}
	}

	public function client()
	{	
		if ($this->ion_auth->logged_in() && is_module_allowed('clients') && ($this->ion_auth->is_admin() || permissions('client_view')))
		{
			$this->data['page_title'] = 'Clients - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$system_users = $this->ion_auth->users(array(4))->result();
			foreach ($system_users as $system_user) {
				if(isset($system_user->saas_id) && $this->session->userdata('saas_id') == $system_user->saas_id){
				$tempRow['id'] = $system_user->user_id;
				$tempRow['email'] = $system_user->email;
				$tempRow['active'] = $system_user->active;
				$tempRow['first_name'] = $system_user->first_name;
				$tempRow['last_name'] = $system_user->last_name;
				$tempRow['phone'] = $system_user->phone!=0?$system_user->phone:'';
				$tempRow['company'] = company_details('company_name', $system_user->user_id);

				$tempRow['profile'] = '';
				if($system_user->profile){
					if(file_exists('assets/uploads/profiles/'.$system_user->profile)){
						$file_upload_path = 'assets/uploads/profiles/'.$system_user->profile;
					  }else{
						$file_upload_path = 'assets/uploads/f'.$this->session->userdata('saas_id').'/profiles/'.$system_user->profile;
					}
					$tempRow['profile'] = base_url($file_upload_path);
				}

				$tempRow['short_name'] = mb_substr($system_user->first_name, 0, 1, "utf-8").''.mb_substr($system_user->last_name, 0, 1, "utf-8");
				$group = $this->ion_auth->get_users_groups($system_user->user_id)->result();
				$tempRow['role'] = ucfirst($group[0]->name);
				$tempRow['group_id'] = $group[0]->id;
				$tempRow['projects_count'] = get_count('id','projects','client_id='.$system_user->user_id);
				$rows[] = $tempRow;
				}
			}
			$this->data['system_users'] = isset($rows)?$rows:'';
			$this->data['user_groups'] = $this->ion_auth->groups(array(1,2,4))->result();
			$this->load->view('clients',$this->data);
		}else{
			redirect('auth', 'refresh');
		}
	}

	public function index()
	{	
		if ($this->ion_auth->logged_in() && is_module_allowed('team_members') && ($this->ion_auth->is_admin() || permissions('user_view') || $this->ion_auth->in_group(3)))
		{
			$this->data['page_title'] = 'Users - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			if($this->ion_auth->in_group(3)){
				$system_users = $this->ion_auth->users(array(3))->result();
			}else{
				$system_users = $this->ion_auth->users(array(1,2))->result();
			}
			foreach ($system_users as $system_user) {
				if($this->session->userdata('saas_id') == $system_user->saas_id){
					$tempRow['id'] = $system_user->user_id;
					$tempRow['email'] = $system_user->email;
					$tempRow['active'] = $system_user->active;
					$tempRow['first_name'] = $system_user->first_name;
					$tempRow['last_name'] = $system_user->last_name;
					$tempRow['company'] = company_details('company_name', $system_user->user_id);
					$tempRow['phone'] = $system_user->phone!=0?$system_user->phone:'';

					$tempRow['profile'] = '';
					if($system_user->profile){
						if(file_exists('assets/uploads/profiles/'.$system_user->profile)){
							$file_upload_path = 'assets/uploads/profiles/'.$system_user->profile;
						}else{
							$file_upload_path = 'assets/uploads/f'.$this->session->userdata('saas_id').'/profiles/'.$system_user->profile;
						}
						$tempRow['profile'] = base_url($file_upload_path);
					}

					$tempRow['short_name'] = mb_substr($system_user->first_name, 0, 1, "utf-8").''.mb_substr($system_user->last_name, 0, 1, "utf-8");
					$group = $this->ion_auth->get_users_groups($system_user->user_id)->result();
					if($group[0]->name == 'admin'){
						$tempRow['role'] = $this->lang->line('admin')?htmlspecialchars($this->lang->line('admin')):'Admin';
					}else{
						$tempRow['role'] = $this->lang->line('team_member')?htmlspecialchars($this->lang->line('team_member')):'Team Member';
					}
					$tempRow['group_id'] = $group[0]->id;
					$tempRow['projects_count'] = get_count('id','project_users','user_id='.$system_user->user_id);
					$tempRow['tasks_count'] = get_count('id','task_users','user_id='.$system_user->user_id);
					$rows[] = $tempRow;
				}
			}
			$this->data['system_users'] = isset($rows)?$rows:'';
			$this->data['user_groups'] = $this->ion_auth->groups(array(1,2))->result();
			if($this->ion_auth->in_group(3)){
				$this->load->view('saas-admins',$this->data);
			}else{
				$this->load->view('users',$this->data);
			}
			
		}else{
			redirect('auth', 'refresh');
		}
	}

	public function get_saas_users()
	{	
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(3))
		{
			return $this->users_model->get_saas_users();
		}else{
			return '';
		}
	}

	public function saas()
	{	
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(3))
		{
			set_expire_all_expired_plans();
			$this->notifications_model->edit('', 'new_user', '', '', '');
			$this->data['page_title'] = 'Users - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['plans'] = $this->plans_model->get_plans();
			$this->load->view('saas-users',$this->data);
		}else{
			redirect('auth', 'refresh');
		}
	}

	public function company()
	{	
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(4))
		{
			$this->data['page_title'] = 'Company Settings - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['company_details'] = company_details();
			$this->load->view('company',$this->data);
		}else{
			redirect('auth', 'refresh');
		}
	}

	public function profile()
	{	
		if ($this->ion_auth->logged_in())
		{
			$this->data['page_title'] = 'Profile - '.company_name();
			$this->data['current_user'] = $profile_user = $this->ion_auth->user()->row();
			
			$tempRow['id'] = $profile_user->user_id;
			$tempRow['email'] = $profile_user->email;
			$tempRow['active'] = $profile_user->active;
			$tempRow['first_name'] = $profile_user->first_name;
			$tempRow['last_name'] = $profile_user->last_name;
			$tempRow['phone'] = $profile_user->phone!=0?$profile_user->phone:'';
			$tempRow['company'] = company_details('company_name', $profile_user->user_id);
			$tempRow['profile'] = !empty($profile_user->profile)?$profile_user->profile:'';
			$tempRow['short_name'] = mb_substr($profile_user->first_name, 0, 1, "utf-8").''.mb_substr($profile_user->last_name, 0, 1, "utf-8");
			$group = $this->ion_auth->get_users_groups($profile_user->user_id)->result();
			$tempRow['role'] = ucfirst($group[0]->name);
			$tempRow['group_id'] = $group[0]->id;
			if($this->ion_auth->in_group(4)){
				$tempRow['projects_count'] = get_count('id','projects','client_id='.$profile_user->user_id);
			}else{
				$tempRow['projects_count'] = get_count('id','project_users','user_id='.$profile_user->user_id);
			}

			$tempRow['tasks_count'] = get_count('id','task_users','user_id='.$profile_user->user_id);
        		
			$this->data['profile_user'] = $tempRow;
			$this->data['user_groups'] = $this->ion_auth->groups(array(1,2))->result();
			$this->load->view('profile',$this->data);
		}else{
			redirect('auth', 'refresh');
		}
	}

	public function ajax_get_user_by_id($id='')
	{	
		$id = !empty($id)?$id:$this->input->post('id');
		if ($this->ion_auth->logged_in() && !empty($id) && is_numeric($id))
		{
			$system_user = $this->ion_auth->user($id)->row();
			if(!empty($system_user)){
				$tempRow['id'] = $system_user->id;
				$tempRow['profile'] = $system_user->profile;
				$tempRow['first_name'] = $system_user->first_name;
				$tempRow['last_name'] = $system_user->last_name;
				$tempRow['company'] = company_details('company_name', $system_user->id);
				$tempRow['short_name'] = mb_substr($system_user->first_name, 0, 1, "utf-8").''.mb_substr($system_user->last_name, 0, 1, "utf-8");
				$tempRow['phone'] = $system_user->phone;
				$tempRow['active'] = $system_user->active;
				$current_plan = get_current_plan($system_user->saas_id);
				if($current_plan){
					$tempRow['current_plan_expiry'] = format_date($current_plan['end_date'],system_date_format());
					$tempRow['current_plan_id'] = $current_plan['plan_id'];
				}
				$group = $this->ion_auth->get_users_groups($system_user->id)->result();
				$tempRow['role'] = ucfirst($group[0]->name);
				$tempRow['group_id'] = $group[0]->id;
				$this->data['error'] = false;
				$this->data['data'] = $tempRow;
				$this->data['message'] = 'Successful';
				echo json_encode($this->data);
			}else{
				$this->data['error'] = true;
				$this->data['message'] = 'No user found.';
				echo json_encode($this->data);
			}
		}else{
			$this->data['error'] = true;
			$this->data['message'] = 'Access Denied';
			echo json_encode($this->data);
		}
	}

}







