<?php

namespace App\Security\Voter;

use App\Entity\Machine;
use App\Entity\User;
use LogicException as LogicExceptionAlias;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class MachineVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const LIST = 'list';
    const CREATE = 'create';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // edit or view
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::LIST, self::CREATE])) {
            return false;
        }

        // only vote on Machine objects inside this voter
        if (!$subject instanceof Machine) {
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

        $machine = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user);
            case self::EDIT:
                return $this->canEdit($machine, $user);
            case self::CREATE:
                return $this->canCreate($user);
            case self::LIST:
                return $this->canList($user);
        }

        throw new LogicExceptionAlias('This code should not be reached!');
    }

    private function canView(User $user)
    {
        // if they can edit, they can view
        return $user->canViewOneSite();
    }

    private function canEdit(Machine $machine, User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $ips = $machine->getIps();

        if (0 === count($ips)) {
            return true;
        }

        return $user->canEditOneSite();
    }

    private function canList(User $user)
    {
        //We can list machine as soon as we can see at least one site
        return count($user->getNewRoles()) || $user->isAdmin();
    }

    private function canCreate(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        //We can list machine as soon as we can edit at least one site
        foreach ($user->getNewRoles() as $role) {
            if ($role->isWritable()) {
                return true;
            }
        }

        return false;
    }
}
