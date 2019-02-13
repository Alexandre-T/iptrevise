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

namespace App\Form\Type;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Machine form builder.
 *
 * @category App\Form\Type
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class MachineType extends AbstractType
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
        $dns = new Service();
        $dns->setLabel('DNS');
        $router = new Service();
        $router->setLabel('ROUTER');
        $builder
            ->add('label', null, [
                'label' => 'form.machine.field.label',
                'help_block' => 'form.machine.help.label',
            ])
            ->add('description', null, [
                'label' => 'form.machine.field.description',
                'help_block' => 'form.machine.help.description',
            ])
            ->add('interface', null, [
                'label' => 'form.machine.field.interface',
                'help_block' => 'form.machine.help.interface',
                'attr' => [
                    'placeholder' => '1',
                ],
            ])
            ->add('location', null, [
              'label' => 'form.machine.field.location',
              'help_block' => 'form.machine.help.location',
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'query_builder' => function (ServiceRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.label', 'ASC');
                },
                'choice_label' => 'label',
                'required' => false,
                'by_reference' => false,
                'multiple' => true,
                'expanded' => true,
                'help_block' => 'form.machine.help.services',
            ])
            ->add('tags', TagsType::class, [
                'label' => 'form.machine.field.tags',
                'help_block' => 'form.machine.help.tags',
            ])
            ->add('location', null, [
              'label' => 'form.machine.field.location',
              'help_block' => 'form.machine.help.location',
            ])
            ->add('macs', null, [
              'label' => 'form.machine.field.macs',
              'help_block' => 'form.machine.help.macs',
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Machine',
            'render_fieldset' => false,
            'show_legend' => false,
        ));
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "MachineProfileType" => "machine_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'app_machine';
    }
}
