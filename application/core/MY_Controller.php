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
		'modal'		=> []
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
