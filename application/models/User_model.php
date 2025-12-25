<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
	public function menu_list($param) {
		$sql = "
			SELECT  NO,
			       	PARENT,
			       	SEQ,
			       	DEPTH,
			       	USE_YN,
			       	TITLE,
			       	URL,
			       	CLASS,
			       	TYPE
			  FROM  TB_MENU
			 WHERE	USE_YN = 'Y'
			  AND 	PARENT = {$param['parent']}
			ORDER BY SEQ ASC
		";

		$result = $this->db
			->query($sql)  
			->result_Array();

		return $result;
	}
	public function auth_list($param) {
		$sql = "
			SELECT  NO,
			       	MENU_NO
			  FROM  TB_MENU_AUTH
			 WHERE	USER_NO = {$param['no']}
		";

		$result = $this->db
			->query($sql)
			->result_Array();

		return $result;
	}

	/**
	 * 사용자 리스트 반환
	 * @param $params
	 * @return mixed
	 */
	public function user_list($params) {
		#region 리스트
		$sql = "
			SELECT  NO,
					ID,
					NAME,
					OFFICE,
					REG_DATE,
					EXPLANATION,
					IS_DELETE
			  FROM  TB_USER
			 WHERE 	IS_DELETE = 'N'
		";
		if ($params['content'] != '') {
			if ($params['type'] == 'name') {
				$sql .= "
			   AND  NAME LIKE '%{$params['content']}%'
                ";
			} else if ($params['type'] == 'id') {
				$sql .= "
			   AND  ID LIKE '%{$params['content']}%'
                ";
			} else {
				$sql .= "
			   AND  (
					ID LIKE '%{$params['content']}%' 
					OR 
					NAME LIKE '%{$params['content']}%'
					)
            ";
			}
		}

		$sql .= "
			ORDER BY IS_DELETE, NO DESC
			LIMIT {$params['limit']} 
                ";
		$offset = ($params['page']-1) * $params['limit'];
		if ($offset != 0) {
			$sql .= "
			OFFSET {$offset}
            ";
		}
		#endregion
		$resultArray = $this->db
			->query($sql)
			->result_Array();
		if ($resultArray == null) {
			$resultArray[0] = [];
		}

		#region 개수
		$sql = "SELECT  COUNT(*) AS TOTAL_CNT
                  FROM  TB_USER
                 WHERE  IS_DELETE = 'N'
        ";
		if ($params['content'] != '') {
			if ($params['type'] == 'name') {
				$sql .= "
			   AND  NAME LIKE '%{$params['content']}%'
                ";
			} else if ($params['type'] == 'id') {
				$sql .= "
			   AND  ID LIKE '%{$params['content']}%'
                ";
			} else {
				$sql .= "
			   AND  (
					ID LIKE '%{$params['content']}%' 
					OR 
					NAME LIKE '%{$params['content']}%'
					)
            ";
			}
		}
		#endregion
		$countArray = $this->db
			->query($sql)
			->row_array();

		$resultArray[0]['TOTAL_CNT'] = $countArray['TOTAL_CNT'];

		return $resultArray;
	}

	public function user_list_for_register($params) {
		#region 리스트
		$sql = "
			SELECT  NO,
					ID,
					NAME,
					OFFICE,
					REG_DATE,
					EXPLANATION,
					IS_DELETE
			  FROM  TB_USER
			 WHERE 	IS_DELETE = 'N'
			   AND  (NAME = '{$params['content']}' OR NAME REGEXP '{$params['content']}[A-Z]+')
		";

		$sql .= "
			ORDER BY IS_DELETE, NO DESC
			LIMIT {$params['limit']} 
                ";
		$offset = ($params['page']-1) * $params['limit'];
		if ($offset != 0) {
			$sql .= "
			OFFSET {$offset}
            ";
		}
		#endregion
		$resultArray = $this->db
			->query($sql)
			->result_Array();
		if ($resultArray == null) {
			$resultArray[0] = [];
		}

		#region 개수
		$sql = "SELECT  COUNT(*) AS TOTAL_CNT
                  FROM  TB_USER
                 WHERE  IS_DELETE = 'N'
				   AND  (NAME = '{$params['content']}' OR NAME REGEXP '{$params['content']}[A-Z]+')
        ";
		#endregion
		$countArray = $this->db
			->query($sql)
			->row_array();

		$resultArray[0]['TOTAL_CNT'] = $countArray['TOTAL_CNT'];

		return $resultArray;
	}

	/**
	 * 사용자 정보 반환
	 * @param $params
	 * @return mixed
	 */
	public function user_info($params) {
		$sql = "
                SELECT  NO,
                        ID,
                        NAME,
                        OFFICE,
                        REG_DATE,
                        EXPLANATION,
                        IS_DELETE
                  FROM  TB_USER
                 WHERE  IS_DELETE = 'N'
                ";
		if (array_key_exists('no', $params)) {
			$sql .= "
				  AND 	NO = '{$params['no']}'
			";
		}
		if (array_key_exists('id', $params)) {
			$sql .= "
				  AND 	ID = '{$params['id']}'
			";
		}
		if (array_key_exists('name', $params)) {
			$sql .= "
				  AND 	NAME = '{$params['name']}'
			";
		}
		$resultArray = $this->db
			->query($sql)
			->row_array();

		return $resultArray;
	}

	/**
	 * 사용자 등록
	 * @param $params
	 * @return mixed
	 */
	public function user_register($params) {
		$sql = "
			INSERT INTO TB_USER
			(
			 	NAME
			 ";
		if ( array_key_exists('id', $params) ) {
			$sql .= "
				,ID
				";
		}
		if ( array_key_exists('office', $params) ) {
			$sql .= "
				,OFFICE
				";
		}
		if ( array_key_exists('explanation', $params) ) {
			$sql .= "
				,EXPLANATION
				";
		}
		if ( array_key_exists('password', $params) ) {
			$sql .= "
				,PASSWORD
				";
		}
		$sql .= "
				,REG_DATE
				,IS_DELETE
			)
			VALUES
			(
				'{$params['name']}'
			";
		if ( array_key_exists('id', $params) ) {
			$sql .= "
				,'{$params['id']}'
				";
		}
		if ( array_key_exists('office', $params) ) {
			$sql .= "
				,'{$params['office']}'
				";
		}
		if ( array_key_exists('explanation', $params) ) {
			$sql .= "
				,'{$params['explanation']}'
				";
		}
		if ( array_key_exists('password', $params) ) {
			$sql .= "
				,'{$params['password']}'
				";
		}
		$sql .= "
				,date_format(current_timestamp(), '%Y-%m-%d')
				,'N'
			)
			";
		$this->db->query($sql);

		$result = $this->db
			->query('SELECT NO FROM TB_USER ORDER BY NO DESC LIMIT 1')
			->row_array();

		return $result;
	}

	/**
	 * 사용자 수정
	 * @param $params
	 * @return false
	 */
	public function user_update($params) {
		if ( $params['id'] != null ) {
			$sql =  "
                SELECT  count(*) AS CNT
                  FROM  TB_USER
                 WHERE  ID = '{$params['id']}'
                   AND  NO != {$params['no']}
				   AND 	IS_DELETE = 'N'
                ";

			$resultArray = $this->db
				->query($sql)
				->row_array();

			if ($resultArray['CNT'] != 0) {
				return false;
			}
		}

		$sql = "
                UPDATE  TB_USER
                   SET  ID          = '{$params['id']}', 
                        NAME        = '{$params['name']}',
                        PASSWORD    = '{$params['password']}',
                        OFFICE      = '{$params['office']}',
                        EXPLANATION = '{$params['explanation']}'                    
                 WHERE  NO = {$params['no']} 
                ";

		$result = $this->db->query($sql);

		return $result;
	}

	/**
	 * 사용자 삭제
	 * @param $params
	 * @return mixed
	 */
	public function user_delete($params) {
		$sql = "
			UPDATE TB_USER
			   SET IS_DELETE = 'Y'
			 WHERE NO = {$params['no']}
			";

		$result = $this->db
			->query($sql);

		return $result;
	}

	/**
	 * 로그인
	 * @param $params
	 * @return mixed
	 */
	public function login($params) {
		$sql = "
			SELECT 	EXISTS 
					(
					SELECT 	*
					  FROM 	TB_USER
					 WHERE 	ID = '{$params['id']}'
					   AND 	PASSWORD = '{$params['password']}'
					) AS EXIST
			";

		$result = $this->db
			->query($sql)
			->row_array();

		return $result;
	}
}
