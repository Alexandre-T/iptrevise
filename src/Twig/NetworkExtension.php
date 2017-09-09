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

namespace App\Twig;

use Symfony\Component\Translation\Translator;

/**
 * Network Extension.
 *
 * This class declare a Twig filter which translate a long (network adress) and an integer (mask) into an IP/Mask
 *
 * @category Twig
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class NetworkExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Set the translator.
     *
     * Initialized by dependence injection.
     *
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return the new filter: network.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'network' => new \Twig_SimpleFilter(
                'network',
                [$this, 'networkFilter'],
                []
            ),
            'networkTooltip' => new \Twig_SimpleFilter(
                'networkTooltip',
                [$this, 'networkTooltipFilter'],
                ['is_safe' => 'html']
            ),            
        );
    }

    /**
     * Network Filter.
     *
     * @param int $address Network address 
     * @param int $mask    Network mask
     * 
     * @return string       IP Adress / Mask
     */
    public function networkFilter($address, $mask = 24)
    {
       return long2ip($address).'/'.$mask;
    }
    /**
     * Network Filter with Tooltip.
     *
     * @param int $address Network address 
     * @param int $mask    Network mask
     * 
     * @return string       IP Adress / Mask
     */
    public function networkTooltipFilter($address, $mask = 24)
    {
       return '<span class="" data-toggle="tooltip" data-tooltip="TOTO">'
              .$this->networkFilter($address, $mask)
              .'</span>';
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'network_extension';
    }
}
