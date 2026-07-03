<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Broadcast extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function create_broadcast()
	{		
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(3))
		{
			$this->data['page_title'] = 'Create Broadcast - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['system_users'] = $this->ion_auth->users(array(1))->result();
			$this->data['all_users'] = $this->ion_auth->users()->result();
			
			$this->load->view('broadcast-create',$this->data);

		}else{
			redirect('auth', 'refresh');
		}
		
	}

	public function index()
	{
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(3))
		{
			$this->data['page_title'] = 'Broadcast - '.company_name();
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['system_users'] = $this->ion_auth->users(array(1))->result();
			$this->data['all_users'] = $this->ion_auth->users()->result();
			
			$this->load->view('broadcast',$this->data);

		}else{
			redirect('auth', 'refresh');
		}
	}

	public function get_broadcast()
	{	
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(3))
		{
			return $this->broadcast_model->get_broadcast();
		}else{
			return '';
		}
	}

	public function create()
	{
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(3))
		{
			$this->form_validation->set_rules('to_user[]', 'users', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('subject', 'subject', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('message', 'message', 'required');

			if($this->form_validation->run() == TRUE){

				$data['from_id'] = $this->session->userdata('user_id');
				$data['to_whom'] = json_encode($this->input->post('to_user')); 
				$data['subject'] = $this->input->post('subject');
				$data['msg'] = xss_clean($this->input->post('message'));
				$all_users = new stdClass;
				$saas_admins = new stdClass;
				$subscribers = new stdClass;
				$users = new stdClass;
				$emails = array();
				
				if(in_array("all", $this->input->post('to_user'))){
					$all_users = $this->ion_auth->users()->result();
				}else{
					foreach($this->input->post('to_user') as $key => $who){
						if($who == 'saas_admins'){
							$saas_admins = $this->ion_auth->users(array(3))->result();
						}elseif($who == 'subscribers'){
							$subscribers = $this->ion_auth->users(array(1))->result();
						}elseif($who == 'users'){
							$users = $this->ion_auth->users(array(2))->result();
						}else{
							$emails[] = $who;
						}
					}

				}


				// Function to extract emails from an object or array
				function extractEmails($data) {
					$emails = [];

					if (is_object($data)) {
						foreach ($data as $key => $value) {
							if (is_array($value) || is_object($value)) {
								$emails = array_merge($emails, extractEmails($value));
							}
						}
					} elseif (is_array($data)) {
						foreach ($data as $item) {
							if (is_object($item) && property_exists($item, 'email')) {
								$emails[] = $item->email;
							} elseif (is_string($item)) {
								if (filter_var($item, FILTER_VALIDATE_EMAIL)) {
									$emails[] = $item;
								}
							}
						}
					}

					return $emails;
				}

				// Combine and extract emails
				$allEmails = array_merge(
					extractEmails($all_users),
					extractEmails($saas_admins),
					extractEmails($subscribers),
					extractEmails($users),
					extractEmails($emails)
				);

				// Remove duplicates
				$allEmails = array_unique($allEmails);

				foreach ($allEmails as $email) {
					try {
					send_mail($email, $data['subject'], $data['msg']);

					} catch (Exception $e) {
									
					}
				}

				$id = $this->broadcast_model->create($data);
				
				if($id){

					$this->session->set_flashdata('message', $this->lang->line('sent')?htmlspecialchars($this->lang->line('sent')):'Sent');
					$this->session->set_flashdata('message_type', 'success');
					$this->data['error'] = false;
					$this->data['data'] = $id;
					$this->data['message'] = $this->lang->line('sent')?htmlspecialchars($this->lang->line('sent')):'Sent';
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


	public function delete($id='')
	{
		if ($this->ion_auth->logged_in() && $this->ion_auth->in_group(3))
		{
			if(empty($id)){
				$id = $this->uri->segment(4)?$this->uri->segment(4):'';
			}

			if(!empty($id) && is_numeric($id) && $this->broadcast_model->delete($id)){

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