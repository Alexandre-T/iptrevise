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
 *
 */
namespace App\Doctrine;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * Hash Password Listener.
 *
 * @category Doctrine
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class HashPasswordListener implements EventSubscriber
{
    /**
     * The password encoder.
     *
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * HashPasswordListener constructor.
     *
     * @param UserPasswordEncoder $passwordEncoder
     */
    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * This subscriber will listen prePersist and preUpdate event.
     *
     * @return array of events this subscriber wants to listen to
     */
    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    /**
     * This function is called before persist.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            //This is not a User, so we quit.
            return;
        }

        $this->encodePassword($entity);
    }

    /**
     * This function is called before update.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        $this->encodePassword($entity);

        // I understand why we do that: it is necessary to force the update to see the change
        $em = $args->getEntityManager();
        // But I do not understand how we can find this solution to force this update.
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * Encode the plain password and pass it to the user entity
     *
     * @param User $entity
     */
    private function encodePassword(User $entity)
    {
        // Is the user password modified?
        if (!$entity->getPlainPassword()) {
            //No, so we quit
            return;
        }

        // Password encoding
        $encoded = $this->passwordEncoder->encodePassword(
            $entity,
            $entity->getPlainPassword()
        );

        // We pass the encoded password to the Entity
        $entity->setPassword($encoded);
    }
}
