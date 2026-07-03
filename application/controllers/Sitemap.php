<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sitemap extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function index(){
		$this->load->view('sitemap', $this->data);
	}

}