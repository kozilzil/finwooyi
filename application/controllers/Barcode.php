<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode extends MY_Controller {
	public function check() {
		$this->setBeforeAssets();
		$this->setAssets('barcode', 'check');
		$this->setAfterAssets();

		$this->setMenuList();

		$this->setTitle(getenv("title.barcode.check"));
		$this->base_view("/barcode/check/index");
	}
}
