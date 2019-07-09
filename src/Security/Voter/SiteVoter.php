<?php

namespace App\Security\Voter;

use App\Entity\Site;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class SiteVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Supports Site and 4 methods.
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // edit or view
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])) {
            return false;
        }

        // only vote on Network objects inside this voter
        if (!$subject instanceof Site) {
            return false;
        }

        return true;
    }

    /**
     * Vote on attribute.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        //user is logged in
        if (!$user instanceof UserInterface) {
            return false;
        }

        $site = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($site, $user);
            case self::DELETE:
            case self::EDIT:
                return $this->canEdit($site, $user);
            case self::CREATE:
                return false;
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(Site $site, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($site, $user)) {
            return true;
        }

        $roles = $user->getNewRoles();
        foreach ($roles as $role) {
            if ($role->getSite() === $site) {
                return true;
            }
        }

        return false;
    }

    private function canEdit(Site $site, User $user)
    {
        $roles = $user->getNewRoles();
        foreach ($roles as $role) {
            if ($role->getSite() === $site && ($role->isWritable())) {
                return true;
            }
        }
        return false;
    }
}
