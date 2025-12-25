<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_model extends CI_Model {
	public function getTargetList($data) {
		$sql = "
				SELECT	YEAR,
						PARENT,
						TITLE,
						IS_INCOME
				  FROM 	TB_OFFERING_TYPE
				 WHERE 	USE_YN = 'Y'
				   AND 	NO = '{$data['type']}';
				";
		$target = $this->db
			->query($sql)
			->row_Array();
		$title = $target['TITLE'];
		$beforeYear = $target["YEAR"] -1;

		$query = "
				SELECT 	*
				  FROM 	TB_OFFERING_TYPE 
				 WHERE 	PARENT = (
								SELECT 	T1.NO
								  FROM 	TB_OFFERING_TYPE AS T1
								INNER JOIN TB_OFFERING_TYPE AS T2 ON T1.PARENT = T2.PARENT AND T1.YEAR = {$beforeYear} AND T1.TITLE = T2.TITLE AND T1.IS_INCOME = T2.IS_INCOME
								 WHERE 	T2.NO = {$target["PARENT"]}
								)
				   AND 	TITLE = '{$target["TITLE"]}'
				   AND 	IS_INCOME = '{$target["IS_INCOME"]}'
		";
		$target_before = $this->db
			->query($query)
			->row_Array();

		$query = "
				SELECT 	USER_NO, ETC
				  FROM 	TB_OFFERING_INCOME
				 WHERE 	REG_DATE LIKE '{$target["YEAR"]}%'
				   AND 	OFFERING_TYPE_NO = {$data['type']}
				   AND 	IS_DELETE = 'N'
				GROUP BY USER_NO, ETC		
		";
		$array = $this->db
			->query($query)
			->result_Array();

		$query = "
				SELECT 	USER_NO, ETC
				  FROM 	TB_OFFERING_INCOME
				 WHERE 	REG_DATE LIKE '{$target_before['YEAR']}%'
				   AND 	OFFERING_TYPE_NO = {$target_before["NO"]}
				   AND 	IS_DELETE = 'N'
				GROUP BY USER_NO, ETC		
		";
		$array_before = $this->db
			->query($query)
			->result_Array();

		$result = [];
		$result = array_merge($result, $array);
		$result = array_merge($result, $array_before);

		// 각 하위 배열을 문자열로 변환하고 중복을 제거
		$uniqueArray = array_unique($result,SORT_REGULAR);
		$uniqueArraySort = [];
		foreach ($uniqueArray as $item) {
			array_push($uniqueArraySort, $item);
		}

		$lastUniqueArray = [];
		for($idx = 0; $idx < count($uniqueArraySort); $idx++) {
			$query = "
				SELECT 	*
				  FROM 	TB_USER
				 WHERE 	NO = '{$uniqueArraySort[$idx]['USER_NO']}'
			";
			$info = $this->db
				->query($query)
				->row_Array();

			$uniqueArraySort[$idx]['NAME'] = $info['NAME'];
			$uniqueArraySort[$idx]['TITLE'] = $title;

			$code = 'A';
			for($codeIdx = 0; $codeIdx < 4-strlen($info['NO']); $codeIdx++) {
				$code .= "0";
			}
			$code .= $info['NO'];
			$code .= "B";
			for($codeIdx = 0; $codeIdx < 4-strlen($data['type']); $codeIdx++) {
				$code .= "0";
			}
			$code .= $data['type'];
			$code .= "C";
			if ($uniqueArraySort[$idx]['ETC'] == "") {
				$code .= "0000";
			} else {
				$query = "
				SELECT 	*
				  FROM 	TB_USER
				 WHERE 	NAME = '{$uniqueArraySort[$idx]['ETC']}'
				";
				$etcInfo = $this->db
					->query($query)
					->row_Array();

				if ($etcInfo == null) {
					$code .= "0000";
					$uniqueArraySort[$idx]['ETC'] = "";
				} else {
					for($codeIdx = 0; $codeIdx < 4-strlen($etcInfo['NO']); $codeIdx++) {
						$code .= "0";
					}
					$code .= $etcInfo['NO'];
				}
			}
			$uniqueArraySort[$idx]['CODE'] = $code;

			if ($idx == 0) {
				array_push($lastUniqueArray, $uniqueArraySort[$idx]);
			} else {
				$isExists = false;
				for($lastIdx = 0; $lastIdx < count($lastUniqueArray); $lastIdx++) {
					if ($lastUniqueArray[$lastIdx]['CODE'] == $uniqueArraySort[$idx]['CODE']) {
						$isExists = true;
					}
				}

				if (!$isExists) {
					array_push($lastUniqueArray, $uniqueArraySort[$idx]);
				}
			}
		}

		return $lastUniqueArray;
	}
}
