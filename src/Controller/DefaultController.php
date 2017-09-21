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

namespace App\Controller;

use App\Manager\IpManager;
use App\Manager\MachineManager;
use App\Manager\NetworkManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default Controller.
 *
 * @category App\Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
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

        return $this->render('@App/default/index.html.twig', $output);
    }
}
