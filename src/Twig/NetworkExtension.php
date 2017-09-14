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
 * This class declare a Twig filter which translate a long (network adress) and an integer (cidr) into an IP/Cidr
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
            'ip' => new \Twig_SimpleFilter(
                'ip',
                [$this, 'ipFilter'],
                []
            ),
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
     * Ip Filter.
     *
     * @param int $address Ip address
     *
     * @return string IP Adress / Cidr
     */
    public function ipFilter($address)
    {
        if (null === $address) {
            return $this->translator->trans('default.ip.none');
        }

        return long2ip($address);
    }

    /**
     * Network Filter.
     *
     * @param int $address Network address
     * @param int $cidr    Network cidr
     *
     * @return string IP Adress / Cidr
     */
    public function networkFilter($address, $cidr = 24)
    {
        return $this->ipFilter($address).'/'.$cidr;
    }

    /**
     * Network Filter with Tooltip.
     *
     * @param int $address Network address
     * @param int $cidr    Network cidr
     *
     * @return string IP Adress / Cidr
     */
    public function networkTooltipFilter($address, $cidr = 24)
    {
        return '<span class="" data-toggle="tooltip" data-tooltip="TOTO">'
              .$this->networkFilter($address, $cidr)
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
