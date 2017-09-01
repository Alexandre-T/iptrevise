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
namespace App\Twig;

/**
 * GravatarExtension class.
 *
 * @category Twig
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class GravatarExtension extends \Twig_Extension
{
    private $secure_request = false;

    /**
     *
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'gravatar' => new \Twig_SimpleFilter(
                'gravatarFilter',
                [$this, 'gravatarFilter'],
                ['is_safe',['html']]
            ),
            'sgravatar' => new \Twig_SimpleFilter(
                'securedGravatarFilter',
                [$this, 'securedGravatarFilter'],
                ['is_safe',['html']]
            ),
        );
    }

    /**
     * Gravatar Filter
     *
     * @param string $email
     * @param string|null $size
     * @param string|null $default
     * @return string
     */
    public function gravatarFilter($email, $size = null, $default = null)
    {
        $defaults = array(
            '404',
            'mm',
            'identicon',
            'monsterid',
            'wavatar',
            'retro',
            'blank'
        );
        $hash = md5($email);
        $url = $this->secure_request ? 'https://' : 'http://';
        $url .= 'www.gravatar.com/avatar/'.$hash;
        // Size
        if (!is_null($size)) {
            $url .= "?s=$size";
        }
        // Default
        if (!is_null($default)) {
            $url .= is_null($size) ? '?' : '&';
            $url .= in_array($default, $defaults) ? $default : urlencode($default);
        }
        return $url;
    }

    /**
     * The request is secured.
     *
     * @param string $email
     * @param string|null $size
     * @param string|null $default
     * @return string url
     */
    public function secureGravatarFilter($email, $size = null, $default = null)
    {
        $this->secure_request = true;
        return $this->gravatarFilter($email, $size, $default);
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'gravatar_extension';
    }
}
