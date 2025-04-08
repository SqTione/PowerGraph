<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    public function init() {
        // Публикуем каталог jquery в assets
        $jqueryAssets = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.extensions.jquery'));

        // Подключаем jQuery
        Yii::app()->clientScript->registerScriptFile(
            'https://code.jquery.com/jquery-3.6.0.min.js',
            CClientScript::POS_HEAD
        );

        Yii::app()->clientScript->registerScriptFile(
            'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
            CClientScript::POS_HEAD
        );

        Yii::app()->clientScript->registerCssFile(
            'https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css'
        );

        // Подключаем Chart.js
        Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile('https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.bundle.min.js', CClientScript::POS_HEAD);

        parent::init();
    }
}