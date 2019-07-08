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

use App\Entity\Ip;
use App\Entity\LabelInterface;
use App\Entity\Machine;
use App\Entity\Network;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Translation\Translator;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * RolesExtension class.
 *
 * This class declare a Twig filter which translate an array of role or a comma separated string
 * to a translated string of roles
 *
 * @category Twig
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class RolesExtension extends Twig_Extension
{

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var User|null
     */
    protected $user = null;

    /**
     * @var array|\Symfony\Component\Security\Core\Role\Role[]
     */
    protected $roles = [];

    public function __construct(TokenStorageInterface $tokenStorage, Translator $translator)
    {
        $this->translator = $translator;

        $token = $tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $this->user = $token->getUser();
            $this->roles = $token->getRoles();
        }
    }

    /**
     * Return the new filter: roles.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'roles' => new Twig_SimpleFilter(
                'roles',
                [$this, 'rolesFilter'],
                []
            ),
        );
    }

    /**
     * Return the new function: roles.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'can_create_site' => new Twig_SimpleFunction(
                'can_create_site',
                [$this, 'canCreateSite'],
                []
            ),
            'can_edit' => new Twig_SimpleFunction(
                'can_edit',
                [$this, 'canEdit'],
                []
            ),
            'can_view' => new Twig_SimpleFunction(
                'can_view',
                [$this, 'canView'],
                []
            ),
            'can_view_deleted_site' => new Twig_SimpleFunction(
                'can_view_deleted_site',
                [$this, 'canViewDeletedSite'],
                []
            ),
        );
    }

    /**
     * Roles Filter.
     *
     * @param array|string $roles
     * @param string       $inputDelimiter  input delimiter used to split a string into an array
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
     * Can user edit an object?
     * 
     * @param LabelInterface $object
     * 
     * @return bool
     */
    public function canEdit(LabelInterface $object) {
        if ($object instanceof Site) {
            return $this->canEditSite();
        }

        if ($object instanceof Network) {
            return $this->canEditNetwork($object);
        }
        
        return false;
    }

    /**
     * Can user view an object?
     * 
     * @param LabelInterface $object
     * 
     * @return bool
     */
    public function canView(LabelInterface $object) {
        if ($object instanceof Site) {
            return $this->canViewSite($object);
        }

        if ($object instanceof Network) {
            return $this->canViewSite($object->getSite());
        }

        if ($object instanceof Ip) {
            return $this->canViewSite($object->getNetwork()->getSite());
        }

        if ($object instanceof Machine) {
            return true;
        }

        return false;
    }

    /**
     * Can user edit site.
     *
     * @return bool
     */
    private function canEditSite()
    {
        return $this->user->isAdmin();
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

    /**
     * Can user view deleted site?
     *
     * @return bool
     */
    public function canViewDeletedSite()
    {
        return $this->user->isAdmin();
    }

    /**
     * Can user create a new site?
     *
     * @return bool
     */
    public function canCreateSite()
    {
        return $this->user->isAdmin();
    }

    /**
     * Can user view site?
     *
     * @param Site $site
     *
     * @return bool
     */
    private function canViewSite(Site $site)
    {
        foreach ($this->user->getNewRoles() as $role) {
            if ($role->getSite() === $site) {
                return true;
            }
        }

        return false;
    }

    private function canEditNetwork(Network $network)
    {
        if (null === $this->user) {
            return false;
        }

        foreach ($this->user->getNewRoles() as $role) {
            /** @var Role $role */
            if ($role->getSite() === $network->getSite()) {
                return $role->isWritable();
            }
        }
        return false;
    }
}
