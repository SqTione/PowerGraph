<?php

class ChartWidget extends CWidget {
	public $data;
	private $flotAssets;

	public function init() {
		if ($this->flotAssets === null) {
			$this->flotAssets = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.extensions.flot'));	
		}

		parent::init();
	}

	protected function registerClientScript() {
		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile($this->flotAssets . '/jquery.flot.js', CClientScript::POS_END);
	}

	public function run() {
		$this->registerClientScript();
		$this->render('_chart', ['data' => $this->data] );
	}
}