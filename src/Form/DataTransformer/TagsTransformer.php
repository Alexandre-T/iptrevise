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
 */

namespace App\Form\DataTransformer;

use App\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Tags transformer.
 *
 * @category DataTransformer
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class TagsTransformer implements DataTransformerInterface
{
    /**
     * FIXME Remplacer l'object manager.
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * TagsTransformer constructor.
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
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
        //FIXME Remplacer le manager par le bon appel via le conteneur.
        $tags = $this->manager->getRepository('App:Tag')->findBy([
            'label' => $names,
        ]);
        $newNames = array_diff($names, $tags);
        foreach ($newNames as $name) {
            $tag = new Tag();
            $tag->setLabel($name);
            //FIXME add creator
            $tags[] = $tag;
        }

        return $tags;
    }
}
