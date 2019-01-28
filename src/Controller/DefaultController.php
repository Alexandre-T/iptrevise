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
 */

namespace App\Controller;

use App\Manager\IpManager;
use App\Manager\MachineManager;
use App\Manager\NetworkManager;
use App\Manager\SiteManager;
use App\Manager\ServiceManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default Controller.
 *
 * @category App\Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class DefaultController extends Controller
{
    /**
     * Homepage.
     *
     * @Route("/", name="home", methods={"get"})
     * @Route("/", name="homepage", methods={"get"})
     *
     * @return Response
     */
    public function indexAction()
    {
        $output = [];

        if ($this->isGranted('ROLE_READ_NETWORK')) {
            $networkManager = $this->get(NetworkManager::class);
            $nNetworks = $networkManager->count();
            $output['nNetworks'] = $nNetworks;
        }

        if ($this->isGranted('ROLE_READ_MACHINE')) {
            $machineManager = $this->get(MachineManager::class);
            $nMachines = $machineManager->count();
            $output['nMachines'] = $nMachines;
        }

        if ($this->isGranted('ROLE_READ_IP')) {
            $ipManager = $this->get(IpManager::class);
            $nIps = $ipManager->count();
            $output['nIps'] = $nIps;
        }

        if ($this->isGranted('ROLE_READ_SITE')) {
            $siteManager = $this->get(SiteManager::class);
            $nSites = $siteManager->count();
            $output['nSites'] = $nSites;
        }
        if ($this->isGranted('ROLE_READ_SERVICE')) {
            $serviceManager = $this->get(ServiceManager::class);
            $nServices = $serviceManager->count();
            $output['nServices'] = $nServices;
        }
        return $this->render('@App/default/index.html.twig', $output);
    }
}
