<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Health extends CI_Controller
{
	public function index()
	{
		$this->output->set_content_type('application/json');

		$db_status = 'ok';
		$db_error = null;

		$db = $this->load->database('db_session', true);
		if (!$db->conn_id) {
			$db_status = 'error';
			$db_error = $db->error();
		} else {
			$db->query('SELECT 1');
			$db_error = $db->error();
			if (!empty($db_error['code'])) {
				$db_status = 'error';
			} else {
				$db_error = null;
			}
		}

		$payload = [
			'status' => $db_status === 'ok' ? 'ok' : 'degraded',
			'environment' => ENVIRONMENT,
			'database' => [
				'status' => $db_status,
				'error' => $db_error,
			],
			'timestamp' => date('c'),
		];

		$this->output->set_output(json_encode($payload));
	}
}
