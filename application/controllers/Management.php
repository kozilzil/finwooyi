<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management extends MY_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->loginCheck()) {
			redirect($this->serverUrl.'/account/index');
		}
	}

	/**
	 * 성도관리 View
	 */
	public function user() {
		$page = $this->uri->segment(3) ?? 1;
		$gets = $this->input->get();
		$type = $gets['type'] ?? '';
		$content = $gets['content'] ?? '';

		$limit = 10;
		$params = [
			'page'	=> $page,
			'limit'	=> $limit,
			'type'	=> $type,
			'content' => $content
		];
		
		$this->load->model('User_model');
		$list = $this->User_model->user_list($params);

		$this->load->library("pagination");

		$config = [
			'base_url' => '/management/user',
			'num_links' => 3,
			'per_page' => 10,
			'cur_page' => $page,
			'uri_segment' => 3,
			'use_page_numbers' => true,
			'full_tag_open' => '<ul id="list-pagination" class="list-pagenation">',
			'full_tag_close' => '</ul>',
			'first_tag_open' => '<li>',
			'first_tag_close' => '</li>',
			'last_tag_open' => '<li>',
			'last_tag_close' => '</li>',
			'next_tag_open' => '<li>',
			'next_tag_close' => '</li>',
			'prev_tag_open' => '<li>',
			'prev_tag_close' => '</li>',
			'cur_tag_open' => '<li class="on">',
			'cur_tag_close' => '</li>',
			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>',
			'first_link' => '&lt;&lt;',
			'last_link' => '&gt;&gt;',
			'page_query_string' => false,
			'reuse_query_string' => true
		];

		if ($list == null) {
			$config['total_rows'] = 0;
		} else {
			$config['total_rows'] = $list[0]['TOTAL_CNT'];
		}
		$this->pagination->initialize($config);
		$pagination['paging'] = $this->pagination->create_links();

		$this->setBeforeAssets();
		$this->setAssets('management', 'user');
		$this->setAfterAssets();

		$this->addViewData('data', 'list', $list);
		$this->addViewData('data', 'pagination', $pagination);
		$this->addViewData('data', 'page', $page);
		$this->addViewData('data', 'limit', $limit);
		$this->addViewData('data', 'type', $type);
		$this->addViewData('data', 'content', $content);

		$this->setMenuList();

		$this->setTitle(getenv('title.management.user'));
		$this->base_view("/management/user/index");
	}

	/**
	 * 성도관리 등록/수정 View
	 */
	public function user_write() {
		$no = $this->uri->segment(3);

		$this->addViewData('data', 'title', '등록');
		if ($no != null) {
			$this->addViewData('data', 'title', '수정');

			$params = [
				'no' => $no
			];
			$this->load->model('User_model');
			$result = $this->User_model->user_info($params);
			if ($result == null) {
				echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
			}

			$this->addViewData('data', 'info', $result);
		}

		$this->setBeforeAssets();
		$this->setAssets('management', 'user_write');
		$this->setAfterAssets();

		$this->setMenuList();


		$this->setTitle(getenv('title.management.user_write'));
		$this->base_view("/management/user/write");
	}

	/**
	 * 성도 등록
	 */
	public function user_register() {
		$posts = $this->input->post();

		$params = [
			'name'			=> $posts['name']
		];
		if (array_key_exists('id', $posts) && $posts['id'] != '') {
			$params['id'] = $posts['id'];
		}
		if (array_key_exists('office', $posts) && $posts['office'] != '') {
			$params['office'] = $posts['office'];
		}
		if (array_key_exists('explanation', $posts) && $posts['explanation'] != '') {
			$params['explanation'] = $posts['explanation'];
		}
		if (array_key_exists('password', $posts) && $posts['password'] != '') {
			$pass = base64_encode(hash('sha512', $posts['password'], true));
			$params['password'] = $pass;
		}
		$this->load->model('User_model');
		$result = $this->User_model->user_register($params);

		if ($result != null) {
			echo json_encode(['status' => true, 'data' => ['NO' => $result['NO']]]);
		} else {
			echo json_encode(['status' => false]);
		}
	}

	/**
	 * 성도 수정
	 */
	public function user_update() {
		$posts = $this->input->post();

		$params = [
			'no' 			=> $posts['no'],
			'id' 			=> $posts['id'],
			'name'			=> $posts['name'],
			'office'		=> $posts['office'],
			'password'		=> null,
			'explanation'	=> $posts['explanation']
		];
		if (array_key_exists('password', $posts)) {
			$pass = base64_encode(hash('sha512', $posts['password'], true));
			$params['password'] = $pass;
		}
		$this->load->model('User_model');
		$result = $this->User_model->user_update($params);

		if ($result == 1) {
			echo json_encode(['status' => true]);
		} else {
			echo json_encode(['status' => false]);
		}
	}

	/**
	 * 성도 삭제
	 */
	public function user_delete() {
		$posts = $this->input->post();

		$this->load->model('User_model');
		$result = $this->User_model->user_delete(['no' => $posts['no']]);

		if ($result == 1) {
			echo json_encode(['status' => true]);
		} else {
			echo json_encode(['status' => false]);
		}
	}

	/**
	 * 성도리스트 Data Json
	 */
	public function user_list() {
		$page = $this->uri->segment(3) ?? 1;
		$gets = $this->input->get();
		$type = $gets['type'] ?? '';
		$content = $gets['content'] ?? '';

		$limit = $get['limit'] ?? 10;
		$params = [
			'page'	=> $page,
			'limit'	=> $limit,
			'type'	=> $type,
			'content' => $content
		];

		$this->load->model('User_model');
		$list = $this->User_model->user_list($params);

		echo json_encode(['status' => true, 'data' => $list]);
	}

	/**
	 * 성도 등록을 위한 리스트 반환
	 */
	public function user_list_for_register() {
		$page = $this->uri->segment(3) ?? 1;
		$gets = $this->input->get();
		$type = $gets['type'] ?? '';
		$content = $gets['content'] ?? '';

		$limit = $get['limit'] ?? 10;
		$params = [
			'page'	=> $page,
			'limit'	=> $limit,
			'type'	=> $type,
			'content' => $content
		];

		$this->load->model('User_model');
		$list = $this->User_model->user_list_for_register($params);

		echo json_encode(['status' => true, 'data' => $list]);
	}

	public function user_info() {
		$posts = $this->input->post();
		$params = [
			'no' => $posts['no']
		];
		$this->load->model('User_model');
		$result = $this->User_model->user_info($params);
		if ($result != null) {
			echo json_encode(['status' => true, 'data' => $result]);
		} else {
			echo json_encode(['status' => false]);
		}
	}

	public function auth_data() {
		$posts = $this->input->post();

		$menuList = $this->getMenuList();
		$authList = $this->getAuthList($posts);

		$this->load->library('blade');
		$this->blade
			->set_data(['menu' => $menuList, 'auth' => $authList])
			->render("management/user/modal/auth_data");
	}
}
