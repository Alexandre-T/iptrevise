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

namespace App\Twig;

use App\Entity\Service;
use Doctrine\Common\Collections\Collection;

/**
 * ServicesExtension class.
 *
 * This class declare a Twig filter which translate an array of role or a comma separated string
 * to a translated string of services
 *
 * @category Twig
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class ServicesExtension extends \Twig_Extension
{
    /**
     * Return the new filter: services.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'services' => new \Twig_SimpleFilter(
                'services',
                [$this, 'servicesFilter'],
                []
            ),
        );
    }

    /**
     * Services Filter.
     *
     * @param string|Collection $services
     * @param string            $inputDelimiter  input deimiter used to split a string into an array
     * @param string            $outputDelimiter delimiter used to implode the result
     *
     * @return string
     */
    public function servicesFilter($services, $inputDelimiter = ', ', $outputDelimiter = ', ')
    {
        $result = [];

        if ($services instanceof Collection) {
            foreach ($services as $service) {
                /* @var Service $service  */
                $result[] = $service->getLabel();
            }
        } else {
            $result = explode($inputDelimiter, $services);
        }

        //Tri
        sort($result);

        return implode($outputDelimiter, $result);
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'services_extension';
    }
}
