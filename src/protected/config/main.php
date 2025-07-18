<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'My Web Application',

    // preloading 'log' component
    'preload'=>array('log'),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*.*',
        'application.components.*.*',
        'application.services.*.*',
        'application.seeders.*.*',
    ),

    'modules'=>array(
        /* Настройка Gii */
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'password',
            // 'ipFilters'=>array('127.0.0.1','::1')
            'ipFilters'=>array(),   // TODO: !!!REMOVE ON RELEASE!!!
        ),
    ),

    // application components
    'components'=>array(

        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
        ),


        /* Настройка маршрутов */
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName' => false,
            'rules'=>array(
                /* Маршруты для API */
                'meter/<id:\d+>' => 'site/meter',
                'voltage-data' => 'voltageData/index',      // Маршрут вывода мгновенных значений в виде графика
                'site/updateUserMeter' => 'site/updateUserMeter',

                /* Стандартные маршруты */
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),

        // database settings are configured in database.php
        'db'=>require(dirname(__FILE__).'/database.php'),

        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>YII_DEBUG ? null : 'site/error',
        ),

        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning, info',
                    'categories' => 'application.log'
                ),
                // uncomment the following to show log messages on web pages
                
                array(
                    'class'=>'CWebLogRoute',
                ),
            ),
        ),

    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        // this is used in contact page
        'adminEmail'=>'webmaster@example.com',
    ),
);