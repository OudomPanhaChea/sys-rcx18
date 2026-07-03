<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
	}


	public function in_out()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			
			$is_running = $this->attendance_model->my_att_running($this->session->userdata('user_id'));
			if($is_running){

				$data['check_out'] = date("Y-m-d H:i:s");
				$this->attendance_model->edit($data, '', $this->session->userdata('user_id'), true);

			}else{
				$data['saas_id'] = $this->session->userdata('saas_id');
				$data['user_id'] = $this->session->userdata('user_id');
				$data['check_in'] = date("Y-m-d H:i:s");
				$data['check_out'] = NULL;
				$this->attendance_model->create($data);
			}
			
			$this->session->set_flashdata('message', $this->lang->line('updated_successfully')?$this->lang->line('updated_successfully'):"Updated successfully.");
			$this->session->set_flashdata('message_type', 'success');

		}else{
			show_404();
		}
		
		redirect('home', 'refresh');
	}

	public function edit()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			$this->form_validation->set_rules('update_id', 'ID', 'trim|required|strip_tags|xss_clean|is_numeric');
		
			if($this->form_validation->run() == TRUE){

				if($this->input->post('user_id')){
					$data['user_id'] = $this->input->post('user_id');
				}

				if($this->input->post('check_in')){
					$data['check_in'] = format_date($this->input->post('check_in'),"Y-m-d H:i:s");
				}

				if($this->input->post('check_out')){
					$data['check_out'] = format_date($this->input->post('check_out'),"Y-m-d H:i:s");
				}
				
				$data['note'] = $this->input->post('note')?$this->input->post('note'):'';

				if($this->attendance_model->edit($data, $this->input->post('update_id'))){

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

	public function get_attendance_by_id()
	{	
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{	
			$this->form_validation->set_rules('id', 'id', 'trim|required|strip_tags|xss_clean|is_numeric');

			if($this->form_validation->run() == TRUE){
				$data = $this->attendance_model->get_attendance_by_id($this->input->post('id'));
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

	public function index()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4) && is_module_allowed('attendance'))
		{
			$this->data['page_title'] = 'Attendance - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['system_users'] = $this->ion_auth->users(array(1,2))->result();
			
			$this->load->view('attendance',$this->data);

		}else{
			redirect('auth', 'refresh');
		}
	}

	public function get_attendance()
	{	
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			return $this->attendance_model->get_attendance();
		}else{
			return '';
		}
	}

	public function create()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{
			
				$data['saas_id'] = $this->session->userdata('saas_id');
				$data['user_id'] = $this->input->post('user_id')?$this->input->post('user_id'):$this->session->userdata('user_id');
				$data['check_in'] = $this->input->post('check_in')?format_date($this->input->post('check_in'),"Y-m-d H:i:s"):date("Y-m-d H:i:s");
				$data['check_out'] = $this->input->post('check_out')?format_date($this->input->post('check_out'),"Y-m-d H:i:s"):NULL;
				$data['note'] = $this->input->post('note')?$this->input->post('note'):'';

				$id = $this->attendance_model->create($data);
				
				if($id){

					$this->session->set_flashdata('message', $this->lang->line('created_successfully')?$this->lang->line('created_successfully'):"Created successfully.");
					$this->session->set_flashdata('message_type', 'success');
					$this->data['error'] = false;
					$this->data['data'] = $id;
					$this->data['message'] = $this->lang->line('created_successfully')?$this->lang->line('created_successfully'):"Created successfully.";
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

	public function delete($id='')
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4))
		{

			if(empty($id)){
				$id = $this->uri->segment(3)?$this->uri->segment(3):'';
			}

			if(!empty($id) && $this->attendance_model->delete($id)){
				
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



}







