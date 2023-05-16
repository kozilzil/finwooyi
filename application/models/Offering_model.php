<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offering_model extends CI_Model {
	public function offering_type_list($params) {
		$sql = "
			SELECT  NO,
					PARENT,
			       	SEQ,
			       	DEPTH,
			       	USE_YN,
			       	TITLE,
			       	IS_INCOME
			  FROM  TB_OFFERING_TYPE
			 WHERE 	IS_DELETE = 'N'
		";

		if (array_key_exists('is-income', $params)) {
			$sql .= "
			   AND 	IS_INCOME = '{$params['is-income']}'
			";
		}

		if (array_key_exists('parent', $params)) {
			$sql .= "
			   AND 	PARENT = {$params['parent']}
			";
		}
		$resultArray = $this->db
			->query($sql)
			->result_Array();

		return $resultArray;
	}

	public function income_register($params) {
		$info = $this->session->userdata('info');

		$sql = "
			INSERT INTO TB_OFFERING_INCOME
			(
			 	WORKER_NO
				,PRICE
				,IS_ONLINE
				,ETC
				,OFFERING_TYPE_NO
				,REG_DATE
				,USER_NO
			)
			VALUES
			(
			 	{$info['NO']}
			 	,{$params['price']}
			 	,'{$params['is-online']}'
			 	,'{$params['etc']}'
			 	,{$params['type']}
			 	,'{$params['reg-date']}'
			 	,{$params['user-no']}
			)
			 ";

		$this->db->query($sql);

		$result = $this->db
			->query('SELECT NO FROM TB_OFFERING_INCOME ORDER BY NO DESC LIMIT 1')
			->row_array();

		return $result;
	}

	public function income_info($params) {
		$sql = "
                SELECT  NO,
                        WORKER_NO,
                        PRICE,
                        IS_ONLINE,
                        ETC,
                        OFFERING_TYPE_NO,
                       	REG_DATE,
                       	USER_NO
                  FROM  TB_OFFERING_INCOME
                 WHERE  IS_DELETE = 'N'
                ";
		if (array_key_exists('no', $params)) {
			$sql .= "
				  AND 	NO = '{$params['no']}'
			";
		}
		$resultArray = $this->db
			->query($sql)
			->row_array();

		return $resultArray;
	}

	public function income_delete($params) {
		$sql = "
                UPDATE 	TB_OFFERING_INCOME
                   SET 	IS_DELETE = 'Y'
                 WHERE 	NO = {$params['no']}
                ";
		$this->db->query($sql);

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function income_update($params) {
		$info = $this->session->userdata('info');
		$now = date("Y-m-d");

		$sql = "
			UPDATE 	TB_OFFERING_INCOME
			   SET	PRICE				= {$params['price']},
			       	IS_ONLINE			= '{$params['is-online']}',
					USER_NO				= '{$params['user-no']}',
			       	ETC					= '{$params['etc']}',
			       	OFFERING_TYPE_NO	= '{$params['offering-type-no']}',
			       	UPDATE_DATE			= '{$now}'
			 WHERE	NO 					= {$params['no']}	
			 ";
		$result = $this->db->query($sql);

		return $result;
	}

	public function offering_type_info($params) {
		$sql = "
			SELECT  T1.NO,
					T1.PARENT,
					T1.SEQ,
					T1.DEPTH,
					T1.USE_YN,
					T1.TITLE,
					T1.IS_INCOME,
					T2.NO AS PARENT_NO,
					T2.PARENT AS PARENT_PARENT,
					T2.SEQ AS PARENT_SEQ,
					T2.DEPTH AS PARENT_DEPTH,
					T2.USE_YN AS PARENT_USE_YN,
					T2.TITLE AS PARENT_TITLE,
					T2.IS_INCOME AS PARENT_IS_INCOME
			  FROM  TB_OFFERING_TYPE AS T1
			INNER JOIN TB_OFFERING_TYPE AS T2 ON T1.PARENT = T2.NO
			 WHERE 	T1.IS_DELETE = 'N'
			   AND 	T1.NO = {$params['no']}
		";
		$resultArray = $this->db
			->query($sql)
			->row_Array();

		return $resultArray;
	}
}
