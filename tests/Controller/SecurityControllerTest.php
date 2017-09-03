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

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * SecurityControllerTest class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * Test the login form.
     */
    public function testLoginAction()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $response = $client->getResponse();
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test the logout rerouting.
     *
     * The SecurityController throw an Exception if the route is not catch by Listener.
     * This test verifies there is no exception thrown.
     */
    public function testLogoutAction()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode());

    }

}
