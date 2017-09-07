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

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User form builder.
 *
 * @category Form
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com
 * @license GNU General Public License, version 3
 *
 * @link http://opensource.org/licenses/GPL-3.0
 *
 */
class UserType extends AbstractType
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
        $builder
            ->add('label', null, [
                'label' => 'form.user.field.username',
                'help_block' => 'form.user.help.username',
            ])
            ->add('mail', null, [
                'label' => 'form.user.field.mail',
                'help_block' => 'form.user.help.mail',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'form.user.field.roles',
                'help_block' => 'form.user.help.roles',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    'form.user.field.roles.option.admin' => 'ROLE_ADMIN',
                    'form.user.field.roles.option.reader' => 'ROLE_READER',
                    'form.user.field.roles.option.organizer' => 'ROLE_ORGANIZER',
                    'form.user.field.roles.option.user' => 'ROLE_USER',
                ]
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
            'data_class' => 'App\Entity\User',
            'render_fieldset' => false,
            'show_legend' => false,
        ));
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'app_user';
    }
}
