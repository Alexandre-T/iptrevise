<?php

namespace App\Security\Voter;

use App\Entity\Site;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class SiteVoter extends Voter
{
  const VIEW = 'view';
  const EDIT = 'edit';

  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  protected function supports($attribute, $subject)
  {
    // edit or view
    if (!in_array($attribute, [self::VIEW, self::EDIT])) {
      return false;
    }

    // only vote on Network objects inside this voter
    if (!$subject instanceof Site) {
      return false;
    }
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {

    $user = $token->getUser();

    //user is logged in
    if (!$user instanceof UserInterface) {
      return false;
    }

    $site = $subject;

    switch ($attribute) {
      case self::VIEW:
      return $this->canView($site, $user);
      case self::EDIT:
      return $this->canEdit($site, $user);
    }

    throw new \LogicException('This code should not be reached!');
  }

  private function canView(Site $site, User $user)
  {
    // if they can edit, they can view
    if ($this->canEdit($site, $user)) {
      return true;
    }

    $roles=$user->getNewRoles();
    foreach($roles as $role) {
      if($role->getSite() == $site){
        return true;
      }
    }
    return false;

  }

  private function canEdit(Network $network, User $user)
  {
    // user is admin
    if ($this->security->isGranted('ROLE_ADMIN')) {
      return true;
    }

    $roles=$user->getNewRoles();
    foreach($roles as $role) {
      if($role->getSite() == $site && !($role->isReadOnly())) {
        return true;
      }
    }
    return false;
  }
}
