<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Offering extends MY_Controller {
	public function __construct(){
		parent::__construct();
		if (!$this->loginCheck()) {
			redirect($this->serverUrl.'/account/index');
		}
	}

	#region 헌금입력
	/**
	 * 헌금등록 View
	 */
	public function write() {
		$this->setBeforeAssets();
		$this->setAssets('offering', 'write');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.offering.write"));
		$this->base_view("/offering/write/index");
	}

	/**
	 * 헌금분류 리스트
	 */
	public function offering_list() {
		$posts = $this->input->post();

		$this->load->model('Offering_model');
		$params = [
			'is-income' => $posts['is-income'],
			'parent'	=> $posts['parent']
		];
		$result = $this->Offering_model->offering_type_list($params);
		if ($result != null) {
			echo json_encode(['status' => true, 'data' => $result]);
		} else {
			echo json_encode(['status' => false]);
		}
	}

	/**
	 * 헌금등록
	 */
	public function offering_register() {
		$posts = $this->input->post();

		$this->load->model('User_model');
		$userInfo = $this->User_model->user_info([
			'name'	=> $posts['name']
		]);

		$params = [
			'type'			=> $posts['type'],
			'reg-date'		=> $posts['regDate'],
			'etc'			=> $posts['etc'],
			'is-online'		=> $posts['is_online'],
			'user-no'		=> $userInfo['NO'],
			'price'			=> $posts['price']
		];

		$this->load->model('Offering_model');
		$result = $this->Offering_model->income_register($params);

		if ($result != null) {
			echo json_encode(['status' => true, 'data' => ['NO' => $result['NO']]]);
		} else {
			echo json_encode(['status' => false]);
		}
	}

	/**
	 * 헌금 리스트
	 */
	public function income_list() {
		$posts = $this->input->post();
		$page = $posts['page'] ?? 1;
		$limit = 10;

		$this->load->model('Income_model');
		$params = [
			'date' 	=> $posts['date'],
			'limit'	=> $limit,
			'page'	=> $page
		];
		$list = $this->Income_model->income_list($params);

		$this->load->library("pagination");

		$config = [
			'base_url' => '#',
			'num_links' => 3,
			'per_page' => $limit,
			'cur_page' => $page,
			'uri_segment' => 3,
			'use_page_numbers' => true,
			'full_tag_open' => '<ul id="data-list-pagination" class="list-pagenation">',
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

		$data = [
			'registrants'	=> $list,
			'pagination' 	=> $pagination,
			'page'			=> $page,
			'limit'			=> $limit
		];

		$this->load->library('blade');
		$this->blade
			->set_data($data)
			->render('/offering/input/income_list');
	}

	/**
	 * 헌금 삭제
	 */
	public function offering_delete() {
		$posts = $this->input->post();
		if (!array_key_exists('no', $posts)) {
			echo json_encode(['status' => false]);
		} else {
			$this->load->model('Offering_model');
			$result = $this->Offering_model->income_delete(['no' => $posts['no']]);

			echo json_encode(['status' => $result]);
		}
	}

	/**
	 * 헌금 수정
	 */
	public function offering_update() {
		$posts = $this->input->post();

		$this->load->model('User_model');
		$userInfo = $this->User_model->user_info([
			'name'	=> $posts['name']
		]);

		$params = [
			'no'				=> $posts['no'],
			'name'				=> $posts['name'],
			'price'				=> $posts['price'],
			'etc'				=> $posts['etc'],
			'is-online'			=> $posts['isOnline'],
			'offering-type-no'	=> $posts['offeringTypeNo'],
			'user-no'			=> $userInfo['NO']
		];

		$this->load->model('Offering_model');
		$result = $this->Offering_model->income_update($params);

		if ($result != null) {
			echo json_encode(['status' => true]);
		} else {
			echo json_encode(['status' => false]);
		}

	}

	/**
	 * 헌금타입
	 */
	public function offering_type_info() {
		$posts = $this->input->post();

		$this->load->model('Offering_model');
		$params = [
			'no'	=> $posts['no']
		];
		$result = $this->Offering_model->offering_type_info($params);
		if ($result != null) {
			echo json_encode(['status' => true, 'data' => $result]);
		} else {
			echo json_encode(['status' => false]);
		}
	}
	#endregion

	#region 헌금계수
	/**
	 * 헌금계수View
	 */
	public function coefficient() {
		$this->setBeforeAssets();
		$this->setAssets('offering', 'coefficient');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.offering.coefficient"));
		$this->base_view("/offering/coefficient/index");
	}

	/**
	 * 헌금계수 데이터
	 */
	public function coefficient_list() {
		$posts = $this->input->post();

		$this->load->model('Income_model');
		$params = [
			'date' 	=> $posts['date']
		];
		$result = $this->Income_model->coefficient_list($params);

		$data = [];

		for($idx=0;$idx<count($result);$idx++) {
			if(!array_key_exists($result[$idx]['WORKER_NO'], $data)) {
				$data[$result[$idx]['WORKER_NO']] = [
					'name' 				=> $result[$idx]['WORKER_NAME'],
					'subTotal'			=> 0,
					'subOnlineTotal'	=> 0,
					'subOfflineTotal'	=> 0,
					'list'				=> [],
					'firstKey'			=> $result[$idx]['OFFERING_TYPE_NO']
				];
			}

			if(!array_key_exists($result[$idx]['OFFERING_TYPE_NO'], $data[$result[$idx]['WORKER_NO']]['list'])) {
				$data[$result[$idx]['WORKER_NO']]['list'][$result[$idx]['OFFERING_TYPE_NO']] = [
					'type'		=> $result[$idx]['OFFERING_TYPE_NAME'],
					'online' 	=> 0,
					'offline' 	=> 0,
					'price'		=> 0
				];
			}

			if ($result[$idx]['IS_ONLINE'] == 'Y') {
				$data[$result[$idx]['WORKER_NO']]['list'][$result[$idx]['OFFERING_TYPE_NO']]['online'] += $result[$idx]['PRICE'];
				$data[$result[$idx]['WORKER_NO']]['subOnlineTotal'] += $result[$idx]['PRICE'];
			} else {
				$data[$result[$idx]['WORKER_NO']]['list'][$result[$idx]['OFFERING_TYPE_NO']]['offline'] += $result[$idx]['PRICE'];
				$data[$result[$idx]['WORKER_NO']]['subOfflineTotal'] += $result[$idx]['PRICE'];
			}
			$data[$result[$idx]['WORKER_NO']]['list'][$result[$idx]['OFFERING_TYPE_NO']]['price'] += $result[$idx]['PRICE'];

			$data[$result[$idx]['WORKER_NO']]['subTotal'] += $result[$idx]['PRICE'];
		}

		$this->load->library('blade');
		$this->blade
			->set_data($data)
			->render('/offering/coefficient/data_list');
	}
	#endregion

	#region 전체헌금계수
	public function total() {
		$this->setBeforeAssets();
		$this->setAssets('offering', 'total');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.offering.total"));
		$this->base_view("/offering/total/index");
	}

	public function total_list() {
		$posts = $this->input->post();

		$this->load->model('Income_model');
		$params = [
			'date' 	=> $posts['date']
		];
		$result = $this->Income_model->coefficient_list($params);

		$data = [
			'data'		=> [
				'total' 	=> []
			],
			'sum'		=> [
				'total' 	=> [
					'online' 	=> 0,
					'offline'	=> 0,
					'total'		=> 0
				]
			]
		];

		for($idx=0;$idx<count($result);$idx++) {
			// 데이터 - 전체조회
			if (!array_key_exists($result[$idx]['OFFERING_TYPE_NO'], $data['data']['total'])) {
				$data['data']['total'][$result[$idx]['OFFERING_TYPE_NO']] = [
					'name'		=> $result[$idx]['OFFERING_TYPE_NAME'],
					'online' 	=> 0,
					'offline'	=> 0,
					'total'		=> 0
				];
			}
			if ($result[$idx]['IS_ONLINE'] == 'Y') {
				$data['data']['total'][$result[$idx]['OFFERING_TYPE_NO']]['online'] += $result[$idx]['PRICE'];
			} else {
				$data['data']['total'][$result[$idx]['OFFERING_TYPE_NO']]['offline'] += $result[$idx]['PRICE'];
			}
			$data['data']['total'][$result[$idx]['OFFERING_TYPE_NO']]['total'] += $result[$idx]['PRICE'];

			// 총계 - 전체조회
			if ($result[$idx]['IS_ONLINE'] == 'Y') {
				$data['sum']['total']['online'] += $result[$idx]['PRICE'];
			} else {
				$data['sum']['total']['offline'] += $result[$idx]['PRICE'];
			}
			$data['sum']['total']['total'] += $result[$idx]['PRICE'];
		}

		$this->load->library('blade');
		$this->blade
			->set_data($data)
			->render('/offering/total/data_list');
	}
	#endregion

	#region 헌금자명단
	/**
	 * 헌금자명단 View
	 */
	public function registrants() {
		$this->setBeforeAssets();
		$this->setAssets('offering', 'registrants');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.offering.registrants"));
		$this->base_view("/offering/registrants/index");
	}

	/**
	 * 헌금자명단 데이터
	 */
	public function registrants_list() {
		$posts = $this->input->post();

		$this->load->model('Income_model');
		$params = [
			'date' 	=> $posts['date']
		];
		$result = $this->Income_model->registrants_list($params);
		$list = [];
		for($idx=0; $idx < count($result); $idx++) {
			if (!array_key_exists($result[$idx]['OFFERING_TYPE_NO'], $list)) {
				$list[$result[$idx]['OFFERING_TYPE_NO']] = [];
			}

			if ($result[$idx]['USER_NAME'] != '무명') {
				array_push($list[$result[$idx]['OFFERING_TYPE_NO']], [
					'OFFERING_NAME' => $result[$idx]['OFFERING_TYPE_NAME'],
					'NAME' 			=> $result[$idx]['USER_NAME'],
					'ETC'			=> $result[$idx]['ETC']
				]);
			}
		}

		for($idx=0; $idx < count($result); $idx++) {
			$cnt = count($list[$result[$idx]['OFFERING_TYPE_NO']]);
			if ($cnt == 0) {
				if ($result[$idx]['USER_NAME'] == '무명') {
					array_push($list[$result[$idx]['OFFERING_TYPE_NO']], [
						'OFFERING_NAME' => $result[$idx]['OFFERING_TYPE_NAME'],
						'NAME' 			=> $result[$idx]['USER_NAME'],
						'ETC'			=> $result[$idx]['ETC']
					]);
				}
			} else if ($cnt > 0 && $list[$result[$idx]['OFFERING_TYPE_NO']][$cnt-1]['NAME'] != '무명') {
				if ($result[$idx]['USER_NAME'] == '무명') {
					array_push($list[$result[$idx]['OFFERING_TYPE_NO']], [
						'OFFERING_NAME' => $result[$idx]['OFFERING_TYPE_NAME'],
						'NAME' 			=> $result[$idx]['USER_NAME'],
						'ETC'			=> $result[$idx]['ETC']
					]);
				}
			}
		}

		$this->load->library('blade');
		$this->blade
			->set_data(['data' => $list])
			->render('/offering/registrants/data_list');
	}

	/**
	 * 헌금자명단 엑셀 다운로드
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function registrants_excel_download() {
		$gets = $this->input->get();

		$this->load->model('Income_model');
		$params = [
			'date' 	=> $gets['date']
		];
		$result = $this->Income_model->registrants_list($params);

		$orderChangeResult = [];
		for ($idx=0; $idx < count($result); $idx++) {
			if ($result[$idx]['OFFERING_TYPE_NAME'] == '십일조헌금') {
				array_push($orderChangeResult, $result[$idx]);
			}
		}

		for ($idx=0; $idx < count($result); $idx++) {
			if ($result[$idx]['OFFERING_TYPE_NAME'] != '십일조헌금') {
				array_push($orderChangeResult, $result[$idx]);
			}
		}

		$values = [];
		$totalPrice = 0;
		for($idx=0;$idx<count($orderChangeResult);$idx++) {
			array_push($values, ['type' => '', 'name' => '', 'etc' => '', 'price' => '', 'is_online' => '']);
		}

		for($idx=0; $idx < count($orderChangeResult); $idx++) {
			$values[$idx]['type'] = $orderChangeResult[$idx]['OFFERING_TYPE_NAME'];
			$values[$idx]['name'] = $orderChangeResult[$idx]['USER_NAME'];
			if ($orderChangeResult[$idx]['ETC'] != '') {
				if ($values[$idx]['etc'] != '') {
					$values[$idx]['etc'] .= '/';
				}
				$values[$idx]['etc'] .= $orderChangeResult[$idx]['ETC'];
			}
			$values[$idx]['price'] = number_format($orderChangeResult[$idx]['PRICE']);
			if ($orderChangeResult[$idx]['IS_ONLINE'] == 'Y') {
				$values[$idx]['is_online'] = '온라인';
			}
			$totalPrice += $orderChangeResult[$idx]['PRICE'];
		}

		$spreadsheet = IOFactory::load("assets/offering_template_2023.xlsx");

		//$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		//I1
		$sheet->setCellValue('I1', $gets['date']);
		// 행번호 초기화
		$column = 'A';
		/*
		$headers = ['헌금종류', '이름', '기타입력', '금액', '온라인헌금', '헌금종류', '이름', '기타입력', '금액', '온라인헌금'];

		foreach ($headers as $header) {
			$sheet->setCellValue($column++ . '1', $header);
		}
		*/

		// 열번호 초기화
		$rowNumLeft = 3;
		$rowNumRight = 3;
		$leftArray = [];
		$rightArray = [];

		for ($idx = 0; $idx < ceil(count($values)/2); $idx++) {
			array_push($leftArray, $values[$idx*2]);

			if ($idx != 0) {
				array_push($rightArray, $values[$idx*2-1]);
			}
		}
		if (count($values) > 0 && count($values) % 2 == 0) {
			array_push($rightArray, $values[count($values)-1]);
		}

		foreach ($leftArray as $key => $value) {
			$sheet->setCellValue('A' . $rowNumLeft, $value['type']);
			$sheet->setCellValue('B' . $rowNumLeft, $value['name']);
			$sheet->setCellValue('C' . $rowNumLeft, $value['etc']);
			$sheet->setCellValue('D' . $rowNumLeft, $value['price']);
			$sheet->setCellValue('E' . $rowNumLeft, $value['is_online']);
			$rowNumLeft++;
		}
		foreach ($rightArray as $key => $value) {
			$sheet->setCellValue('F' . $rowNumRight, $value['type']);
			$sheet->setCellValue('G' . $rowNumRight, $value['name']);
			$sheet->setCellValue('H' . $rowNumRight, $value['etc']);
			$sheet->setCellValue('I' . $rowNumRight, $value['price']);
			$sheet->setCellValue('J' . $rowNumRight, $value['is_online']);
			$rowNumRight++;
		}

		// 범위 내 여러 열 너비 설정
//		$sheet->getColumnDimension('A')->setWidth(16);
//		$sheet->getColumnDimension('B')->setWidth(10);
//		$sheet->getColumnDimension('C')->setWidth(25);
//		$sheet->getColumnDimension('D')->setWidth(15);
//		$sheet->getColumnDimension('E')->setWidth(15);
//		$sheet->getColumnDimension('F')->setWidth(16);
//		$sheet->getColumnDimension('G')->setWidth(10);
//		$sheet->getColumnDimension('H')->setWidth(25);
//		$sheet->getColumnDimension('I')->setWidth(15);
//		$sheet->getColumnDimension('J')->setWidth(15);


		$sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle ( "A2:E" . (count($leftArray)+3) )->getBorders()->getInside()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
		$sheet->getStyle ( "A2:E" . (count($leftArray)+3) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A2:H2" )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A2:E" . (count($leftArray)+3) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle('I')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle ( "F2:J" . (count($leftArray)+3) )->getBorders()->getInside()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
		$sheet->getStyle ( "F2:J" . (count($leftArray)+3) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "F2:J2" )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "F2:J" . (count($leftArray)+3) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);



		$sheet->getStyle('J' . (count($leftArray)+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		//$sheet->getStyle ( "J" . (count($leftArray)+3) )->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );

		$sheet->setCellValue('I' . (count($leftArray)+3), '￦' . number_format($totalPrice) . '원');

		$writer = new Xlsx($spreadsheet);

		$filename = $gets['date'] . '_헌금명단';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	#endregion
}
