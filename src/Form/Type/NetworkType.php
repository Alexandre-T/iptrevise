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

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Network form builder.
 *
 * @category Form
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com
 * @license GNU General Public License, version 3
 *
 * @see http://opensource.org/licenses/GPL-3.0
 */
class NetworkType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        dump($options);
        //@FIXME Si on a déjà des IP qui sont enregistrées alors il faut empêcher l'édition de l'IP est du masque
        $builder
            ->add('label', null, [
                'label' => 'form.network.field.label',
                'help_block' => 'form.network.help.label',
            ])
            //TODO Use a HTML5 ColorType
            //@see https://stackoverflow.com/questions/19845930/symfony2-custom-form-field-type-html5-color
            ->add('color', null, [
                'label' => 'form.network.field.color',
                'help_block' => 'form.network.help.color',
                'attr' => [
                    'placeholder' => '000000'
                ],
            ])
            ->add('description', null, [
                'label' => 'form.network.field.description',
                'help_block' => 'form.network.help.description',
            ])
            ->add('ip', IpType::class, [
                'label' => 'form.network.field.ip',
                'help_block' => 'form.network.help.ip',
                'attr' => [
                    'placeholder' => '192.168.0.0'
                ],
            ])
            ->add('mask', null, [
                'label' => 'form.network.field.mask',
                'help_block' => 'form.network.help.mask',
                'attr' => [
                    'placeholder' => '24'
                ],
            ])
        ;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Network',
            'render_fieldset' => false,
            'show_legend' => false,
        ));
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "NetworkProfileType" => "network_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'app_network';
    }
}
