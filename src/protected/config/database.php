<?php

require_once(dirname(__FILE__) . '/../components/EnvHelper.php');

$dbname = EnvHelper::getEnv('MYSQL_DATABASE');
$db_username = EnvHelper::getEnv('MYSQL_USER');
$db_password = EnvHelper::getEnv('MYSQL_PASSWORD');

// This is the database connection configuration.
// return array(
//     'class'=>'CDbConnection',
//     'connectionString'=>'mysql:host=db;dbname=' . $dbname,
//     'username'=>$db_username,
//     'password'=>$db_password,
//     'emulatePrepare'=>true,
//     'charset'=>'utf8mb4',
// );

return array(
    'connectionString' => 'mysql:host=db;dbname=power_graph_db',
    'username' => 'user',
    'password' => 'user_password',
    'charset' => 'utf8mb4',
    'class' => 'CDbConnection',
);