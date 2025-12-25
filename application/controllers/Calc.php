<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calc extends MY_Controller {
	public function cash_type() {
		$this->setBeforeAssets();
		$this->setAssets('calc', 'cash_type');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.calc.cash_type"));
		$this->base_view("/calc/cash_type/index");
	}

	public function counter() {
		$this->setBeforeAssets();
		$this->setAssets('calc', 'counter');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.calc.counter"));
		$this->base_view("/calc/counter/index");
	}

	public function coefficient() {
		$posts = $this->input->post();
		$date = $posts['date'];

		$this->load->model('Income_model');
		$result = $this->Income_model->coefficient_list(['date' => $date]);
		$total = 0;
		for($idx=0;$idx<count($result);$idx++) {
			if ($result[$idx]['IS_ONLINE'] == "N") {
				$total += $result[$idx]['PRICE'];
			}
		}

		echo json_encode(['price' => $total]);
	}
}
