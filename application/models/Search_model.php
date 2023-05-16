<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends CI_Model {
	public function getTotalListIncome($params, $orderType) {
		$sql = "
			SELECT 	T1.OFFERING_TYPE_NO,
					SUM(T1.PRICE) AS PRICE,
			       	T3.TITLE AS PARENT_TITLE,
					T2.TITLE AS CHILD_TITLE,
			       	'INCOME' AS TYPE,
			       	MID(T1.REG_DATE, 6, 2) AS MONTH,
					RIGHT(T1.REG_DATE, 2) AS DAY,
					REG_DATE
			  FROM 	TB_OFFERING_INCOME AS T1
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T2 ON T1.OFFERING_TYPE_NO = T2.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T3 ON T2.PARENT = T3.NO
			 WHERE 	REG_DATE >= '{$params['start-date']}'
			   AND 	REG_DATE <= '{$params['end-date']}'
			   AND 	T1.IS_DELETE = 'N'
		";
		if ( array_key_exists('type', $params) ) {
			$sql .= "
			   AND 	T2.NO = {$params['type']}
			";
		}
		$sql .= "
			GROUP BY T1.REG_DATE, OFFERING_TYPE_NO
		";
		if ( $orderType == 'total' ) {
			$sql .= "
			ORDER BY T1.REG_DATE ASC
			";
		}
		if ( $orderType == 'income' || $orderType == 'expense' ) {
			$sql .= "
			ORDER BY T1.OFFERING_TYPE_NO ASC, T1.REG_DATE ASC
			";
		}
		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}

	public function getTotalListExpense($params, $orderType) {
		$sql = "
			SELECT 	T1.OFFERING_TYPE_NO,
					SUM(T1.PRICE) AS PRICE,
			       	T3.TITLE AS PARENT_TITLE,
					T2.TITLE AS CHILD_TITLE,
			       	'EXPENSE' AS TYPE,
			       	MID(T1.REG_DATE, 6, 2) AS MONTH,
					RIGHT(T1.REG_DATE, 2) AS DAY,
					REG_DATE,
					T1.CONTENTS
			  FROM 	TB_EXPENSE AS T1
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T2 ON T1.OFFERING_TYPE_NO = T2.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T3 ON T2.PARENT = T3.NO
			 WHERE 	REG_DATE >= '{$params['start-date']}'
			   AND 	REG_DATE <= '{$params['end-date']}'
			   AND 	T1.IS_DELETE = 'N'
		";
		if ( array_key_exists('type', $params) ) {
			$sql .= "
			   AND 	T2.NO = {$params['type']}
			";
		}
		$sql .= "
			GROUP BY T1.REG_DATE, OFFERING_TYPE_NO
		";
		if ( array_key_exists('add_group_by', $params)) {
			$sql .= $params['add_group_by'];
		}
		if ( $orderType == 'total' ) {
			$sql .= "
			ORDER BY T1.REG_DATE ASC
			";
		}
		if ( $orderType == 'income' || $orderType == 'expense' ) {
			$sql .= "
			ORDER BY T1.OFFERING_TYPE_NO ASC, T1.REG_DATE ASC
			";
		}

		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}

	public function getPersionIncomeList($params) {
		$sql = "
			SELECT 	T1.OFFERING_TYPE_NO,
					T1.PRICE,
			       	T3.TITLE AS PARENT_TITLE,
					T2.TITLE AS CHILD_TITLE,
			       	'INCOME' AS TYPE,
			       	MID(T1.REG_DATE, 6, 2) AS MONTH,
					RIGHT(T1.REG_DATE, 2) AS DAY,
					REG_DATE
			  FROM 	TB_OFFERING_INCOME AS T1
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T2 ON T1.OFFERING_TYPE_NO = T2.NO
			LEFT OUTER JOIN TB_OFFERING_TYPE AS T3 ON T2.PARENT = T3.NO
			 WHERE 	LEFT(REG_DATE, 4) = '{$params['year']}'
			   AND 	T1.IS_DELETE = 'N'
			   AND 	T1.USER_NO = '{$params['no']}'
			ORDER BY T3.SEQ DESC, T2.SEQ DESC, T1.NO DESC
		";

		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}
}
