<?php
/**
 * This file is part of the iptrevise2.
 *
 * PHP version 5.6 | 7.0 | 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license   MIT
 *
 * @see https://github.com/Alexandre-T/casguard/blob/master/LICENSE
 */

namespace App\Tests\unit\Manager;

use App\Entity\Ip;
use App\Entity\Network;
use App\Manager\IpManager;
use App\Repository\IpRepository;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @category App\Tests\unit\Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class IpManagerTest extends TestCase
{
    /**
     * @var IpManager
     */
    private $ipManager;

    /**
     * Repository mocked.
     *
     * @var IpRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->repository = $this->getMockBuilder(IpRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var EntityManager|\PHPUnit_Framework_MockObject_MockObject $em */
        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $em->expects(self::once())
            ->method('getRepository')
            ->with(Ip::class)
            ->willReturn($this->repository);
        $this->ipManager = new IpManager($em);
    }

    /**
     * Test the GetFirstNonReferencedIp method.
     */
    public function testGetFirstNonReferencedIp()
    {
        $networkUnset = new Network();
        self::assertNull($this->ipManager->getFirstNonReferencedIp($networkUnset));

        $networkC = new Network();
        $networkC->setCidr(24);
        $networkC->setIp(ip2long('192.168.0.0'));
        self::assertEquals(ip2long('192.168.0.1'), $this->ipManager->getFirstNonReferencedIp($networkC));

        $networkB = new Network();
        $networkB->setCidr(16);
        $networkB->setIp(ip2long('172.22.0.0'));
        self::assertEquals(ip2long('172.22.0.1'), $this->ipManager->getFirstNonReferencedIp($networkB));

        $networkA = new Network();
        $networkA->setCidr(8);
        $networkA->setIp(ip2long('10.0.0.0'));
        self::assertEquals(ip2long('10.0.0.1'), $this->ipManager->getFirstNonReferencedIp($networkA));

        //We return one value
        $ip = new Ip();
        $this->repository->expects(self::any())
            ->method('findBy')
            ->willReturn([$ip]);

        $ip->setIp(ip2long('192.168.0.1'));
        self::assertEquals(ip2long('192.168.0.2'), $this->ipManager->getFirstNonReferencedIp($networkC));
        $ip->setIp(ip2long('192.168.0.42'));
        self::assertEquals(ip2long('192.168.0.1'), $this->ipManager->getFirstNonReferencedIp($networkC));

        $ip->setIp(ip2long('172.22.0.1'));
        self::assertEquals(ip2long('172.22.0.2'), $this->ipManager->getFirstNonReferencedIp($networkB));
        $ip->setIp(ip2long('172.22.0.42'));
        self::assertEquals(ip2long('172.22.0.1'), $this->ipManager->getFirstNonReferencedIp($networkB));

        $ip->setIp(ip2long('10.0.0.1'));
        self::assertEquals(ip2long('10.0.0.2'), $this->ipManager->getFirstNonReferencedIp($networkA));
        $ip->setIp(ip2long('10.0.0.42'));
        self::assertEquals(ip2long('10.0.0.1'), $this->ipManager->getFirstNonReferencedIp($networkA));
    }

    public function testGetFirstNonReferencedIp2()
    {
        $networkC = new Network();
        $networkC->setCidr(24);
        $networkC->setIp(ip2long('192.168.0.0'));

        //We return two value
        $ip1 = new Ip();
        $ip2 = new Ip();

        $ip1->setIp(ip2long('192.168.0.1'));
        $ip2->setIp(ip2long('192.168.0.2'));

        $this->repository->expects(self::any())
            ->method('findBy')
            ->willReturn([$ip1, $ip2]);
        self::assertEquals(ip2long('192.168.0.3'), $this->ipManager->getFirstNonReferencedIp($networkC));
        $ip2->setIp(ip2long('192.168.0.42'));
        self::assertEquals(ip2long('192.168.0.2'), $this->ipManager->getFirstNonReferencedIp($networkC));
    }

    public function testGetFirstNonReferencedIp3()
    {
        $networkC = new Network();
        $networkC->setCidr(24);
        $networkC->setIp(ip2long('192.168.0.0'));

        $ips = [];

        for ($index = 1; $index < 254; ++$index) {
            $ip = new Ip();
            $ip->setIp(ip2long('192.168.0.0') + $index);
            $ips[] = $ip;
        }

        $this->repository->expects(self::any())
            ->method('findBy')
            ->willReturn($ips);
        self::assertEquals(ip2long('192.168.0.254'), $this->ipManager->getFirstNonReferencedIp($networkC));
    }

    /**
     * Test a satured network.
     */
    public function testGetFirstNonReferencedIp4()
    {
        $networkC = new Network();
        $networkC->setCidr(24);
        $networkC->setIp(ip2long('192.168.0.0'));

        $ips = [];

        for ($index = 1; $index < 255; ++$index) {
            $ip = new Ip();
            $ip->setIp(ip2long('192.168.0.0') + $index);
            $ips[] = $ip;
        }

        $this->repository->expects(self::any())
            ->method('findBy')
            ->willReturn($ips);
        self::assertNull($this->ipManager->getFirstNonReferencedIp($networkC));
    }
}
