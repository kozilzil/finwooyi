<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	// 공통 사용 배열
	private $view_data = [
		'title'		=> '',
		'header' 	=> [
			'css' 	=> [],
			'js' 	=> [],
			'data'	=> []
		],
		'footer' 	=> [
			'css' 	=> [],
			'js' 	=> [],
			'data'	=> []
		],
		'data' 		=> [],
		'modal'		=> [],
		'menu'		=> []
	];
	// resource json data
	private $json;
	protected $serverUrl;

	public function __construct(){
		parent::__construct();

		// Web JSON 파일 읽어오기
		$json_string = file_get_contents('./resource.json');
		$this->json = json_decode($json_string, true);

		$this->serverUrl = getenv('server.scheme').'://'.getenv('server.url').':'.getenv('server.port');
	}

	/**
	 * 세션으로 로그인 상태를 체크한다.
	 * @return bool
	 */
	public function loginCheck() {
		if (array_key_exists("info", $this->session->userdata()) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 세션에 ROLE이 없으면 DB에서 재조회해 갱신 후 반환
	 */
	protected function current_role() {
		$info = $this->session->userdata('info');
		if (!is_array($info)) { $info = []; }
		$no = array_key_exists('NO', $info) ? $info['NO'] : null;
		// 세션에 ROLE이 있고 NO가 없다면 그대로 반환
		if (!empty($info['ROLE']) && $no === null) { return (int)$info['ROLE']; }
		if ($no === null) { return 4; }
		// DB에서 최신 ROLE 조회 후 세션 갱신
		$this->load->model('User_model');
		// ROLE 컬럼이 없으면 추가
		$this->db->query("ALTER TABLE TB_USER ADD COLUMN IF NOT EXISTS ROLE TINYINT NOT NULL DEFAULT 4");
		$user = $this->User_model->user_info(['no' => $no]);
		if ($user && array_key_exists('ROLE', $user)) {
			$info['ROLE'] = (int)$user['ROLE'];
			$this->session->set_userdata('info', $info);
			return (int)$user['ROLE'];
		}
		return 4;
	}

	/**
	 * 최소 권한 체크
	 * @param array $roles 허용 등급 배열 (1:관리자, 2:재정위원, 3:교역자, 4:일반)
	 */
	protected function require_role(array $roles) {
		$role = $this->current_role();
		if (!in_array($role, $roles, true)) {
			show_error('접근 권한이 없습니다.', 403);
			exit;
		}
	}

	/**
	 * 메뉴 URL 기준 권한 체크 (ROLE 1,2는 통과)
	 * @param string $url TB_MENU.URL 값 (예: '/offering/write')
	 * @param array $allowedAuth ['R','W','A']
	 */
	protected function require_menu_auth_by_url($url, array $allowedAuth) {
		$this->load->model('User_model');
		$menuNo = $this->User_model->menu_no_by_url($url);
		if ($menuNo === null) {
			show_error('메뉴 정보가 없습니다.', 403);
			exit;
		}
		$auth = $this->User_model->user_auth($this->session->userdata('info')['NO'], $menuNo);
		if (!in_array($auth, $allowedAuth, true)) {
			show_error('접근 권한이 없습니다.', 403);
			exit;
		}
	}

	public function setMenuList() {
		$this->view_data['menu'] = $this->getMenuList();
	}

	public function getMenuList() {
		$this->load->model('User_model');
		$result = $this->User_model->menu_list(['parent' => 0]);
		for($idx=0;$idx<count($result);$idx++) {
			$child = $this->User_model->menu_list(['parent' => $result[$idx]['NO']]);
			$result[$idx]['child'] = $child;
		}
		return $result;
	}

	public function getAuthList($param) {
		$this->load->model('User_model');
		$result = $this->User_model->auth_list($param);
		return $result;
	}

	#region 기본 View설정관련 내용
	protected function setBeforeAssets() {
		foreach ($this->json['common']['before']['header_css'] AS $key => $value) {
			array_push(
				$this->view_data['header']['css'],
				[
					"type" 		=> "text/css",
					"href" 		=> $value['href'],
					"rel" 		=> "stylesheet"
				]
			);
		}
		foreach ($this->json['common']['before']['header_js'] AS $key => $value) {
			array_push(
				$this->view_data['header']['js'],
				[
					"type"		=> "text/javascript",
					"src" 		=> $value['src'],
					"data"		=> $value["data"]
				]
			);
		}
		foreach ($this->json['common']['before']['footer_css'] AS $key => $value) {
			array_push(
				$this->view_data['footer']['css'],
				[
					"type" 		=> "text/css",
					"href" 		=> $value['href'],
					"rel" 		=> "stylesheet"
				]
			);
		}
		foreach ($this->json['common']['before']['footer_js'] AS $key => $value) {
			array_push(
				$this->view_data['footer']['js'],
				[
					"type"		=> "text/javascript",
					"src" 		=> $value['src'],
					"data"		=> $value["data"]
				]
			);
		}
	}
	protected function setAfterAssets() {
		foreach ($this->json['common']['after']['header_css'] AS $key => $value) {
			array_push(
				$this->view_data['header']['css'],
				[
					"type" 		=> "text/css",
					"href" 		=> $value['href'],
					"rel" 		=> "stylesheet"
				]
			);
		}
		foreach ($this->json['common']['after']['header_js'] AS $key => $value) {
			array_push(
				$this->view_data['header']['js'],
				[
					"type"		=> "text/javascript",
					"src" 		=> $value['src'],
					"data"		=> $value["data"]
				]
			);
		}
		foreach ($this->json['common']['after']['footer_css'] AS $key => $value) {
			array_push(
				$this->view_data['footer']['css'],
				[
					"type" 		=> "text/css",
					"href" 		=> $value['href'],
					"rel" 		=> "stylesheet"
				]
			);
		}
		foreach ($this->json['common']['after']['footer_js'] AS $key => $value) {
			array_push(
				$this->view_data['footer']['js'],
				[
					"type"		=> "text/javascript",
					"src" 		=> $value['src'],
					"data"		=> $value["data"]
				]
			);
		}
	}
	protected function setAssets($class, $method) {
		foreach ($this->json['method'][$class][$method]['header_css'] AS $key => $value) {
			array_push(
				$this->view_data['header']['css'],
				[
					"type" 		=> "text/css",
					"href" 		=> $value['href'],
					"rel" 		=> "stylesheet"
				]
			);
		}
		foreach ($this->json['method'][$class][$method]['header_js'] AS $key => $value) {
			array_push(
				$this->view_data['header']['js'],
				[
					"type"		=> "text/javascript",
					"src" 		=> $value['src'],
					"data"		=> $value["data"]
				]
			);
		}
		foreach ($this->json['method'][$class][$method]['footer_css'] AS $key => $value) {
			array_push(
				$this->view_data['footer']['css'],
				[
					"type" 		=> "text/css",
					"href" 		=> $value['href'],
					"rel" 		=> "stylesheet"
				]
			);
		}
		foreach ($this->json['method'][$class][$method]['footer_js'] AS $key => $value) {
			array_push(
				$this->view_data['footer']['js'],
				[
					"type"		=> "text/javascript",
					"src" 		=> $value['src'],
					"data"		=> $value["data"]
				]
			);
		}
	}
	protected function addModal($view_name, $view_data) {
		if(!array_key_exists($view_name, $this->view_data['modal'])) {
			array_push($this->view_data['modal'], [
				'view_name' => $view_name,
				'view_data'	=> $view_data
			]);
		} else {
			echo 'modal add error!';exit;
		}
	}
	public function base_view($view_name = null) {
		$this->load->library('blade');
		$this->blade
			->set_data([
				'view_name' => $view_name,
				'view_data'	=> $this->view_data
			])
			->render("_parts/_layouts");
	}
	public function addViewData($type, $key, $value) {
		$this->view_data[$type][$key] = $value;
	}
	public function setTitle($title) {
		$this->view_data['title'] = $title;
	}
	#endregion

	/**
	 * API
	 * @param $method
	 * @param $url
	 * @param false $data
	 * @return bool|string
	 */
	public function callAPI($method, $url, $data = false) {
		$curl = curl_init();

		switch (strtoupper($method)) {
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);

				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}

		// Optional Authentication:
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);

		$result = curl_exec($curl);

		curl_close($curl);

		return $result;
	}
}
