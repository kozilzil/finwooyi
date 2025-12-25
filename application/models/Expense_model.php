<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model {
	/**
	 * 은행 리스트 반환
	 * @return mixed
	 */
	public function bankList() {
		$sql = "
			SELECT  NO,
			       	CODE,
			       	NAME
			  FROM  TB_BANK_CODE
		";
		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}

	public function insertAccount($params) {
		$sql = "
			INSERT INTO TB_ACCOUNT
			(
			 	NICK_NAME,
			 	HOLDER,
			 	BANK_NO,
			 	ACCOUNT
			)
			VALUES
			(
			 	'{$params['nickname']}',
			 	'{$params['holder']}',
			 	'{$params['bank']}',
			 	'{$params['number']}'
			)
		";
		$this->db->query($sql);
	}

	public function accountList($params) {
		$sql = "
			SELECT  T1.NO,
			       	T1.NICK_NAME,
			       	T1.HOLDER,
			       	T1.ACCOUNT,
			       	T2.CODE,
			       	T2.NAME
			  FROM 	TB_ACCOUNT AS T1 
			INNER JOIN TB_BANK_CODE AS T2 ON T1.BANK_NO = T2.CODE
			 WHERE 	T1.IS_DELETE = 'N'
		";
		if ($params['nickname'] != '') {
			$sql .= "
			   AND 	T1.NICK_NAME LIKE '{$params['nickname']}%'
			";
		}
		$sql .="
			ORDER BY T1.NO DESC
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

		$sql = "SELECT  COUNT(*) AS TOTAL_CNT
                  FROM  TB_ACCOUNT
				 WHERE 	IS_DELETE = 'N'
        ";
		if ($params['nickname'] != '') {
			$sql .= "
			       AND 	NICK_NAME LIKE '{$params['nickname']}%'
			";
		}

		$countArray = $this->db
			->query($sql)
			->row_array();

		$resultArray[0]['TOTAL_CNT'] = $countArray['TOTAL_CNT'];

		return $resultArray;
	}

	public function register($params) {
		$sql = "
			INSERT INTO TB_EXPENSE
			(
			 	OFFERING_TYPE_NO,
			 	PRICE,
			 	CONTENTS,
			 	PAYMETHOD,
			 	REG_DATE,
			 	ACCOUNT_NO,
			 	RECIPIENT
			)
			VALUES
			(
			 	{$params['type']},
			 	{$params['price']},
			 	'{$params['contents']}',
			 	'{$params['pay-method']}',
			 	'{$params['reg-date']}',
			 	'{$params['account-no']}',
			 	'{$params['recipient']}'
			)
		";
		$this->db->query($sql);
	}

	public function expenseList($params) {
		$sql = "
			SELECT	T1.NO,
					T1.PRICE,
					T1.PAYMETHOD,
					T1.CONTENTS,
					T1.REG_DATE,
					T1.ACCOUNT_NO,
			       	T1.RECIPIENT,
					T2.NO AS OFFERING_TYPE_NO,
			       	T2.TITLE AS OFFERING_TYPE_NAME,
			       	T3.NO AS OFFERING_TYPE_PARENT_NO,
					T3.TITLE AS OFFERING_TYPE_PARENT_NAME,
					T4.NICK_NAME,
					T4.HOLDER,
					T4.ACCOUNT,
					T5.NAME AS BANK_NAME
			  FROM  TB_EXPENSE AS T1
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T2 ON T1.OFFERING_TYPE_NO = T2.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T3 ON T2.PARENT = T3.NO
			LEFT OUTER JOIN TB_ACCOUNT AS T4 ON T1.ACCOUNT_NO = T4.NO AND T4.IS_DELETE = 'N'
			LEFT OUTER JOIN TB_BANK_CODE AS T5 ON T4.BANK_NO = T5.CODE
			 WHERE  T1.IS_DELETE = 'N'
		";
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
                  FROM  TB_EXPENSE
                 WHERE  IS_DELETE = 'N'
        ";
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

	public function expenseDelete($no) {
		$sql = "UPDATE	TB_EXPENSE
				   SET 	IS_DELETE = 'Y'
                 WHERE  NO = {$no}
        ";

		$this->db->query($sql);
	}

	public function expenseUpdate($params) {
		$sql = "UPDATE	TB_EXPENSE
				   SET 	OFFERING_TYPE_NO 	= {$params['offeringTypeNo']},
				       	PRICE 				= {$params['price']},
				       	CONTENTS			= '{$params['contents']}',
				       	RECIPIENT			= '{$params['recipient']}'
                 WHERE  NO 					= {$params['no']}
        ";

		$this->db->query($sql);
	}

	public function getAccountCnt($no) {
		$sql = "SELECT	COUNT(*) AS CNT
				  FROM 	TB_EXPENSE				   
                 WHERE  ACCOUNT_NO = {$no}
				   AND 	IS_DELETE = 'N'
        ";

		$result = $this->db
			->query($sql)
			->row_array();
		return $result['CNT'];
	}

	public function deleteAccount($no) {
		$sql = "UPDATE	TB_ACCOUNT
				   SET	IS_DELETE = 'Y'				   
                 WHERE 	NO = {$no}
        ";

		$this->db->query($sql);
	}

	public function coefficient_list($params) {
		$sql = "
			SELECT	T1.NO,
					SUM(T1.PRICE) AS PRICE,
					T1.PAYMETHOD,
					T2.NO AS OFFERING_TYPE_NO,
					T2.TITLE AS OFFERING_TYPE_NAME,
					T3.NO AS OFFERING_TYPE_PARENT_NO,
					T3.TITLE AS OFFERING_TYPE_PARENT_NAME
			  FROM  TB_EXPENSE AS T1
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T2 ON T1.OFFERING_TYPE_NO = T2.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T3 ON T2.PARENT = T3.NO
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
			GROUP BY 	PAYMETHOD,
						OFFERING_TYPE_NO,
						OFFERING_TYPE_NAME,
						OFFERING_TYPE_PARENT_NO,
						OFFERING_TYPE_PARENT_NAME
			ORDER BY T1.REG_DATE DESC, T3.SEQ ASC, T2.SEQ ASC
                ";

		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}

	public function registrants_list($params) {
		$sql = "
			SELECT	T1.NO,
					T1.PRICE,
					T1.CONTENTS,
			       	T1.RECIPIENT,
					T2.ACCOUNT,
					T3.CODE,
			       	T3.NAME
			  FROM  TB_EXPENSE AS T1
			INNER JOIN TB_ACCOUNT AS T2 ON T1.ACCOUNT_NO = T2.NO
			INNER JOIN TB_BANK_CODE AS T3 ON T2.BANK_NO = T3.CODE
			 WHERE  T1.IS_DELETE = 'N'
			   AND 	T1.PAYMETHOD = 'bank'
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
			ORDER BY T1.REG_DATE DESC
                ";

		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}

	public function fixedList($params) {
		$sql = "
			SELECT	T1.NO,
					T1.PRICE,
					T1.PAYMETHOD,
					T1.CONTENTS,
					T1.ACCOUNT_NO,
			       	T1.RECIPIENT,
			       	T1.WEEKLY,
					T2.NO AS OFFERING_TYPE_NO,
			       	T2.TITLE AS OFFERING_TYPE_NAME,
			       	T3.NO AS OFFERING_TYPE_PARENT_NO,
					T3.TITLE AS OFFERING_TYPE_PARENT_NAME,
					T4.NICK_NAME,
					T4.HOLDER,
					T4.ACCOUNT,
					T5.NAME AS BANK_NAME
			  FROM  TB_EXPENSE_FIXED AS T1
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T2 ON T1.OFFERING_TYPE_NO = T2.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T3 ON T2.PARENT = T3.NO
			LEFT OUTER JOIN TB_ACCOUNT AS T4 ON T1.ACCOUNT_NO = T4.NO AND T4.IS_DELETE = 'N'
			LEFT OUTER JOIN TB_BANK_CODE AS T5 ON T4.BANK_NO = T5.CODE
			 WHERE  T1.IS_DELETE = 'N'
		";
		if ($params['contents'] != '') {
			$sql .= "
			   AND	T1.CONTENTS LIKE '%{$params['contents']}%'
			";
		}
		if ($params['weekly'] != '') {
			$sql .= "
			   AND	T1.WEEKLY = '{$params['weekly']}'
			";
		}
		if ($params['year'] != '') {
			$sql .= "
			   AND	T1.YEAR = '{$params['year']}'
			";
		}
		// $weekly
		$sql .= "
			ORDER BY T1.NO DESC
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

		$sql = "SELECT  COUNT(*) AS TOTAL_CNT
                  FROM  TB_EXPENSE_FIXED
                 WHERE  IS_DELETE = 'N'
        ";
		if ($params['contents'] != '') {
			$sql .= "
			   AND	CONTENTS LIKE '%{$params['contents']}%'
			";
		}
		if ($params['weekly'] != '') {
			$sql .= "
			   AND	WEEKLY = '{$params['weekly']}'
			";
		}
		if ($params['year'] != '') {
			$sql .= "
			   AND	YEAR = '{$params['year']}'
			";
		}

		$countArray = $this->db
			->query($sql)
			->row_array();

		$resultArray[0]['TOTAL_CNT'] = $countArray['TOTAL_CNT'];

		return $resultArray;
	}

	public function fixed_register($params) {
		$sql = "
			INSERT INTO TB_EXPENSE_FIXED
			(
			 	OFFERING_TYPE_NO,
			 	PRICE,
			 	CONTENTS,
			 	PAYMETHOD,
			 	ACCOUNT_NO,
			 	RECIPIENT,
			 	WEEKLY,
			 	YEAR
			)
			VALUES
			(
			 	{$params['type']},
			 	{$params['price']},
			 	'{$params['contents']}',
			 	'{$params['pay-method']}',
			 	'{$params['account-no']}',
			 	'{$params['recipient']}',
			 	'{$params['weekly']}',
			 	'{$params['year']}'
			)
		";
		$this->db->query($sql);
	}

	public function fixedDelete($no) {
		$sql = "UPDATE	TB_EXPENSE_FIXED
				   SET 	IS_DELETE = 'Y'
                 WHERE  NO = {$no}
        ";

		$this->db->query($sql);
	}

	public function fixedUpdate($params) {
		$sql = "UPDATE	TB_EXPENSE_FIXED
				   SET 	OFFERING_TYPE_NO 	= {$params['offeringTypeNo']},
				       	PRICE 				= {$params['price']},
				       	CONTENTS			= '{$params['contents']}',
				       	RECIPIENT			= '{$params['recipient']}',
				        WEEKLY 				= {$params['weekly']}
                 WHERE  NO 					= {$params['no']}
        ";

		$this->db->query($sql);
	}
}
