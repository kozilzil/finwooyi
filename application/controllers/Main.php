<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->loginCheck()) {
			redirect($this->serverUrl.'/account/index');
		}
	}

	public function index() {
		$this->setBeforeAssets();
		$this->setAssets('main', 'index');
		$this->setAfterAssets();

		$this->setTitle('우이중앙교회 재정부 - 성도 리스트');
		$this->base_view("/main/index");
	}
}
