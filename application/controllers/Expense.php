<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	class Expense extends MY_Controller {
	public function __construct(){
		parent::__construct();
		if (!$this->loginCheck()) {
			redirect($this->serverUrl.'/account/index');
		}
		// 지출 메뉴 권한으로 제어
		$this->require_menu_auth_by_url('/expense/write', ['R','W','A']);
	}

	#region 지출입력
	/**
	 * 지출입력 View
	 */
	public function write() {
		$this->setBeforeAssets();
		$this->setAssets('expense', 'write');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.expense.write"));
		$this->base_view("/expense/input/index");
	}

	/**
	 * 은행리스트 AJAX
	 */
	public function bank_list() {
		$this->load->model('Expense_model');
		$result = $this->Expense_model->bankList();
		echo json_encode(['status' => true, 'data' => $result]);
	}

	/**
	 * 계좌등록 AJAX
	 */
	public function reg_account() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$this->Expense_model->insertAccount([
			'nickname'	=> $posts['nickname'],
			'bank'		=> $posts['bank'],
			'holder'	=> $posts['holder'],
			'number'	=> $posts['number']
		]);

		echo json_encode(['status' => true]);
	}

	/**
	 * 계좌리스트 View
	 */
		public function account() {
			$posts = $this->input->post();
			$page = isset($posts['page']) ? $posts['page'] : 1;
			$nickname = isset($posts['nickname']) ? $posts['nickname'] : '';
			$limit = 50;
		$this->load->model('Expense_model');
		$list = $this->Expense_model->accountList([
			'limit'		=> $limit,
			'page'		=> $page,
			'nickname' 	=> $nickname
		]);

		$this->load->library("pagination");

		$config = [
			'base_url' => '#',
			'num_links' => 3,
			'per_page' => $limit,
			'cur_page' => $page,
			'uri_segment' => 3,
			'use_page_numbers' => true,
			'full_tag_open' => '<ul id="account-modal-list-pagenation" class="list-pagenation">',
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
			'list'			=> $list,
			'pagination' 	=> $pagination,
			'page'			=> $page,
			'limit'			=> $limit
		];
		$this->load->library('blade');
		$this->blade
			->set_data($data)
			->render('/expense/input/account_data_list');
	}

	/**
	 * 지출등록 AJAX
	 */
	public function expense_register() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$this->Expense_model->register([
			'type'			=> $posts['type'],
			'reg-date'		=> $posts['regDate'],
			'contents'		=> $posts['contents'],
			'price'			=> $posts['price'],
			'pay-method'	=> $posts['payMethod'],
			'account-no'	=> $posts['accountNo'],
			'recipient'		=> $posts['recipient']
		]);

		echo json_encode(['status' => true]);
	}

	/**
	 * 계좌삭제 AJAX
	 */
	public function del_account() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$cnt = $this->Expense_model->getAccountCnt($posts['no']);
		if ($cnt != 0) {
			echo json_encode(['status' => false, 'message' => '해당 계좌로 등록되어 있는 지출내역이 존재합니다. 삭제 후 진행하세요.']);
		} else {
			$this->Expense_model->deleteAccount($posts['no']);
			echo json_encode(['status' => true, 'message' => '삭제되었습니다.']);
		}
	}

	/**
	 * 지출리스트 View
	 */
		public function expense_list() {
			$posts = $this->input->post();
			$page = isset($posts['page']) ? $posts['page'] : 1;
			$limit = 50;
		$this->load->model('Expense_model');
		$list = $this->Expense_model->expenseList([
			'limit'	=> $limit,
			'page'	=> $page,
			'date'	=> $posts['date']
		]);

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
			->render('/expense/input/expense_list');
	}

	/**
	 * 지출삭제 AJAX
	 */
	public function expense_delete() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$this->Expense_model->expenseDelete($posts['no']);

		echo json_encode(['status' => true]);
	}

	/**
	 * 지출수정 AJAX
	 */
	public function expense_update() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$this->Expense_model->expenseUpdate($posts);

		echo json_encode(['status' => true]);
	}
	#endregion

	#region 고정지출관리
	/**
	 * 고정지출입력 View
	 */
	public function fixed() {
		$this->setBeforeAssets();
		$this->setAssets('expense', 'fixed');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.expense.fixed"));
		$this->base_view("/expense/fixed/index");
	}

	/**
	 * 고정지출입력 등록 AJAX
	 */
	public function fixed_register() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$this->Expense_model->fixed_register([
			'type'			=> $posts['type'],
			'contents'		=> $posts['contents'],
			'price'			=> $posts['price'],
			'pay-method'	=> $posts['payMethod'],
			'account-no'	=> $posts['accountNo'],
			'recipient'		=> $posts['recipient'],
			'weekly'		=> $posts['weekly'],
			'year'			=> $posts['year']
		]);

		echo json_encode(['status' => true]);
	}

	/**
	 * 고정지출 리스트 View
	 */
	function _fixedData($count) {
		$posts = $this->input->post();
			$page = isset($posts['page']) ? $posts['page'] : 1;
		$year = $posts['year'];
		$contents = isset($posts['contents']) ? $posts['contents'] : '';
		$weekly = isset($posts['weekly']) ? $posts['weekly'] : '';
		$limit = $count;
		$this->load->model('Expense_model');
		$list = $this->Expense_model->fixedList([
			'limit'		=> $limit,
			'contents'	=> $contents,
			'weekly'	=> $weekly,
			'page'		=> $page,
			'year'		=> $year
		]);

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

		return $data;
	}
	public function fixed_list() {
		$data = $this->_fixedData(10);

		$this->load->library('blade');
		$this->blade
			->set_data($data)
			->render('/expense/fixed/fixed_list');
	}

	public function fixed_popup_list() {
		$data = $this->_fixedData(50);

		$this->load->library('blade');
		$this->blade
			->set_data($data)
			->render('/expense/input/modal/fixed_list');
	}

	public function fixed_delete() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$this->Expense_model->fixedDelete($posts['no']);

		echo json_encode(['status' => true]);
	}

	public function fixed_update() {
		$posts = $this->input->post();
		$this->load->model('Expense_model');
		$this->Expense_model->fixedUpdate($posts);

		echo json_encode(['status' => true]);
	}
	#endregion

	#region 지출계수
	/**
	 * 지출계수 View
	 */
	public function coefficient() {
		$this->setBeforeAssets();
		$this->setAssets('expense', 'coefficient');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.expense.coefficient"));
		$this->base_view("/expense/coefficient/index");
	}

	/**
	 * 지출계수 데이터
	 */
	public function coefficient_list() {
		$posts = $this->input->post();

		$this->load->model('Expense_model');
		$params = [
			'date' 	=> $posts['date']
		];
		$result = $this->Expense_model->coefficient_list($params);

		$data = [];
		$sum = [
			'bank'		=> 0,
			'payment'	=> 0,
			'cash'		=> 0,
			'total'		=> 0
		];
		for($idx=0;$idx<count($result);$idx++) {
			if(!array_key_exists($result[$idx]['OFFERING_TYPE_NO'], $data)) {
				$data[$result[$idx]['OFFERING_TYPE_NO']] = [
					'parent-name' 	=> $result[$idx]['OFFERING_TYPE_PARENT_NAME'],
					'name' 			=> $result[$idx]['OFFERING_TYPE_NAME'],
					'bank'			=> 0,
					'payment'		=> 0,
					'cash'			=> 0,
					'total'			=> 0,
					'parent'		=> [
						'idx'	=> $result[$idx]['OFFERING_TYPE_NO'],
						'count'	=> 1
					]
				];
			}

			if ($idx != 0) {
				if ($data[$result[$idx]['OFFERING_TYPE_NO']]['parent-name'] == $data[$result[$idx-1]['OFFERING_TYPE_NO']]['parent-name']) {
					$data[$result[$idx]['OFFERING_TYPE_NO']]['parent']['count'] = 0;
					$data[$result[$idx]['OFFERING_TYPE_NO']]['parent']['idx'] = $data[$result[$idx-1]['OFFERING_TYPE_NO']]['parent']['idx'];
					$targetIdx = $data[$result[$idx]['OFFERING_TYPE_NO']]['parent']['idx'];
					if ($data[$result[$idx]['OFFERING_TYPE_NO']]['name'] != $data[$result[$idx-1]['OFFERING_TYPE_NO']]['name']) {
						$data[$targetIdx]['parent']['count']++;
					}

					if ($result[$idx]['OFFERING_TYPE_NO'] == $data[$result[$idx]['OFFERING_TYPE_NO']]['parent']['idx']) {
						$data[$result[$idx]['OFFERING_TYPE_NO']]['parent']['count'] = 1;
					}
				}
			}

			if( $result[$idx]['PAYMETHOD'] == 'cash') {
				$data[$result[$idx]['OFFERING_TYPE_NO']]['cash'] += $result[$idx]['PRICE'];
				$sum['cash'] += $result[$idx]['PRICE'];
			} else if( $result[$idx]['PAYMETHOD'] == 'bank') {
				$data[$result[$idx]['OFFERING_TYPE_NO']]['bank'] += $result[$idx]['PRICE'];
				$sum['bank'] += $result[$idx]['PRICE'];
			} else if( $result[$idx]['PAYMETHOD'] == 'payment') {
				$data[$result[$idx]['OFFERING_TYPE_NO']]['payment'] += $result[$idx]['PRICE'];
				$sum['payment'] += $result[$idx]['PRICE'];
			}
			$data[$result[$idx]['OFFERING_TYPE_NO']]['total'] += $result[$idx]['PRICE'];
			$sum['total'] += $result[$idx]['PRICE'];
		}



		$this->load->library('blade');
		$this->blade
			->set_data([
				'data' => $data,
				'sum' => $sum
			])
			->render('/expense/coefficient/data_list');
	}

	/**
	 * 지출계수 엑셀 다운로드
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function coefficient_excel_download() {
		$gets = $this->input->get();

		$this->load->model('Expense_model');
		$params = [
			'date' 	=> $gets['date']
		];
		$result = $this->Expense_model->registrants_list($params);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// 열번호 초기화
		$rowNum = 2;

		foreach ($result as $key => $value) {
			$sheet->setCellValue('A' . $rowNum, $value['CODE']);
			$sheet->setCellValue('B' . $rowNum, $value['ACCOUNT']);
			$sheet->setCellValue('C' . $rowNum, $value['PRICE']);
			$sheet->setCellValue('D' . $rowNum, $value['RECIPIENT']);
			$sheet->setCellValue('E' . $rowNum, $value['CONTENTS']);
			$rowNum++;
		}

		// 범위 내 여러 열 너비 설정
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle ( "A1:E" . (count($result)+1) )->getBorders()->getInside()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
		$sheet->getStyle ( "A1:E" . (count($result)+1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:E1" )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:E" . (count($result)+1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

		$writer = new Xlsx($spreadsheet);

		$filename = $gets['date'] . '_계좌이체';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	#endregion
}
