<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Barcode extends MY_Controller {
	public function check() {
		$this->setBeforeAssets();
		$this->setAssets('barcode', 'check');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.barcode.check"));
		$this->base_view("/barcode/check/index");
	}

	public function auto() {
		$this->setBeforeAssets();
		$this->setAssets('barcode', 'auto');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.barcode.auto"));
		$this->base_view("/barcode/auto/index");
	}

	public function auto_excel_download() {
		$gets = $this->input->get();
		$year = $gets['year'];
		$title = "";

		$this->load->model('Barcode_model');
		$result = $this->Barcode_model->getTargetList(['type'	=> $gets['type']]);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// 열번호 초기화
		$rowNum = 2;

		$sheet->setCellValue('A1', '헌금이름');
		$sheet->setCellValue('B1', '헌금자');
		$sheet->setCellValue('C1', '공동헌금자');
		$sheet->setCellValue('D1', '헌금자코드');

		foreach ($result as $key => $value) {
			if ($key == 0 ){
				$title = $value['TITLE'];
			}
			$sheet->setCellValue('A' . $rowNum, $value['TITLE']);
			$sheet->setCellValue('B' . $rowNum, $value['NAME']);
			$sheet->setCellValue('C' . $rowNum, $value['ETC']);
			$sheet->setCellValue('D' . $rowNum, $value['CODE']);
			$rowNum++;
		}

		// 범위 내 여러 열 너비 설정
		$sheet->getColumnDimension('A')->setWidth(16);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle ( "A1:D" . (count($result)+1) )->getBorders()->getInside()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN );
		$sheet->getStyle ( "A1:D" . (count($result)+1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:D1" )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
		$sheet->getStyle ( "A1:D" . (count($result)+1) )->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

		$writer = new Xlsx($spreadsheet);

		$filename = $year . '_' . $title . date('Ymd');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	public function manual() {
		$this->setBeforeAssets();
		$this->setAssets('barcode', 'manual');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.barcode.manual"));
		$this->base_view("/barcode/manual/index");
	}
}
