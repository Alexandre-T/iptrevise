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
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Controller;

use App\Manager\DeletedIpManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Gedmo\Loggable\Entity\LogEntry;

/**
 * SiteController class.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("ip")
 */
class DeletedIpController extends Controller
{
    /**
     * Limit of site per page for listing
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Lists all machine entities.
     *
     * @Route("/deleted", name="default_deleted_ip_index")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving all services
        $deletedIpManager = $this->get(DeletedIpManager::class);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $deletedIpManager->getQueryBuilder(), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'ext_log_entries.objectId', 'defaultSortDirection' => 'asc']
        );

        return $this->render('default/ip/indexDeleted.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Finds and displays a site entity.
     *
     * @Route("/deleted/{id}", name="default_deleted_ip_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param LogEntry $log
     *
     * @return Response
     */
    public function showAction(LogEntry $log)
    {
        /** @var DeletedIpManager $deletedIpManager */
        $deletedIpManager = $this->get(DeletedIpManager::class);

        $view = [];
        $view['logs'] = $deletedIpManager->retrieveLogs($log);
        $view['log'] = $log;

        return $this->render('default/ip/showDeleted.html.twig', $view);
    }
}
