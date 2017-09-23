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
 *
 */

namespace App\Tests\Bean;

use App\Bean\Data;
use App\Bean\Factory\DataFactory;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Data Bean Factory test case.
 *
 * @category Testing
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 */
class DataFactoryTest extends TestCase
{
    /**
     * Testing Create User Data.
     */
    public function testCreateUserDataWithOneKey()
    {
        //First case with label
        $rowdata['label'] = 'label';
        $expected = new Data();
        $expected->setLabel('form.user.field.label');
        $expected->setName('label');
        $actuals = DataFactory::createUserData($rowdata);
        self::assertCount(1, $actuals);
        self::compareMethodsResults($expected, $actuals[0]);
        unset($rowdata, $expected, $actuals);

        //Second case with password
        $rowdata['password'] = 'password';
        $expected = new Data();
        $expected->setLabel('form.user.field.password');
        //Password is never put in Logs rendering.
        $expected->setName('*****');
        $actuals = DataFactory::createUserData($rowdata);
        self::assertCount(1, $actuals);
        self::compareMethodsResults($expected, $actuals[0]);
        unset($rowdata, $expected, $actuals);

        //Third case with mail
        $rowdata['mail'] = 'mail';
        $expected = new Data();
        $expected->setLabel('form.user.field.mail');
        $expected->setName('mail');
        $actuals = DataFactory::createUserData($rowdata);
        self::assertCount(1, $actuals);
        self::compareMethodsResults($expected, $actuals[0]);
        unset($rowdata, $expected, $actuals);

        //Fourth case with empty
        $rowdata['password'] = '';
        $expected = new Data();
        $expected->setLabel('form.user.field.password');
        $expected->setNone(true);
        $actuals = DataFactory::createUserData($rowdata);
        self::assertCount(1, $actuals);
        self::compareMethodsResults($expected, $actuals[0]);
        unset($rowdata, $expected, $actuals);

        //Fifth case with roles
        $rowdata['roles'] = ['ROLE_ADMIN', 'ROLE_READER'];
        $expected = new Data();
        $expected->setLabel('form.user.field.roles');
        $expected->setName('ROLE_ADMIN, ROLE_READER');
        $actuals = DataFactory::createUserData($rowdata);
        self::assertCount(1, $actuals);
        self::compareMethodsResults($expected, $actuals[0]);
        unset($rowdata, $expected, $actuals);

    }

    /**
     * Testing Create User Data.
     */
    public function testCreateUserDataWithTwoKeys()
    {
        //First case with name and password
        $rowdata['mail'] = 'mail';
        $expected[0] = new Data();
        $expected[0]->setLabel('form.user.field.mail');
        $expected[0]->setName('mail');
        $actuals = DataFactory::createUserData($rowdata);
        self::compareMethodsResults($expected[0], $actuals[0]);
        unset($actuals);

        $rowdata['password'] = 'password';
        $expected[1] = new Data();
        $expected[1]->setLabel('form.user.field.password');
        $expected[1]->setName('*****');
        $actuals = DataFactory::createUserData($rowdata);
        self::compareMethodsResults($expected[0], $actuals[0]);
        unset($actuals);

        //Third case No more parent 42 but some wrong users
        $users[]= $this->createMockUser(17);
        $users[]= $this->createMockUser(19);

        $actuals = DataFactory::createUserData($rowdata);
        self::assertCount(2, $actuals);
        self::compareMethodsResults($expected[0], $actuals[0]);
        self::compareMethodsResults($expected[1], $actuals[1]);
        unset($expected[1], $actuals);
    }

    /**
     * This test compare the result of each method.
     *
     * Because we aren't comparing two object of different instance,
     * we compare the result of each method.
     *
     * @param Data $expected
     * @param Data $actual
     */
    private static function compareMethodsResults(Data $expected, Data $actual)
    {
        self::assertEquals($expected->getEntity(), $actual->getEntity());
        self::assertEquals($expected->getId(), $actual->getId());
        self::assertEquals($expected->getLabel(), $actual->getLabel());
        self::assertEquals($expected->getName(), $actual->getName());
        self::assertEquals($expected->isNoMore(), $actual->isNoMore());
        self::assertEquals($expected->isNone(), $actual->isNone());
    }

    /**
     * Create a Mock of User.
     *
     * @param int $id
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockUser(int $id)
    {
        $User = $this->getMockBuilder(User::class)->getMock();
        $User->method('getId')->willReturn($id);
        $User->method('getMail')->willReturn("Mail $id");
        $User->method('getPassword')->willReturn("foo/bar");
        $User->method('getLabel')->willReturn("Label $id");

        return $User;
    }
}
