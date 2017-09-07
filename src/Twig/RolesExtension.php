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
 * RolesExtension class.
 *
 * This class declare a Twig filter which translate an array of role or a comma separated string
 * to a translated string of roles
 *
 * @category Twig
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class RolesExtension extends \Twig_Extension
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
     * Return the new filter: roles.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'roles' => new \Twig_SimpleFilter(
                'roles',
                [$this, 'rolesFilter'],
                []
            ),
        );
    }

    /**
     * Roles Filter.
     *
     * @param array|string $roles
     * @param string       $inputDelimiter  input deimiter used to split a string into an array
     * @param string       $outputDelimiter delimiter used to implode the result
     *
     * @return string
     */
    public function rolesFilter($roles, $inputDelimiter = ', ', $outputDelimiter = ', ')
    {
        $result = [];

        if (!is_array($roles)) {
            $roles = explode($inputDelimiter, $roles);
        }

        foreach ($roles as $role) {
            $result[] = $this->translator->trans($role);
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
        return 'roles_extension';
    }
}
