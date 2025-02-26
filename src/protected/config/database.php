<?php

// This is the database connection configuration.
return array(
    'class'=>'CDbConnection',
    'connectionString'=>'mysql:host=db;dbname=power_graph_db',
    'username'=>'user',
    'password'=>'user_password',
    'emulatePrepare'=>true,
);