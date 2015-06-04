<?php

$config = require __DIR__ . '/../config.php';

$config['css']['assets.lks-admin'] = '/assets/css/lks.admin.css';

unset($config['regions']);
$config['regions']['admin menu'] = 'admin menu';
$config['regions']['admin content top'] = 'admin content top';
$config['regions']['admin content bottom'] = 'admin content bottom';

return $config;