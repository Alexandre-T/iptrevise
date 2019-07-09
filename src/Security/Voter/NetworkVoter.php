<?php

namespace App\Security\Voter;

use App\Entity\Network;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class NetworkVoter extends Voter
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

    protected function supports($attribute, $subject)
    {
        // edit or view
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])) {
            return false;
        }

        // only vote on Network objects inside this voter
        if (!$subject instanceof Network && $subject !== 'network') {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // user is admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        //user is logged in
        if (!$user instanceof UserInterface) {
            return false;
        }

        $network = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($network, $user);
            case self::CREATE:
                return $this->canCreate($user);
            case self::DELETE:
            case self::EDIT:
                return $this->canEdit($network, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(Network $network, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($network, $user)) {
            return true;
        }

        $site = $network->getSite();
        foreach ($user->getNewRoles() as $role) {
            if ($role->getSite() === $site) {
                return true;
            }
        }
        return false;

    }

    private function canEdit(Network $network, User $user)
    {
        $site = $network->getSite();
        foreach ($user->getNewRoles() as $role) {
            if ($role->getSite() === $site && !($role->isReadOnly())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Can create a network?
     *
     * @param User $user
     *
     * @return bool
     */
    private function canCreate(User $user): bool
    {
        foreach ($user->getNewRoles() as $role) {
            if ($role->isWritable()) {
                return true;
            }
        }

        return false;
    }
}
