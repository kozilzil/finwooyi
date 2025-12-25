<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_model extends CI_Model {
	public function income_list($params) {
		$info = $this->session->userdata('info');

		$sql = "
			SELECT	T1.NO,
					T1.WORKER_NO,
					T1.PRICE,
					T1.IS_ONLINE,
					T1.ETC,
					T1.REG_DATE,
			       	T1.CREATE_DATE,
			       	T1.UPDATE_DATE,
					T1.USER_NO,
					T2.NAME AS WORKER_NAME,
					T3.NAME AS USER_NAME,
			       	T4.NO AS OFFERING_TYPE_NO,
			       	T4.TITLE AS OFFERING_TYPE_NAME,
			       	T5.NO AS OFFERING_TYPE_PARENT_NO,
					T5.TITLE AS OFFERING_TYPE_PARENT_NAME
			  FROM  TB_OFFERING_INCOME AS T1
			LEFT OUTER JOIN TB_USER AS T2 ON T1.WORKER_NO = T2.NO
			LEFT OUTER JOIN TB_USER AS T3 ON T1.USER_NO = T3.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T4 ON T1.OFFERING_TYPE_NO = T4.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T5 ON T4.PARENT = T5.NO
			 WHERE  T1.IS_DELETE = 'N'
			   AND 	T1.WORKER_NO = '{$info['NO']}' 
		";
		if (array_key_exists('start-date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE >= '{$params['reg-date']}'
			";
		}
		if (array_key_exists('end-date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE <= '{$params['end-date']}'
			";
		}
		if (array_key_exists('date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE = '{$params['date']}'
			";
		}
		$sql .= "
			ORDER BY T1.REG_DATE DESC, T1.NO DESC
			LIMIT {$params['limit']} 
                ";
		$offset = ($params['page']-1) * $params['limit'];
		if ($offset != 0) {
			$sql .= "
			OFFSET {$offset}
            ";
		}

		$resultArray = $this->db
			->query($sql)
			->result_Array();
		if ($resultArray == null) {
			$resultArray[0] = [];
		}

		$sql = "SELECT  COUNT(*) AS TOTAL_CNT,
       					SUM(PRICE) AS TOTAL_PRICE
                  FROM  TB_OFFERING_INCOME
                 WHERE  IS_DELETE = 'N'
				   AND 	WORKER_NO = '{$info['NO']}'
        ";
		if (array_key_exists('start-date', $params)) {
			$sql .= "
			   AND 	REG_DATE >= '{$params['reg-date']}'
			";
		}
		if (array_key_exists('end-date', $params)) {
			$sql .= "
			   AND 	REG_DATE <= '{$params['end-date']}'
			";
		}
		if (array_key_exists('date', $params)) {
			$sql .= "
			   AND 	REG_DATE = '{$params['date']}'
			";
		}

		$countArray = $this->db
			->query($sql)
			->row_array();

		$resultArray[0]['TOTAL_CNT'] = $countArray['TOTAL_CNT'];
		$resultArray[0]['TOTAL_PRICE'] = $countArray['TOTAL_PRICE'];

		return $resultArray;
	}

	public function coefficient_list($params) {
		$sql = "
			SELECT	T1.WORKER_NO,
					T1.PRICE,
					T1.IS_ONLINE,
					T1.USER_NO,
					T2.NAME AS WORKER_NAME,
					T3.NAME AS USER_NAME,
			       	T4.NO AS OFFERING_TYPE_NO,
			       	T4.TITLE AS OFFERING_TYPE_NAME,
			       	T5.NO AS OFFERING_TYPE_PARENT_NO,
					T5.TITLE AS OFFERING_TYPE_PARENT_NAME
			  FROM  TB_OFFERING_INCOME AS T1
			LEFT OUTER JOIN TB_USER AS T2 ON T1.WORKER_NO = T2.NO
			LEFT OUTER JOIN TB_USER AS T3 ON T1.USER_NO = T3.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T4 ON T1.OFFERING_TYPE_NO = T4.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T5 ON T4.PARENT = T5.NO
			 WHERE  T1.IS_DELETE = 'N'
		";
		if (array_key_exists('start-date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE >= '{$params['reg-date']}'
			";
		}
		if (array_key_exists('end-date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE <= '{$params['end-date']}'
			";
		}
		if (array_key_exists('date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE = '{$params['date']}'
			";
		}
		$sql .= "
			ORDER BY T1.REG_DATE DESC, T4.NO ASC
                ";

		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}

	public function registrants_list($params) {
		$sql = "
			SELECT	T3.NAME AS USER_NAME,
			       	T1.PRICE AS PRICE,
			       	T4.NO AS OFFERING_TYPE_NO,
			       	T4.TITLE AS OFFERING_TYPE_NAME,
			       	T1.ETC,
			       	T1.IS_ONLINE
			  FROM  TB_OFFERING_INCOME AS T1
			LEFT OUTER JOIN TB_USER AS T3 ON T1.USER_NO = T3.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T4 ON T1.OFFERING_TYPE_NO = T4.NO
			 WHERE  T1.IS_DELETE = 'N'
		";
		if (array_key_exists('start-date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE >= '{$params['reg-date']}'
			";
		}
		if (array_key_exists('end-date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE <= '{$params['end-date']}'
			";
		}
		if (array_key_exists('date', $params)) {
			$sql .= "
			   AND 	T1.REG_DATE = '{$params['date']}'
			";
		}
		$sql .= "
			ORDER BY T1.REG_DATE DESC, T4.NO ASC, T3.NAME ASC
                ";

		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}
}
