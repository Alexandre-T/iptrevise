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
namespace App\Bean\Factory;

use App\Bean\Data;

/**
 * DataFactory class.
 *
 * @category App\Bean\Factory
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class DataFactory
{
    /**
     * Valid columns of user entity.
     */
    const VALID_USER = ['label', 'mail', 'password'];

    /**
     * Create Data from a serialized data.
     *
     * @param array $rowdata
     * @return array of Data
     */
    public static function createUserData(array $rowdata):array
    {
        //Initialization
        $resultat = [];
        foreach ($rowdata as $column => $value) {
            $data = new Data();

            $data->setLabel("form.user.field.$column");
            if (empty($value)) {
                $data->setNone(true);
            } else {
                if ($column == 'password') {
                    $data->setName('*****');
                } elseif($column == 'roles') {
                    $data->setName(implode(', ', $value));
                }else{
                    $data->setName($value);
                }
            }
            $resultat[] = $data;
        }
        return $resultat;
    }
}
