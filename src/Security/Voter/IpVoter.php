<?php

namespace App\Security\Voter;

use App\Entity\Ip;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class IpVoter extends Voter
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

        // only vote on Ip objects inside this voter
        if (!$subject instanceof Ip && $subject !== 'ip') {
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

        $ip = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($ip, $user);
            case self::CREATE:
                return $this->canCreate($user);
            case self::DELETE:
            case self::EDIT:
                return $this->canEdit($ip, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(Ip $ip, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($ip, $user)) {
            return true;
        }

        $site = $ip->getNetwork()->getSite();
        foreach ($user->getNewRoles() as $role) {
            if ($role->getSite() === $site) {
                return true;
            }
        }
        return false;

    }

    private function canEdit(Ip $ip, User $user)
    {
        $site = $ip->getNetwork()->getSite();
        foreach ($user->getNewRoles() as $role) {
            if ($role->getSite() === $site && !($role->isReadOnly())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Can create a ip?
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
