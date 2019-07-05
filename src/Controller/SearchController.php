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

use App\Manager\IpManager;
use App\Manager\SiteManager;
use App\Manager\MachineManager;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Search CRUD Controller.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("search")
 */
class SearchController extends Controller
{
    /**
     * Limit of machine per page for listing.
     */
    const LIMIT_PER_PAGE = 25;


    /**
     * Lists of all machines or ips whose matched
     *
     * @Route("/", name="default_search_index")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_MACHINE')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving search
        $search = $request->query->get('search', '');
        $search = (empty($search))? '%' : "%$search%";

        //Retrieving manager
        $machineManager = $this->get(MachineManager::class);
        $ipManager = $this->get(IpManager::class);
        $siteManager = $this->get(SiteManager::class);

        //retrieving readable site
        $sites = $siteManager->getReadable($this->getUser());

        $pageMachine = $request->query->getInt('pageMachine', 1); /*page number*/
        //Retrieving all services
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $machineManager->getQueryBuilderWithSearch($search), /* queryBuilder NOT result */
            $pageMachine,
            self::LIMIT_PER_PAGE,
            [
                'defaultSortFieldName' => 'machine.label',
                'defaultSortDirection' => 'asc',
                Paginator::PAGE_PARAMETER_NAME => 'pageMachine'
            ]
        );

        $pagination2 = $paginator->paginate(
            $ipManager->getQueryBuilderWithSearch($search, $sites), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'ip.ip | ip', 'defaultSortDirection' => 'asc']
        );

        return $this->render('default/search/index.html.twig', [
            'pagination' => $pagination,
            'pagination2' => $pagination2,
            'result' => $search
        ]);

    }
}

?>
