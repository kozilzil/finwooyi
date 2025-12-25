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

		$list = $this->_getTotalData($params, 'list');

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

		$result = $this->_getTotalData($params, 'excel');

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

		$filename = substr($gets['startDate'],0, 4) . '_총계정원장';

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
	function _getTotalData($params, $type) {
		$this->load->model('Search_model');

		$expenseList = $this->Search_model->getTotalListExpense($params, 'total');
		$incomeList = $this->Search_model->getTotalListIncome($params, 'total');
		$list = array_merge($expenseList, $incomeList);

		if (count($list) !== 0) {
			foreach ((array) $list as $key => $value) {
				$sort['OFFERING_TYPE_NO'][$key] = $value['OFFERING_TYPE_NO'];
				$sort['REG_DATE'][$key] = $value['REG_DATE'];
				$sort['TYPE'][$key] = $value['TYPE'];
				$sort['PARENT_SEQ'][$key] = $value['PARENT_SEQ'];
				$sort['CHILD_SEQ'][$key] = $value['CHILD_SEQ'];
			}
			array_multisort($sort['REG_DATE'], SORT_ASC, $sort['TYPE'], SORT_DESC, $sort['CHILD_SEQ'], SORT_ASC, $sort['PARENT_SEQ'], SORT_ASC, $list);
		}

		if ($type == 'list') {
			$params['year'] = substr($params['start-date'], 0, 4);
			$carryoverList = $this->Search_model->getCarryover($params);
			if ($carryoverList != null) {
				array_unshift($list, ['REG_DATE' => $params['year'].'-01-01', 'MONTH' => "01", 'DAY' => "01", 'TYPE' => 'INCOME', 'PRICE' => $carryoverList['CARRYOVER_PRE'], 'CHILD_TITLE' => '전년도 이월금']);
				array_push($list, ['MONTH' => "12", 'DAY' => "31", 'TYPE' => 'EXPENSE', 'PRICE' => $carryoverList['CARRYOVER_NEXT'], 'CHILD_TITLE' => '차년도 이월금']);
			}
		}
		if ( $list == []) {
			return $list;
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
					$quarterIncome += $weeklyIncome;
					$quarterExpense += $weeklyExpense;
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
				$monthlyIncome += $weeklyIncome;
				$monthlyExpense += $weeklyExpense;
				$totalIncome += $weeklyIncome;
				$totalExpense += $weeklyExpense;

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

	public function carrayover_update() {
		$posts = $this->input->post();
		$params = [
			'year'	=> $posts['year'],
			'pre'	=> $posts['pre'] ?? 0,
			'next'	=> $posts['next'] ?? 0
		];
		$this->load->model('Search_model');
		$data = $this->Search_model->getCarryover($params);
		if( $data === null ) {
			$this->Search_model->insertCarryover($params);
		} else {
			$this->Search_model->updateCarryover($params);
		}
		echo json_encode(['status' => true, 'message' => '처리되었습니다.']);
	}

	public function carrayover_data() {
		$posts = $this->input->post();
		$params = [
			'year'	=> $posts['year']
		];
		$this->load->model('Search_model');
		$data = $this->Search_model->getCarryover($params);
		if ($data === null) {
			$data['YEAR'] = $posts['year'];
			$data['CARRYOVER_PRE'] = 0;
			$data['CARRYOVER_NEXT'] = 0;
		}
		echo json_encode(['data' => $data]);
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

		$list = [];
		if (array_key_exists('type', $posts)) {
			$params = [
				'start-date' 	=> $posts['startDate'],
				'end-date'		=> $posts['endDate'],
				'type'			=> $posts['type']
			];

			$list = $this->_getIncomeData($params);
		}

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
					$quarterIncome += $weeklyIncome;
					$quarterExpense += $weeklyExpense;
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
				$monthlyIncome += $weeklyIncome;
				$monthlyExpense += $weeklyExpense;
				$totalIncome += $weeklyIncome;
				$totalExpense += $weeklyExpense;

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

		$list = [];
		if (array_key_exists('type', $posts)) {
			$params = [
				'start-date' 	=> $posts['startDate'],
				'end-date'		=> $posts['endDate'],
				'type'			=> $posts['type']
			];

			$list = $this->_getExpenseData($params);
		}

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
					$quarterIncome += $weeklyIncome;
					$quarterExpense += $weeklyExpense;
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
				$monthlyIncome += $weeklyIncome;
				$monthlyExpense += $weeklyExpense;
				$totalIncome += $weeklyIncome;
				$totalExpense += $weeklyExpense;

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
		$year = substr($gets['date'],0,4);

		$this->load->model('Income_model');
		$this->load->model('User_model');
		$this->load->model('Expense_model');

		$keyValue = [];

		try {
			$filename = "assets/excel/weekly_table/weekly_table.xlsx";
			// 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
			$objReader = IOFactory::createReaderForFile($filename);
			// 읽기전용으로 설정
			$objReader->setReadDataOnly(true);
			// 엑셀파일을 읽는다
			$objExcel = $objReader->load($filename);
			// 첫번째 시트를 선택
			$objExcel->setActiveSheetIndex(0);
			$objWorksheet = $objExcel->getActiveSheet();
			$rowIterator = $objWorksheet->getRowIterator();

			foreach ($rowIterator as $row) { // 모든 행에 대해서
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);
			}

			$maxRow = $objWorksheet->getHighestRow();

			for ($i = 2 ; $i <= $maxRow ; $i++) {
				$loadYear = $objWorksheet->getCell('A' . $i)->getValue(); // A열

				if ($year == $loadYear) {
					$key = $objWorksheet->getCell('B' . $i)->getValue(); // B열
					$address = $objWorksheet->getCell('C' . $i)->getValue(); // C열
					$keyValue[$key] = $address;
				}
			}
		}
		catch (exception $e) {
			echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';exit;
		}


		$spreadsheet = IOFactory::load("assets/excel/weekly_table/".$year.".xlsx");

		$sheet = $spreadsheet->getActiveSheet();
		$dateArray = getenv('mapping.weekly_table_excel.date');
		$result = explode('/', $dateArray);
		$newDate = date($result[1], strtotime($gets['date']));

		$sheet->setCellValue('F9', $newDate);

		#region 수입정리
		$incomeArray = [];
		$parentIncomeArray = [];
		$params = [
			'date' 	=> $gets['date']
		];
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
		foreach ($incomeArray as $key => $item) {
			$sheet->setCellValue($keyValue[$key], $item['price']);
		}

		// 수입 소계정리
		foreach ($parentIncomeArray as $key => $item) {
			if (!empty($keyValue[$key])) {
				$sheet->setCellValue($keyValue[$key], $item['price']);
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
		foreach ($expenseArray as $key => $item) {
			$sheet->setCellValue($keyValue[$key], $item['price']);
		}

		// 지출 소계정리
		foreach ($parentExpenseArray as $key => $item) {
			if (!empty($keyValue[$key])) {
				$sheet->setCellValue($keyValue[$key], $item['price']);
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
