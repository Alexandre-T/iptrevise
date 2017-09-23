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

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Tags transformer.
 *
 * @category DataTransformer
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class TagsTransformer implements DataTransformerInterface
{
    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * Creator of the tag.
     *
     * @var User
     */
    private $user;

    /**
     * TagsTransformer constructor.
     * Object manager is provided by constructor.
     *
     * @param ObjectManager $manager
     * @param User          $user
     */
    public function __construct(ObjectManager $manager, User $user)
    {
        $this->manager = $manager;
        $this->user = $user;
    }

    /**
     * Implode value to a comma separated string.
     *
     * @param $value
     *
     * @return string
     */
    public function transform($value): string
    {
        return implode(',', $value);
    }

    /**
     * Explode string to a Tag[].
     *
     * @param $string
     *
     * @return array
     */
    public function reverseTransform($string): array
    {
        $names = array_unique(array_filter(array_map('trim', explode(',', $string))));
        $tags = $this->manager->getRepository('App:Tag')->findBy([
            'label' => $names,
        ]);
        $newNames = array_diff($names, $tags);
        foreach ($newNames as $name) {
            $tag = new Tag();
            $tag->setLabel($name);
            $tag->setCreator($this->user);
            $tags[] = $tag;
        }

        return $tags;
    }
}
