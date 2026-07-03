<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leaves extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function delete($id='')
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{

			if(empty($id)){
				$id = $this->uri->segment(3)?$this->uri->segment(3):'';
			}
			
			if(!empty($id) && is_numeric($id) && $this->leaves_model->delete($this->session->userdata('user_id'), $id)){

				$this->session->set_flashdata('message', $this->lang->line('deleted_successfully')?$this->lang->line('deleted_successfully'):"Deleted successfully.");
				$this->session->set_flashdata('message_type', 'success');

				$this->data['error'] = false;
				$this->data['message'] = $this->lang->line('deleted_successfully')?$this->lang->line('deleted_successfully'):"Deleted successfully.";
				echo json_encode($this->data);
			}else{
				
				$this->data['error'] = true;
				$this->data['message'] = $this->lang->line('something_wrong_try_again')?$this->lang->line('something_wrong_try_again'):"Something wrong! Try again.";
				echo json_encode($this->data);
			}

		}else{
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied')?$this->lang->line('access_denied'):"Access Denied";
			echo json_encode($this->data);
		}
	}

	public function edit()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			$this->form_validation->set_rules('update_id', 'Leave ID', 'trim|required|strip_tags|xss_clean|is_numeric');
			$this->form_validation->set_rules('leave_days', 'Leave Days', 'trim|required|strip_tags|xss_clean|is_numeric');
			$this->form_validation->set_rules('starting_date', 'Starting Date', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('ending_date', 'Ending Date', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('leave_reason', 'Leave Reason', 'trim|required|strip_tags|xss_clean');

			if($this->form_validation->run() == TRUE){
				
				$data['user_id'] = $this->input->post('user_id')?$this->input->post('user_id'):$this->session->userdata('user_id');
				$data['leave_days'] = $this->input->post('leave_days');
				$data['starting_date'] = format_date($this->input->post('starting_date'),"Y-m-d");
				$data['ending_date'] = format_date($this->input->post('ending_date'),"Y-m-d");
				$data['leave_reason'] = $this->input->post('leave_reason');
				if($this->input->post('status')){
					$data['status'] = $this->input->post('status');
					if($this->input->post('status') == 1){
						$notification_data = array(
							'notification' => 'leave request accepted',
							'type' => 'leave_request_accepted',	
							'type_id' => $this->input->post('update_id'),	
							'from_id' => $this->session->userdata('user_id'),
							'to_id' => $this->input->post('user_id')?$this->input->post('user_id'):$this->session->userdata('user_id'),
						);
						$notification_id = $this->notifications_model->create($notification_data);
					}elseif($this->input->post('status') == 2){
						$notification_data = array(
							'notification' => 'leave request rejected',
							'type' => 'leave_request_rejected',	
							'type_id' => $this->input->post('update_id'),	
							'from_id' => $this->session->userdata('user_id'),
							'to_id' => $this->input->post('user_id')?$this->input->post('user_id'):$this->session->userdata('user_id'),
						);
						$notification_id = $this->notifications_model->create($notification_data);
					}
				}

				if($this->leaves_model->edit($this->input->post('update_id'), $data)){
					$this->session->set_flashdata('message', $this->lang->line('updated_successfully')?$this->lang->line('updated_successfully'):"Updated successfully.");
					$this->session->set_flashdata('message_type', 'success');
					$this->data['error'] = false;
					$this->data['message'] = $this->lang->line('updated_successfully')?$this->lang->line('updated_successfully'):"Updated successfully.";
					echo json_encode($this->data); 
				}else{
					$this->data['error'] = true;
					$this->data['message'] = $this->lang->line('something_wrong_try_again')?$this->lang->line('something_wrong_try_again'):"Something wrong! Try again.";
					echo json_encode($this->data);
				}
			}else{
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data); 
			}

		}else{
			
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied')?$this->lang->line('access_denied'):"Access Denied";
			echo json_encode($this->data); 
		}
		
	}

	public function get_leaves_by_id()
	{	
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{	
			$this->form_validation->set_rules('id', 'id', 'trim|required|strip_tags|xss_clean|is_numeric');

			if($this->form_validation->run() == TRUE){
				$data = $this->leaves_model->get_leaves_by_id($this->input->post('id'));
				$this->data['error'] = false;
				$this->data['data'] = $data?$data:'';
				$this->data['message'] = "Success";
				echo json_encode($this->data); 
			}else{
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data); 
			}
		}else{
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied')?$this->lang->line('access_denied'):"Access Denied";
			echo json_encode($this->data); 
		}
	}

	public function get_leaves()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			return $this->leaves_model->get_leaves();
		}else{
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied')?$this->lang->line('access_denied'):"Access Denied";
			echo json_encode($this->data);
		}
	}

	public function create()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			$this->form_validation->set_rules('leave_days', 'Leave Days', 'trim|required|strip_tags|xss_clean|is_numeric');
			$this->form_validation->set_rules('starting_date', 'Starting Date', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('ending_date', 'Ending Date', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('leave_reason', 'Leave Reason', 'trim|required|strip_tags|xss_clean');

			if($this->form_validation->run() == TRUE){
				$data = array(
					'saas_id' => $this->session->userdata('saas_id'),
					'user_id' => $this->input->post('user_id')?$this->input->post('user_id'):$this->session->userdata('user_id'),
					'leave_days' => $this->input->post('leave_days'),
					'starting_date' => format_date($this->input->post('starting_date'),"Y-m-d"),
					'ending_date' => format_date($this->input->post('ending_date'),"Y-m-d"),	
					'leave_reason' => $this->input->post('leave_reason'),	
				);

				$id = $this->leaves_model->create($data);
				
				if($id){

					if($this->ion_auth->in_group(2)){
						$system_admins = $this->ion_auth->users(array(1))->result();
						foreach ($system_admins as $system_user) {
							if($this->session->userdata('saas_id') == $system_user->saas_id && $system_user->user_id != $this->session->userdata('user_id')){
								$notification_data = array(
									'notification' => 'Leave request received',
									'type' => 'leave_request',	
									'type_id' => $id,	
									'from_id' => $this->input->post('user_id')?$this->input->post('user_id'):$this->session->userdata('user_id'),
									'to_id' => $system_user->user_id,		
								);
								$notification_id = $this->notifications_model->create($notification_data);
							}
						}
					}

					$this->session->set_flashdata('message', $this->lang->line('created_successfully')?$this->lang->line('created_successfully'):"Created successfully.");
					$this->session->set_flashdata('message_type', 'success');
					$this->data['error'] = false;
					$this->data['message'] = $this->lang->line('created_successfully')?$this->lang->line('created_successfully'):"Created successfully.";
					echo json_encode($this->data); 
				}else{
					$this->data['error'] = true;
					$this->data['message'] = $this->lang->line('something_wrong_try_again')?$this->lang->line('something_wrong_try_again'):"Something wrong! Try again.";
					echo json_encode($this->data);
				}
			}else{
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data); 
			}

		}else{
			
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied')?$this->lang->line('access_denied'):"Access Denied";
			echo json_encode($this->data); 
		}
		
	}

	public function index()
	{
		if ($this->ion_auth->logged_in()  && is_module_allowed('leaves') && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			$this->data['page_title'] = 'Leaves - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['system_users'] = $this->ion_auth->users(array(1,2))->result();
			$this->load->view('leaves',$this->data);
		}else{
			redirect('auth', 'refresh');
		}
	}

}
