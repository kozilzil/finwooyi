<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Search extends MY_Controller {
	public function __construct() {
		parent::__construct();
		if (!$this->loginCheck()) {
			redirect($this->serverUrl.'/account/index');
		}
	}

	#region 총계정원장
	/**
	 * 총계정원장 View
	 */
	public function total() {
		$this->setBeforeAssets();
		$this->setAssets('search', 'total');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.search.total"));
		$this->base_view("/search/total/index");
	}

	/**
	 * 총계정원장 데이터
	 */
	public function total_list() {
		$posts = $this->input->post();

		$params = [
			'start-date' 	=> $posts['startDate'],
			'end-date'		=> $posts['endDate']
		];

		$list = $this->_getTotalData($params);

		$this->load->library('blade');
		$this->blade
			->set_data($list)
			->render('/search/total/data_list');
	}

	/**
	 * 총계정원장 엑셀 다운로드
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function total_excel_download() {
		$gets = $this->input->get();

		$params = [
			'start-date' 	=> $gets['startDate'],
			'end-date'		=> $gets['endDate']
		];

		$result = $this->_getTotalData($params);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// 열번호 초기화
		$rowNum = 2;

		$sheet->setCellValue('A1', '날짜');
		$sheet->setCellValue('C1', '항목');
		$sheet->setCellValue('D1', '수입');
		$sheet->setCellValue('E1', '지출');

		foreach ($result as $key => $value) {
			if(array_key_exists('month-chk', $value)) {
				$sheet->setCellValue('A' . $rowNum, $value['MONTH']);
			}
			if(array_key_exists('day-chk', $value)) {
				$sheet->setCellValue('B' . $rowNum, $value['DAY']);
			}
			$sheet->setCellValue('C' . $rowNum, $value['CHILD_TITLE']);
			if ($value['TYPE'] == 'INCOME') {
				$sheet->setCellValue('D' . $rowNum, $value['PRICE']);
			} else {
				$sheet->setCellValue('E' . $rowNum, $value['PRICE']);
			}
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

		$filename = $gets['startDate'] . '_' . $gets['endDate'] . '_총계정원장';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	/**
	 * 총계정원장 원천데이터
	 * @param $params
	 * @return array
	 */
	function _getTotalData($params) {
		$this->load->model('Search_model');

		$expenseList = $this->Search_model->getTotalListExpense($params, 'total');
		$incomeList = $this->Search_model->getTotalListIncome($params, 'total');
		$list = array_merge($expenseList, $incomeList);

		if (count($list) == 0) {
			return [];
		}

		foreach ((array) $list as $key => $value) {
			$sort['OFFERING_TYPE_NO'][$key] = $value['OFFERING_TYPE_NO'];
			$sort['REG_DATE'][$key] = $value['REG_DATE'];
		}
		array_multisort($sort['REG_DATE'], SORT_ASC, $sort['OFFERING_TYPE_NO'], SORT_ASC, $list);


		$list[0]['month-chk'] = true;
		$list[0]['day-chk'] = true;
		$weeklyIncome = 0;
		$weeklyExpense = 0;
		$monthlyIncome = 0;
		$monthlyExpense = 0;
		$totalIncome = 0;
		$totalExpense = 0;
		$quarterIncome = 0;
		$quarterExpense = 0;

		for($idx=0; $idx<count($list); $idx++) {
			if($idx > 0) {
				// 주계
				if ( $list[$idx-1]['DAY'] != $list[$idx]['DAY'] ) {
					$list[$idx]['day-chk'] = true;
					$list[$idx-1]['weekly-income'] = $weeklyIncome;
					$list[$idx-1]['weekly-expense'] = $weeklyExpense;
					$monthlyIncome += $weeklyIncome;
					$monthlyExpense += $weeklyExpense;
					$totalIncome += $weeklyIncome;
					$totalExpense += $weeklyExpense;
					$list[$idx-1]['total-income'] = $totalIncome;
					$list[$idx-1]['total-expense'] = $totalExpense;
					$weeklyIncome = 0;
					$weeklyExpense = 0;
				}

				// 월계
				if ($list[$idx-1]['MONTH'] != $list[$idx]['MONTH']) {
					$list[$idx]['month-chk'] = true;
					$list[$idx-1]['monthly-income'] = $monthlyIncome;
					$list[$idx-1]['monthly-expense'] = $monthlyExpense;
					$list[$idx-1]['total-income'] = $totalIncome;
					$list[$idx-1]['total-expense'] = $totalExpense;
					$quarterIncome += $monthlyIncome;
					$quarterExpense += $monthlyExpense;
					$monthlyIncome = 0;
					$monthlyExpense = 0;

					if (in_array($list[$idx-1]['MONTH'], [3,6,9,12])) {
						$list[$idx-1]['quarter-income'] = $quarterIncome;
						$list[$idx-1]['quarter-expense'] = $quarterExpense;
						$quarterIncome = 0;
						$quarterExpense = 0;
					}
				}
			}

			if ($list[$idx]['TYPE'] == 'INCOME') {
				$weeklyIncome += $list[$idx]['PRICE'];
			} else {
				$weeklyExpense += $list[$idx]['PRICE'];
			}

			if ($idx+1 == count($list)) {
				if ($monthlyIncome == 0) {
					$monthlyIncome = $weeklyIncome;
				}
				if ($monthlyExpense == 0) {
					$monthlyExpense = $weeklyExpense;
				}
				if ($totalIncome == 0) {
					$totalIncome = $weeklyIncome;
				}
				if ($totalExpense == 0) {
					$totalExpense = $weeklyExpense;
				}
				$list[$idx]['weekly-income'] = $weeklyIncome;
				$list[$idx]['weekly-expense'] = $weeklyExpense;
				$list[$idx]['monthly-income'] = $monthlyIncome;
				$list[$idx]['monthly-expense'] = $monthlyExpense;
				$list[$idx]['total-income'] = $totalIncome;
				$list[$idx]['total-expense'] = $totalExpense;
			}
		}

		return $list;
	}
	#endregion

	#region 수입조회
	/**
	 * 수입조회 view
	 */
	public function income() {
		$this->setBeforeAssets();
		$this->setAssets('search', 'income');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.search.income"));
		$this->base_view("/search/income/index");
	}

	/**
	 * 수입조회 데이터
	 */
	public function income_list() {
		$posts = $this->input->post();

		$params = [
			'start-date' 	=> $posts['startDate'],
			'end-date'		=> $posts['endDate'],
			'type'			=> $posts['type']
		];

		$list = $this->_getIncomeData($params);

		$this->load->library('blade');
		$this->blade
			->set_data($list)
			->render('/search/income/data_list');
	}

	/**
	 * 수입조회 엑셀 다운로드
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function income_excel_download() {
		$gets = $this->input->get();

		$params = [
			'start-date' 	=> $gets['startDate'],
			'end-date'		=> $gets['endDate'],
			'type'			=> $gets['type']
		];

		$result = $this->_getIncomeData($params);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// 열번호 초기화
		$rowNum = 2;

		$sheet->setCellValue('A1', '날짜');
		$sheet->setCellValue('C1', '항목');
		$sheet->setCellValue('D1', '수입');
		$sheet->setCellValue('E1', '지출');

		foreach ($result as $key => $value) {
			if(array_key_exists('month-chk', $value)) {
				$sheet->setCellValue('A' . $rowNum, $value['MONTH']);
			}
			if(array_key_exists('day-chk', $value)) {
				$sheet->setCellValue('B' . $rowNum, $value['DAY']);
			}
			$sheet->setCellValue('C' . $rowNum, $value['CHILD_TITLE']);
			if ($value['TYPE'] == 'INCOME') {
				$sheet->setCellValue('D' . $rowNum, $value['PRICE']);
			} else {
				$sheet->setCellValue('E' . $rowNum, $value['PRICE']);
			}
			$rowNum++;

			if(array_key_exists('monthly-income', $value)) {
				$sheet->setCellValue('A' . $rowNum, '월계');
				$sheet->setCellValue('D' . $rowNum, $value['monthly-income']);
				$sheet->setCellValue('E' . $rowNum, $value['monthly-expense']);
				$rowNum++;
			}
			if(array_key_exists('quarter-income', $value)) {
				$sheet->setCellValue('A' . $rowNum, '분기계');
				$sheet->setCellValue('D' . $rowNum, $value['quarter-income']);
				$sheet->setCellValue('E' . $rowNum, $value['quarter-expense']);
				$rowNum++;
			}
			if(array_key_exists('total-income', $value)) {
				$sheet->setCellValue('A' . $rowNum, '누계');
				$sheet->setCellValue('D' . $rowNum, $value['total-income']);
				$sheet->setCellValue('E' . $rowNum, $value['total-expense']);
				$rowNum++;
			}
		}

		// 범위 내 여러 열 너비 설정
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle ( "A1:E" . ($rowNum-1) )->getBorders()->getInside()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
		$sheet->getStyle ( "A1:E" . ($rowNum-1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:E1" )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:E" . ($rowNum-1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

		$writer = new Xlsx($spreadsheet);

		$filename = $gets['startDate'] . '_' . $gets['endDate'] . '_수입조회';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	/**
	 * 수입조회 원천데이터
	 * @param $params
	 * @return array
	 */
	function _getIncomeData($params) {
		$this->load->model('Search_model');

		$list = [];
		$list = array_merge($list, $this->Search_model->getTotalListIncome($params, 'income'));
		$list = array_merge($list, $this->Search_model->getTotalListExpense($params, 'income'));
		if (count($list) == 0) {
			return [];
		}

		$list[0]['month-chk'] = true;
		$list[0]['day-chk'] = true;
		$weeklyIncome = 0;
		$weeklyExpense = 0;
		$monthlyIncome = 0;
		$monthlyExpense = 0;
		$totalIncome = 0;
		$totalExpense = 0;
		$quarterIncome = 0;
		$quarterExpense = 0;

		for($idx=0; $idx<count($list); $idx++) {
			if($idx > 0) {
				// 주계
				if ( $list[$idx-1]['DAY'] != $list[$idx]['DAY'] ) {
					$list[$idx]['day-chk'] = true;
					$list[$idx-1]['weekly-income'] = $weeklyIncome;
					$list[$idx-1]['weekly-expense'] = $weeklyExpense;
					$monthlyIncome += $weeklyIncome;
					$monthlyExpense += $weeklyExpense;
					$totalIncome += $weeklyIncome;
					$totalExpense += $weeklyExpense;
					$list[$idx-1]['total-income'] = $totalIncome;
					$list[$idx-1]['total-expense'] = $totalExpense;
					$weeklyIncome = 0;
					$weeklyExpense = 0;
				}

				// 월계
				if ($list[$idx-1]['MONTH'] != $list[$idx]['MONTH']) {
					$list[$idx]['month-chk'] = true;
					$list[$idx-1]['monthly-income'] = $monthlyIncome;
					$list[$idx-1]['monthly-expense'] = $monthlyExpense;
					$list[$idx-1]['total-income'] = $totalIncome;
					$list[$idx-1]['total-expense'] = $totalExpense;
					$quarterIncome += $monthlyIncome;
					$quarterExpense += $monthlyExpense;
					$monthlyIncome = 0;
					$monthlyExpense = 0;

					if (in_array($list[$idx-1]['MONTH'], [3,6,9,12])) {
						$list[$idx-1]['quarter-income'] = $quarterIncome;
						$list[$idx-1]['quarter-expense'] = $quarterExpense;
						$quarterIncome = 0;
						$quarterExpense = 0;
					}
				}
			}

			if ($list[$idx]['TYPE'] == 'INCOME') {
				$weeklyIncome += $list[$idx]['PRICE'];
			} else {
				$weeklyExpense += $list[$idx]['PRICE'];
			}

			if ($idx+1 == count($list)) {
				if ($monthlyIncome == 0) {
					$monthlyIncome = $weeklyIncome;
				}
				if ($monthlyExpense == 0) {
					$monthlyExpense = $weeklyExpense;
				}
				if ($totalIncome == 0) {
					$totalIncome = $weeklyIncome;
				}
				if ($totalExpense == 0) {
					$totalExpense = $weeklyExpense;
				}
				$list[$idx]['weekly-income'] = $weeklyIncome;
				$list[$idx]['weekly-expense'] = $weeklyExpense;
				$list[$idx]['monthly-income'] = $monthlyIncome;
				$list[$idx]['monthly-expense'] = $monthlyExpense;
				$list[$idx]['total-income'] = $totalIncome;
				$list[$idx]['total-expense'] = $totalExpense;
			}
		}

		return $list;
	}
	#endregion

	#region 지출조회
	/**
	 * 지출조회 view
	 */
	public function expense() {
		$this->setBeforeAssets();
		$this->setAssets('search', 'expense');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.search.expense"));
		$this->base_view("/search/expense/index");
	}

	/**
	 * 지출조회 데이터
	 */
	public function expense_list() {
		$posts = $this->input->post();

		$params = [
			'start-date' 	=> $posts['startDate'],
			'end-date'		=> $posts['endDate'],
			'type'			=> $posts['type']
		];

		$list = $this->_getExpenseData($params);

		$this->load->library('blade');
		$this->blade
			->set_data($list)
			->render('/search/expense/data_list');
	}

	/**
	 * 지출조회 엑셀 다운로드
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function expense_excel_download() {
		$gets = $this->input->get();

		$params = [
			'start-date' 	=> $gets['startDate'],
			'end-date'		=> $gets['endDate'],
			'type'			=> $gets['type']
		];

		$result = $this->_getExpenseData($params);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// 열번호 초기화
		$rowNum = 2;

		$sheet->setCellValue('A1', '날짜');
		$sheet->setCellValue('C1', '항목');
		$sheet->setCellValue('D1', '수입');
		$sheet->setCellValue('E1', '지출');

		foreach ($result as $key => $value) {
			if(array_key_exists('month-chk', $value)) {
				$sheet->setCellValue('A' . $rowNum, $value['MONTH']);
			}
			if(array_key_exists('day-chk', $value)) {
				$sheet->setCellValue('B' . $rowNum, $value['DAY']);
			}
			$sheet->setCellValue('C' . $rowNum, $value['CONTENTS']);
			if ($value['TYPE'] == 'INCOME') {
				$sheet->setCellValue('D' . $rowNum, $value['PRICE']);
			} else {
				$sheet->setCellValue('E' . $rowNum, $value['PRICE']);
			}
			$rowNum++;

			if(array_key_exists('monthly-income', $value)) {
				$sheet->setCellValue('A' . $rowNum, '월계');
				$sheet->setCellValue('D' . $rowNum, $value['monthly-income']);
				$sheet->setCellValue('E' . $rowNum, $value['monthly-expense']);
				$rowNum++;
			}
			if(array_key_exists('quarter-income', $value)) {
				$sheet->setCellValue('A' . $rowNum, '분기계');
				$sheet->setCellValue('D' . $rowNum, $value['quarter-income']);
				$sheet->setCellValue('E' . $rowNum, $value['quarter-expense']);
				$rowNum++;
			}
			if(array_key_exists('total-income', $value)) {
				$sheet->setCellValue('A' . $rowNum, '누계');
				$sheet->setCellValue('D' . $rowNum, $value['total-income']);
				$sheet->setCellValue('E' . $rowNum, $value['total-expense']);
				$rowNum++;
			}
		}

		// 범위 내 여러 열 너비 설정
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle ( "A1:E" . ($rowNum-1) )->getBorders()->getInside()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
		$sheet->getStyle ( "A1:E" . ($rowNum-1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:E1" )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:E" . ($rowNum-1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

		$writer = new Xlsx($spreadsheet);

		$filename = $gets['startDate'] . '_' . $gets['endDate'] . '_지출조회';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	/**
	 * 지출조회 원천데이터
	 * @param $params
	 * @return array
	 */
	function _getExpenseData($params) {
		$this->load->model('Search_model');

		$list = [];
		$list = array_merge($list, $this->Search_model->getTotalListIncome($params, 'expense'));
		$params['add_group_by'] = ',T1.CONTENTS';
		$list = array_merge($list, $this->Search_model->getTotalListExpense($params, 'expense'));
		if (count($list) == 0) {
			return [];
		}

		$list[0]['month-chk'] = true;
		$list[0]['day-chk'] = true;
		$weeklyIncome = 0;
		$weeklyExpense = 0;
		$monthlyIncome = 0;
		$monthlyExpense = 0;
		$totalIncome = 0;
		$totalExpense = 0;
		$quarterIncome = 0;
		$quarterExpense = 0;

		for($idx=0; $idx<count($list); $idx++) {
			if($idx == 0) {
				$list[$idx]['day-chk'] = true;
				$list[$idx]['weekly-income'] = $weeklyIncome;
				$list[$idx]['weekly-expense'] = $weeklyExpense;
				$monthlyIncome = $weeklyIncome;
				$monthlyExpense = $weeklyExpense;
				$totalIncome = $weeklyIncome;
				$totalExpense = $weeklyExpense;
			} elseif($idx > 0) {
				// 주계
				if ( $list[$idx-1]['DAY'] != $list[$idx]['DAY'] ) {
					$list[$idx]['day-chk'] = true;
					$list[$idx-1]['weekly-income'] = $weeklyIncome;
					$list[$idx-1]['weekly-expense'] = $weeklyExpense;
					$monthlyIncome += $weeklyIncome;
					$monthlyExpense += $weeklyExpense;
					$totalIncome += $weeklyIncome;
					$totalExpense += $weeklyExpense;
					$list[$idx-1]['total-income'] = $totalIncome;
					$list[$idx-1]['total-expense'] = $totalExpense;
					$weeklyIncome = 0;
					$weeklyExpense = 0;
				}
				// 월계
				if ($list[$idx-1]['MONTH'] != $list[$idx]['MONTH']) {
					$list[$idx]['month-chk'] = true;
					$list[$idx-1]['monthly-income'] = $monthlyIncome;
					$list[$idx-1]['monthly-expense'] = $monthlyExpense;
					$list[$idx-1]['total-income'] = $totalIncome;
					$list[$idx-1]['total-expense'] = $totalExpense;
					$quarterIncome += $monthlyIncome;
					$quarterExpense += $monthlyExpense;
					$monthlyIncome = 0;
					$monthlyExpense = 0;

					if (in_array($list[$idx-1]['MONTH'], [3,6,9,12])) {
						$list[$idx-1]['quarter-income'] = $quarterIncome;
						$list[$idx-1]['quarter-expense'] = $quarterExpense;
						$quarterIncome = 0;
						$quarterExpense = 0;
					}
				}
			}

			if ($list[$idx]['TYPE'] == 'INCOME') {
				$weeklyIncome += $list[$idx]['PRICE'];
			} else {
				$weeklyExpense += $list[$idx]['PRICE'];
			}

			if ($idx+1 == count($list)) {
				if ($monthlyIncome == 0) {
					$monthlyIncome = $weeklyIncome;
				} else {
					$monthlyIncome += $weeklyIncome;
				}
				if ($monthlyExpense == 0) {
					$monthlyExpense = $weeklyExpense;
				} else {
					$monthlyExpense += $weeklyExpense;
				}
				if ($totalIncome == 0) {
					$totalIncome = $weeklyIncome;
				} else {
					$totalIncome += $weeklyIncome;
				}
				if ($totalExpense == 0) {
					$totalExpense = $weeklyExpense;
				} else {
					$totalExpense += $weeklyExpense;
				}
				$list[$idx]['weekly-income'] = $weeklyIncome;
				$list[$idx]['weekly-expense'] = $weeklyExpense;
				$list[$idx]['monthly-income'] = $monthlyIncome;
				$list[$idx]['monthly-expense'] = $monthlyExpense;
				$list[$idx]['total-income'] = $totalIncome;
				$list[$idx]['total-expense'] = $totalExpense;
			}
		}

		return $list;
	}
	#endregion

	#region 기부금영수증
	/**
	 * 기부금영수증 View
	 */
	public function donation() {
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
			'base_url' => '/search/donation',
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
		$this->setAssets('search', 'donation');
		$this->setAfterAssets();

		$this->addViewData('data', 'list', $list);
		$this->addViewData('data', 'pagination', $pagination);
		$this->addViewData('data', 'page', $page);
		$this->addViewData('data', 'limit', $limit);
		$this->addViewData('data', 'type', $type);
		$this->addViewData('data', 'content', $content);

		$this->setMenuList();

		$this->setTitle(getenv("title.search.donation"));
		$this->base_view("/search/donation/index");
	}

	/**
	 * 기부금영수증 상세 View
	 */
	public function donation_detail() {
		$userNo = $this->uri->segment(3);
		$year = $this->uri->segment(4);

		$this->load->model('User_model');
		$params = [
			'no' 	=> $userNo,
			'year'	=> $year
		];
		$info = $this->User_model->user_info($params);
		if ($info == null) {
			echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
		}

		$this->load->model('Search_model');
		$list = $this->Search_model->getPersionIncomeList($params);
		if(count($list) != 0) {
			$totalPrice = 0;
			for($idx=0;$idx<count($list);$idx++) {
				$totalPrice += $list[$idx]['PRICE'];
			}
			$list[0]['TOTAL_PRICE'] = $totalPrice;
		}


		$this->setBeforeAssets();
		$this->setAssets('search', 'donation_detail');
		$this->setAfterAssets();

		$this->addViewData('data', 'info', $info);
		$this->addViewData('data', 'list', $list);
		$this->addViewData('data', 'year', $year);

		$this->setMenuList();

		$this->setTitle(getenv("title.search.donation_detail"));
		$this->base_view("/search/donation/detail");
	}
	#endregion

	#region 주계표
	public function weekly_table() {
		$this->setBeforeAssets();
		$this->setAssets('search', 'weekly_table');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.search.weekly_table"));
		$this->base_view("/search/weekly_table/index");
	}

	public function weekly_table_excel_download() {
		$gets = $this->input->get();

		$this->load->model('Income_model');
		$this->load->model('User_model');
		$this->load->model('Expense_model');
		$newDate = date("Y년 m월 d일", strtotime($gets['date']));
		$params = [
			'date' 	=> $gets['date']
		];

		$spreadsheet = IOFactory::load("assets/weekly_table_2023.xlsx");

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('F9', $newDate);

		#region 수입정리
		$incomeArray = [];
		$parentIncomeArray = [];
		$incomeResult = $this->Income_model->coefficient_list($params);
		for($idx = 0; $idx < count($incomeResult); $idx++) {
			if (!array_key_exists($incomeResult[$idx]['OFFERING_TYPE_NO'], $incomeArray)) {
				$incomeArray[$incomeResult[$idx]['OFFERING_TYPE_NO']] = [
					'name'	=> $incomeResult[$idx]['OFFERING_TYPE_NAME'],
					'price'	=> 0
				];
			}
			$incomeArray[$incomeResult[$idx]['OFFERING_TYPE_NO']]['price'] += $incomeResult[$idx]['PRICE'];

			if (!array_key_exists($incomeResult[$idx]['OFFERING_TYPE_PARENT_NO'], $parentIncomeArray)) {
				$parentIncomeArray[$incomeResult[$idx]['OFFERING_TYPE_PARENT_NO']] = [
					'name'	=> $incomeResult[$idx]['OFFERING_TYPE_PARENT_NAME'],
					'price'	=> 0
				];
			}
			$parentIncomeArray[$incomeResult[$idx]['OFFERING_TYPE_PARENT_NO']]['price'] += $incomeResult[$idx]['PRICE'];
		}

		// 수입 상세내역 정리
		foreach ($incomeArray AS $item) {
			switch ($item['name']) {
				case "유치부헌금":
					$sheet->setCellValue('C12', $item['price']);
					break;
				case "유 초등부헌금":
					$sheet->setCellValue('C13', $item['price']);
					break;
				case "중고등부헌금":
					$sheet->setCellValue('C14', $item['price']);
					break;
				case "주일헌금":
					$sheet->setCellValue('C15', $item['price']);
					break;
				case "십일조헌금":
					$sheet->setCellValue('C16', $item['price']);
					break;
				case "감사헌금":
					$sheet->setCellValue('C17', $item['price']);
					break;
				case "에스겔헌금":
					$sheet->setCellValue('C18', $item['price']);
					break;
				case "목장헌금":
					$sheet->setCellValue('C19', $item['price']);
					break;

				case "신년감사헌금":
					$sheet->setCellValue('C22', $item['price']);
					break;
				case "부활절감사헌금":
					$sheet->setCellValue('C23', $item['price']);
					break;
				case "맥추절감사헌금":
					$sheet->setCellValue('C24', $item['price']);
					break;
				case "추수감사헌금":
					$sheet->setCellValue('C25', $item['price']);
					break;
				case "성탄절감사헌금":
					$sheet->setCellValue('C26', $item['price']);
					break;

				case "선교헌금":
					$sheet->setCellValue('C28', $item['price']);
					break;
				case "장학헌금":
					$sheet->setCellValue('C29', $item['price']);
					break;
				case "엎드림헌금":
					$sheet->setCellValue('C30', $item['price']);
					break;
				case "이웃사랑헌금":
					$sheet->setCellValue('C31', $item['price']);
					break;
				case "전도헌금":
					$sheet->setCellValue('C32', $item['price']);
					break;

				case "부흥사경회헌금":
					$sheet->setCellValue('C34', $item['price']);
					break;
				case "세례교인헌금":
					$sheet->setCellValue('C35', $item['price']);
					break;
				case "건축헌금":
					$sheet->setCellValue('C36', $item['price']);
					break;
				case "엘레베이터헌금":
					$sheet->setCellValue('C37', $item['price']);
					break;
				case "차세대헌금":
					$sheet->setCellValue('C38', $item['price']);
					break;
				case "설립기념주일":
					$sheet->setCellValue('C39', $item['price']);
					break;
				case "기타특별헌금":
					$sheet->setCellValue('C40', $item['price']);
					break;
				case "사랑의 헌금":
					$sheet->setCellValue('C41', $item['price']);
					break;

				case "선교헌금(카페)":
					$sheet->setCellValue('C44', $item['price']);
					break;
				case "적금만기수입금":
					$sheet->setCellValue('C45', $item['price']);
					break;
				case "잡수익기타":
					$sheet->setCellValue('C46', $item['price']);
					break;
				case "교육관사택임차보증금":
					$sheet->setCellValue('C47', $item['price']);
					break;
				case "임차보증금환입금":
					$sheet->setCellValue('C48', $item['price']);
					break;

				case "미래사역비환입금":
					$sheet->setCellValue('C52', $item['price']);
					break;
				case "일반적금환입금":
					$sheet->setCellValue('C53', $item['price']);
					break;
				case "은급비환입금":
					$sheet->setCellValue('C54', $item['price']);
					break;
			}
		}

		// 수입 소계정리
		foreach ($parentIncomeArray AS $item) {
			switch ($item['name']) {
				case "정상헌금":
					$sheet->setCellValue('C21', $item['price']);
					break;
				case "절기헌금":
					$sheet->setCellValue('C27', $item['price']);
					break;
				case "선교장학":
					$sheet->setCellValue('C33', $item['price']);
					break;
				case "기타목적헌금":
					$sheet->setCellValue('C43', $item['price']);
					break;
				case "기타수익급":
					$sheet->setCellValue('C51', $item['price']);
					break;
				case "환입금":
					$sheet->setCellValue('C56', $item['price']);
					break;
			}
		}
		#endregion

		#region 지출정리
		$expenseArray = [];
		$parentExpenseArray = [];
		$expenseResult = $this->Expense_model->coefficient_list($params);
		for($idx = 0; $idx < count($expenseResult); $idx++) {
			if (!array_key_exists($expenseResult[$idx]['OFFERING_TYPE_NO'], $expenseArray)) {
				$expenseArray[$expenseResult[$idx]['OFFERING_TYPE_NO']] = [
					'name'	=> $expenseResult[$idx]['OFFERING_TYPE_NAME'],
					'price'	=> 0
				];
			}
			$expenseArray[$expenseResult[$idx]['OFFERING_TYPE_NO']]['price'] += $expenseResult[$idx]['PRICE'];

			if (!array_key_exists($expenseResult[$idx]['OFFERING_TYPE_PARENT_NO'], $parentExpenseArray)) {
				$parentExpenseArray[$expenseResult[$idx]['OFFERING_TYPE_PARENT_NO']] = [
					'name'	=> $expenseResult[$idx]['OFFERING_TYPE_PARENT_NAME'],
					'price'	=> 0
				];
			}
			$parentExpenseArray[$expenseResult[$idx]['OFFERING_TYPE_PARENT_NO']]['price'] += $expenseResult[$idx]['PRICE'];
		}

		// 지출 상세내역 정리
		foreach ($expenseArray AS $item) {
			switch ($item['name']) {
				case "교역자사례비":
					$sheet->setCellValue('G12', $item['price']);
					break;
				case "직원사례비":
					$sheet->setCellValue('G13', $item['price']);
					break;
				case "지휘자보수비":
					$sheet->setCellValue('G14', $item['price']);
					break;

				case "목회활동비":
					$sheet->setCellValue('G16', $item['price']);
					break;
				case "안식활동비":
					$sheet->setCellValue('G17', $item['price']);
					break;
				case "접대비":
					$sheet->setCellValue('G18', $item['price']);
					break;
				case "도서구입비":
					$sheet->setCellValue('G19', $item['price']);
					break;
				case "판공비":
					$sheet->setCellValue('G20', $item['price']);
					break;
				case "인쇄비":
					$sheet->setCellValue('G21', $item['price']);
					break;
				case "세례교인헌금":
					$sheet->setCellValue('G22', $item['price']);
					break;
				case "경조비":
					$sheet->setCellValue('G23', $item['price']);
					break;
				case "교육 및 강사비":
					$sheet->setCellValue('G24', $item['price']);
					break;
				case "구제비":
					$sheet->setCellValue('G25', $item['price']);
					break;
				case "수양(휴가)회비":
					$sheet->setCellValue('G26', $item['price']);
					break;
				case "상회비":
					$sheet->setCellValue('G27', $item['price']);
					break;

				case "부흥사경회비":
					$sheet->setCellValue('G29', $item['price']);
					break;
				case "심방비":
					$sheet->setCellValue('G30', $item['price']);
					break;
				case "미화비":
					$sheet->setCellValue('G31', $item['price']);
					break;
				case "홍보영상비":
					$sheet->setCellValue('G32', $item['price']);
					break;

				case "선교비":
					$sheet->setCellValue('G34', $item['price']);
					break;
				case "교역자장학금":
					$sheet->setCellValue('G35', $item['price']);
					break;
				case "장학금":
					$sheet->setCellValue('G36', $item['price']);
					break;

				case "미자립교회지원":
					$sheet->setCellValue('G38', $item['price']);
					break;
				case "70인전도비":
					$sheet->setCellValue('G39', $item['price']);
					break;
				case "새가족양육비":
					$sheet->setCellValue('G40', $item['price']);
					break;
				case "문서전도비":
					$sheet->setCellValue('G41', $item['price']);
					break;
				case "전도행사비":
					$sheet->setCellValue('G42', $item['price']);
					break;

				case "유치부 교육비":
					$sheet->setCellValue('G44', $item['price']);
					break;
				case "유초등부 교육비":
					$sheet->setCellValue('G45', $item['price']);
					break;
				case "중고등부 교육비":
					$sheet->setCellValue('G46', $item['price']);
					break;
				case "교육위원회비":
					$sheet->setCellValue('G47', $item['price']);
					break;

				case "벧엘찬양대":
					$sheet->setCellValue('G52', $item['price']);
					break;
				case "시온찬양대":
					$sheet->setCellValue('G53', $item['price']);
					break;
				case "주향한찬양대":
					$sheet->setCellValue('G54', $item['price']);
					break;
				case "오케스트라운영비":
					$sheet->setCellValue('G55', $item['price']);
					break;
				case "솔리스트":
					$sheet->setCellValue('G56', $item['price']);
					break;
				case "반주자":
					$sheet->setCellValue('G57', $item['price']);
					break;
				case "음영비":
					$sheet->setCellValue('G58', $item['price']);
					break;

				case "행사비":
					$sheet->setCellValue('G60', $item['price']);
					break;

				case "비품구입비":
					$sheet->setCellValue('G62', $item['price']);
					break;
				case "시설물보수비":
					$sheet->setCellValue('G63', $item['price']);
					break;
				case "엘레베이터설치비":
					$sheet->setCellValue('G64', $item['price']);
					break;
				case "소모품비":
					$sheet->setCellValue('G65', $item['price']);
					break;
				case "공공요금":
					$sheet->setCellValue('G66', $item['price']);
					break;
				case "소유권이전비":
					$sheet->setCellValue('G67', $item['price']);
					break;
				case "카페물품구입비":
					$sheet->setCellValue('G68', $item['price']);
					break;

				case "차량구입비":
					$sheet->setCellValue('G71', $item['price']);
					break;
				case "차량관리비":
					$sheet->setCellValue('G72', $item['price']);
					break;

				case "후생비":
					$sheet->setCellValue('G74', $item['price']);
					break;
				case "교역자주택지원비":
					$sheet->setCellValue('G75', $item['price']);
					break;
				case "교역자자녀교육비":
					$sheet->setCellValue('G76', $item['price']);
					break;
				case "교역자후생비":
					$sheet->setCellValue('G77', $item['price']);
					break;
				case "사택임차보증금":
					$sheet->setCellValue('G78', $item['price']);
					break;
				case "차입금이자":
					$sheet->setCellValue('G79', $item['price']);
					break;
				case "화재보험료":
					$sheet->setCellValue('G80', $item['price']);
					break;
				case "보험료(4대보험)":
					$sheet->setCellValue('G81', $item['price']);
					break;
				case "퇴직금":
					$sheet->setCellValue('G82', $item['price']);
					break;
				case "잡비청소비기타":
					$sheet->setCellValue('G83', $item['price']);
					break;

				case "총회연금":
					$sheet->setCellValue('G87', $item['price']);
					break;
				case "연금":
					$sheet->setCellValue('G88', $item['price']);
					break;
				case "담임목사은급비":
					$sheet->setCellValue('G89', $item['price']);
					break;
				case "일반직원은급비":
					$sheet->setCellValue('G90', $item['price']);
					break;
				case "일반적금":
					$sheet->setCellValue('G91', $item['price']);
					break;
				case "상환적금":
					$sheet->setCellValue('G92', $item['price']);
					break;

				case "일반예비비":
					$sheet->setCellValue('G94', $item['price']);
					break;
				case "미래사역비":
					$sheet->setCellValue('G95', $item['price']);
					break;
			}
		}

		// 지출 소계정리
		foreach ($parentExpenseArray AS $item) {
			switch ($item['name']) {
				case "사례보수비":
					$sheet->setCellValue('G15', $item['price']);
					break;
				case "목회운영비":
					$sheet->setCellValue('G28', $item['price']);
					break;
				case "예배비":
					$sheet->setCellValue('G33', $item['price']);
					break;
				case "선교장학비":
					$sheet->setCellValue('G37', $item['price']);
					break;
				case "전도비":
					$sheet->setCellValue('G43', $item['price']);
					break;
				case "교육비":
					$sheet->setCellValue('G51', $item['price']);
					break;
				case "음영비":
					$sheet->setCellValue('G59', $item['price']);
					break;
				case "행사비":
					$sheet->setCellValue('G61', $item['price']);
					break;
				case "총무비":
					$sheet->setCellValue('G70', $item['price']);
					break;
				case "기타후생비":
					$sheet->setCellValue('G86', $item['price']);
					break;
				case "적금":
					$sheet->setCellValue('G93', $item['price']);
					break;
				case "차량비":
					$sheet->setCellValue('G73', $item['price']);
					break;
				case "예비비":
					$sheet->setCellValue('G96', $item['price']);
					break;
			}
		}
		#endregion

		$writer = new Xlsx($spreadsheet);

		$filename = $gets['date'] . '_주계표';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}
	#endregion
}
