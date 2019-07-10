<?php

namespace App\Security\Voter;

use App\Entity\Plage;
use App\Entity\User;
use LogicException as LogicExceptionAlias;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PlageVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const LIST = 'list';
    const CREATE = 'create';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // edit or view
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::LIST, self::CREATE, self::DELETE])) {
            return false;
        }

        // only vote on Machine objects inside this voter
        if (!$subject instanceof Plage) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // user is admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        $user = $token->getUser();

        //user is logged in
        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::DELETE:
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::CREATE:
                return $this->canCreate($subject, $user);
            case self::LIST:
                return $this->canList($user);
        }

        throw new LogicExceptionAlias('This code should not be reached!');
    }

    private function canView(Plage $plage, User $user)
    {
        if (null === $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        foreach ($user->getNewRoles() as $role) {
            if ($role->getSite() === $plage->getNetwork()->getSite()) {
                return true;
            }
        }

        return false;
    }

    private function canEdit(Plage $plage, User $user)
    {
        if (null === $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        foreach ($user->getNewRoles() as $role) {
            if ($role->isWritable() && $role->getSite() === $plage->getNetwork()->getSite()) {
                return true;
            }
        }

        return false;
    }

    private function canList(User $user)
    {
        if (null === $user) {
            return false;
        }

        return $user->isAdmin() || count($user->getNewRoles());
    }

    private function canCreate(Plage $plage, User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        //We can list machine as soon as we can edit at least one site
        foreach ($user->getNewRoles() as $role) {
            if ($role->isWritable() && $role->getSite() === $plage->getNetwork()->getSite()) {
                return true;
            }
        }
        
        return false;
    }
}
