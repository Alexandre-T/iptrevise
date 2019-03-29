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

use App\Bean\Factory\InformationFactory;
use App\Form\Type\NetworkType;
use App\Entity\Network;
use App\Manager\NetworkManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * NetworkController class.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("network")
 */
class NetworkController extends Controller
{
    /**
     * Limit of network per page for listing.
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Lists all network entities.
     *
     * @Route("/", name="default_network_index")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_NETWORK')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving all services
        $networkManager = $this->get(NetworkManager::class);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $networkManager->getQueryBuilder(), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'network.label', 'defaultSortDirection' => 'asc']
        );

        return $this->render('@App/default/network/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new network entity.
     *
     * @Route("/new", name="default_network_new")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_NETWORK')")
     *
     * @param Request $request
     *
     * @return RedirectResponse |Response
     */
    public function newAction(Request $request)
    {
        $network = new Network();
        $form = $this->createForm(NetworkType::class, $network);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $networkService = $this->get(NetworkManager::class);
            $networkService->save($network, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.network.created %name%', ['%name%' => $network->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_network_show', array('id' => $network->getId()));
        }

        return $this->render('@App/default/network/new.html.twig', [
            'network' => $network,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a network entity.
     *
     * @Route("/{id}", name="default_network_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_NETWORK')")
     *
     * @param Network $network
     *
     * @return Response
     */
    public function showAction(Network $network)
    {
        /** @var NetworkManager $networkManager */
        $networkManager = $this->get(NetworkManager::class);

        $view = [];
        $view['information'] = InformationFactory::createInformation($network);
        $view['logs'] = $networkManager->retrieveLogs($network);
        $view['network'] = $network;
        $view['isDeletable'] = $this->isGranted('ROLE_MANAGE_NETWORK') && $networkManager->isDeletable($network);

        if ($view['isDeletable']){
            $view['delete_form'] = $this->createDeleteForm($network)->createView();
        }

        return $this->render('@App/default/network/show.html.twig', $view);
    }

    /**
     * Generates the occupation graph for a network entity.
     *
     * @Route("/matrice/{network}", name="default_network_matrice")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_NETWORK')")
     *
     * @param Network $network
     *
     * @return Response
     */

    public function matriceAction(Network $network)
    {
        // create background image ( default : grey)
        // FIXME change $height and $width according to network cidr?
        $height = 64;
        $width = 1024;
        $image = imagecreate ( $width , $height );
        imagecolorallocate($image, 127, 127, 127);

        // get the size in pixels of each adress according to the network cidr
        $cidr = $network->getCidr();
        /*if ($cidr < 16) { TODO gestion d'erreur
            return -1;
        }*/
        $adressWidth = 1;
        $adressHeight = 1;
        for ($i = 0; $i < $cidr - 16; $i++)
        {
            if ($i % 2 == 0)
            {
                $adressHeight *= 2;
            } else {
                $adressWidth *= 2;
            }
        }

        // color the adresses reserved by the network with its color
        $color = imagecolorallocate($image,
                                    $network->getRed(),
                                    $network->getGreen(),
                                    $network->getBlue());

        $white = imagecolorallocate($image, 255, 255, 255);
        $ips = $network->getIps();
        $plages = $network->getPlages();
        $end = $network->getIp()+(2**(32-$cidr));
        // the first line will be initialized at 0 in the loops by adding 1
        $line = -1;
        $adressIndex = 0;

        // default value of $startPlage and $endPlage are not in the network
        $startPlage = $end+1;
        $endPlage = $end+1;

        for( $i = 0; $i < $height; $i++ )
        {
          for( $j = 0; $j < $width; $j++ )
          {

            if ($j % $adressWidth == 0 )
            {

                if ( $i % $adressHeight == 0 && $j == 0 )
                {
                    $line += 1;
                }

                $setColor = false;
                $adressIndex += 1;

                // if $adressIndex % ($width/$adressWidth) == 0 then the adress is exactly ($width/$adressWidth) plus the line
                if($adressIndex % ($width/$adressWidth) == 0)
                {
                    $adress = $network->getIp() + ($width/$adressWidth)+($line*($width/$adressWidth));
                }
                else
                {
                    $adress = $network->getIp() + ($adressIndex %($width/$adressWidth))+($line*($width/$adressWidth));
                }

                /*if the current address does not belong to the current plage, check if it belongs to another plage in the network*/
               if ($adress < $startPlage || $adress > $endPlage)
               {
                   foreach ($plages as $plage)
                   {
                       if ( $adress >= $plage->getStart() && $adress <=  $plage->getEnd() )
                       {
                           $startPlage = $plage->getStart();
                           $endPlage = $plage->getEnd();
                           // the color used is the plage's color not the network's  color
                           $plageColor = imagecolorallocate($image,
                                                       $plage->getRed(),
                                                       $plage->getGreen(),
                                                       $plage->getBlue());
                       }
                   }
               }

               foreach ($ips as $ip)
               {
                   if ($ip->getIp() == $adress)
                   {
                       $setColor = true;
                   }
               }
           }

           /* the color of a plage of address is set first*/
           if ( $adress >= $startPlage && $adress <= $endPlage)
           {
               imagesetpixel($image, $j, $i, $plageColor);
           }
           /*the color of a reserved adress on the network is set after*/
           if ($setColor)
           {
               imagesetpixel($image, $j, $i, $color);
           }

           /* if the address is greater or equal to the broadcast address, it cannot be reserved so it is set to white*/
           /*a white grid is set to have an idea of the dimension of an ip address on the network */
            if ($j % $adressWidth == 0  ||$i % $adressHeight == 0  || $adress >= $end -1)
            {
                imagesetpixel($image, $j, $i, $white);
            }
          }
      }
        //FIXME Cette partie de code est moche, mais je n'ai rien trouvé de mieux pour le moment
        ob_start();
        imagejpeg($image);
        $imageString = ob_get_clean();

        $headers= array(
            'Content-type'=>'image/jpeg',
            'Pragma'=>'no-cache',
            'Cache-Control'=>'no-cache'
        );

        return new Response($imageString, 200, $headers);
    }

    /**
     * Displays a form to edit an existing network entity.
     *
     * @Route("/{id}/edit", name="default_network_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_NETWORK')")
     *
     * @param Request $request The request
     * @param Network $network The network entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Network $network)
    {
        $networkService = $this->get(NetworkManager::class);
        $view = [];
        $isDeletable = $networkService->isDeletable($network);

        if ($isDeletable){
            $view['delete_form'] = $this->createDeleteForm($network)->createView();
        }

        $editForm = $this->createForm(NetworkType::class, $network);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $networkService->save($network, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.network.updated %name%', ['%name%' => $network->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_network_show', array('id' => $network->getId()));
        }
        $logs = $networkService->retrieveLogs($network);
        $information = InformationFactory::createInformation($network);

        return $this->render('@App/default/network/edit.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'network' => $network,
            'edit_form' => $editForm->createView(),
        ]));
    }

    /**
     * Deletes a network entity.
     *
     * @Route("/{id}", name="default_network_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_MANAGE_NETWORK')")
     *
     * @param Request $request The request
     * @param Network $network The $network entity
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Network $network)
    {
        $form = $this->createDeleteForm($network);
        $form->handleRequest($request);
        $session = $this->get('session');
        $trans = $this->get('translator.default');
        $networkManager = $this->get(NetworkManager::class);
        $isDeletable = $networkManager->isDeletable($network);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $networkManager->delete($network);

            $message = $trans->trans('default.network.deleted %name%', ['%name%' => $network->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        }elseif (!$isDeletable){

            $message = $trans->trans('default.network.not-deletable %name%', ['%name%' => $network->getLabel()]);
            $session->getFlashBag()->add('warning', $message);
            return $this->redirectToRoute('default_network_show', ['id' => $network->getId()]);
        }

        return $this->redirectToRoute('default_network_index');
    }

    /**
     * Creates a form to delete a network entity.
     *
     * @param Network $network The network entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Network $network)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_network_delete', array('id' => $network->getId())))
            ->setMethod('DELETE')
            ->add('delete', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'trash-o',
                'label' => 'administration.delete.confirm.delete',
            ])
            ->getForm()
            ;
    }
}
