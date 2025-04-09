<?php

Yii::import('application.components.DataThinning.LinearRegressionDataThinning');
Yii::import('application.components.DataThinning.WeekDataThinningPeriod');
Yii::import('application.services.DataThinningService');

class ProcessOldDataCommand extends CConsoleCommand {
    public function actionIndex() {
        echo "Starting process...\n";
        Yii::log('Starting process...', CLogger::LEVEL_WARNING, 'application');

        $strategy = new LinearRegressionDataThinning();
        $thinningPeriod = new WeekDataThinningPeriod();

        $service = new DataThinningService($strategy, $thinningPeriod);
        $service->processOldData();

        echo "Process completed.\n";
        Yii::log('Process completed.', CLogger::LEVEL_WARNING, 'application');
    }
}