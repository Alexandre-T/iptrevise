<?php

namespace App\Security\Voter;

use App\Entity\Machine;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class MachineVoter extends Voter
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
    if (!$user instanceof UserInterface) {
      return false;
    }

    $machine = $subject;

    switch ($attribute) {
      case self::VIEW:
      return $this->canView($machine, $user);
      case self::EDIT:
      return $this->canEdit($machine, $user);
    }

    throw new \LogicException('This code should not be reached!');
  }

  private function canView(Machine $machine, User $user)
  {
    // if they can edit, they can view
    if ($this->canEdit($machine, $user)) {
      return true;
    }
    $ips=$machine->getIps();
    $networks=array();
    $withoutNetwork=true;
    foreach($ips as $ip){
      if($ip->getNetwork()!=NULL and !in_array($ip->getNetwork(),$networks) ) {
        $networks[]=$ip->getNetwork();
        $withoutNetwork=false;
      }
    }
    foreach($networks as $network){
      $site=$network->getSite();
      $roles=$user->getNewRoles();
      foreach($roles as $role) {
        if($role->getSite() == $site or $withoutNetwork==true){
          return true;
        }

      }
      return false;

    }
  }

    private function canEdit(Machine $machine, User $user)
    {


      $ips=getIps();
      $networks=array();
      $withoutNetwork=true;

      foreach($ips as $ip){
        if($ip->getNetwork()!=NULL and !in_array($ip->getNetwork(),$networks)) {
          $networks[]=$ip->getNetwork();
          $withoutNetwork=false;
        }
      }
      foreach($networks as $network){
        $site=$network->getSite();
        $roles=$user->getNewRoles();
        foreach($roles as $role) {
          if($role->getSite() == $site && !($role->isReadOnly())) {
            return true;
          }
          else if($withoutNetwork==true){
            return true;

        }
        return false;

      }
    }
  }
}
