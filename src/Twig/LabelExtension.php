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

namespace App\Twig;

use App\Entity\LabelInterface;
use Symfony\Component\Translation\Translator;

/**
 * Label Extension.
 *
 * This class return the label of an entity.
 *
 * @category Twig
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class LabelExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Set the translator.
     *
     * Initialized by dependence injection.
     *
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return the new filter: label.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'label' => new \Twig_SimpleFilter(
                'label',
                [$this, 'labelFilter'],
                []
            ),
        );
    }

    /**
     * Label Filter.
     *
     * @param LabelInterface $entity
     *
     * @return string IP Adress / Cidr
     */
    public function labelFilter(LabelInterface $entity = null)
    {
        if (is_null($entity)){
            return $this->translator->trans('default.label.none');
        } else {
            return $entity->getLabel();
        }
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'label_extension';
    }
}
