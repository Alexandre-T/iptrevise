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
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 *
 */

$dotEnv = new \Symfony\Component\Dotenv\Dotenv();
$dotEnv->populate([
    'APP_ENV' => 'test',
    'DATABASE_URL' => 'postgres://postgres@127.0.0.1:5432/symfony?charset=utf8&application_name=iptrevise2'
]);
$dotEnv->load(__DIR__.'/../../.env');
