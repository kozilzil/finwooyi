<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {
	public function index() {
		$this->setBeforeAssets();
		$this->setAssets('account', 'index');
		$this->setAfterAssets();

		$this->setTitle(getenv('title.account.index'));
		$this->base_view("/account/index");
	}

	public function login() {
		$posts = $this->input->post();
		$pass = base64_encode(hash('sha512', $posts['password'], true));
		$params = [
			'id'		=> $posts['id'],
			'password'	=> $pass
		];

		$this->load->model('User_model');
		$result = $this->User_model->login($params);
		if ($result['EXIST'] == 0) {
			echo "<script>alert('아이디 또는 비밀번호가 잘못되었습니다.');location.href = '/account';</script>";
		} else {
			// ROLE 컬럼이 없으면 추가
			$this->db->query("ALTER TABLE TB_USER ADD COLUMN IF NOT EXISTS ROLE TINYINT NOT NULL DEFAULT 4");

			$this->load->model('User_model');
			$result = $this->User_model->user_info($params);

			// 기본 ROLE이 없으면 일반(4)로 세팅
			if (!array_key_exists('ROLE', $result) || $result['ROLE'] == null) {
				$result['ROLE'] = 4;
			}
			$this->session->set_userdata('info', $result);

			redirect($this->serverUrl . '/management/user');
		}
	}

	public function logout() {
		$this->session->sess_destroy();
		redirect($this->serverUrl . "/account");
	}
}
