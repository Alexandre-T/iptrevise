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
 */

namespace App\Bean\Factory;

use App\Bean\Data;

/**
 * DataFactory class.
 *
 * @category App\Bean\Factory
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class DataFactory
{
    /**
     * Valid columns of user entity.
     */
    const VALID_USER = ['label', 'mail', 'password'];

    /**
     * Create Data from a serialized user data.
     *
     * @param array $rowdata
     *
     * @return array of Data
     */
    public static function createUserData(array $rowdata): array
    {
        //Initialization
        $result = [];
        foreach ($rowdata as $column => $value) {
            $data = new Data();

            $data->setLabel("form.user.field.$column");
            if (empty($value)) {
                $data->setNone(true);
            } else {
                if ($column == 'password') {
                    $data->setName('*****');
                } elseif ($column == 'roles') {
                    $data->setName(implode(', ', $value));
                } else {
                    $data->setName($value);
                }
            }
            $result[] = $data;
        }

        return $result;
    }

    /**
     * Create Data from a serialized network data.
     *
     * @param array $rowdata
     *
     * @return array of Data
     */
    public static function createNetworkData(array $rowdata): array
    {
        //Initialization
        $result = [];
        foreach ($rowdata as $column => $value) {
            $data = new Data();

            $data->setLabel("form.network.field.$column");
            if (empty($value)) {
                $data->setNone(true);
            } else {
                if ('ip' == $column) {
                    $data->setName(long2ip($value));
                } else {
                    $data->setName($value);
                }
            }
            $result[] = $data;
        }

        return $result;
    }

    /**
     * Create Data from a serialized machine data.
     *
     * @param array $rowdata
     *
     * @return array of Data
     */
    public static function createMachineData(array $rowdata): array
    {
        //Initialization
        $result = [];
        foreach ($rowdata as $column => $value) {
            $data = new Data();

            $data->setLabel("form.machine.field.$column");
            if (empty($value)) {
                $data->setNone(true);
            } else {
                $data->setName($value);
            }
            $result[] = $data;
        }

        return $result;
    }

    /**
     * Create Data from a serialized ip data.
     *
     * @param array $rowdata
     *
     * @return array of Data
     */
    public static function createIpData(array $rowdata): array
    {
        //Initialization
        $result = [];
        foreach ($rowdata as $column => $value) {
            $data = new Data();

            $data->setLabel("form.ip.field.$column");
            if (empty($value)) {
                $data->setNone(true);
            } else {
                if ('ip' == $column) {
                    $data->setName(long2ip($value));
                } elseif ('network' == $column) {
                    $data->setName($value['id']);
                } elseif ('machine' == $column) {
                    $data->setName($value['id']);
                } else {
                    $data->setName($value);
                }
            }
            $result[] = $data;
        }

        return $result;
    }
}
