<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 */

require_once __DIR__.'/../vendor/autoload.php';

$dotEnv = new \Symfony\Component\Dotenv\Dotenv();
$dotEnv->populate([
    'APP_ENV' => 'test',
    'DATABASE_URL' => 'postgres://postgres@127.0.0.1:5432/symfony?charset=utf8&application_name=iptrevise2'
]);
$dotEnv->load(__DIR__.'/../.env');

class_alias('App\Kernel', 'Kernel');
