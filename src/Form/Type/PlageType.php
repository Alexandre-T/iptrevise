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

// use App\Entity\Ip;
use App\Entity\Plage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Ip form builder.
 *
 * @category App\Form\Type
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class PlageType extends AbstractType
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
            ->add('label', null,[
              'label' => 'form.plage.field.label',
              'help_block' => 'form.plage.help.label',
            ])
            ->add('start', AddressIpType::class, [
                'label' => 'form.plage.field.ipdeb',
                'help_block' => 'form.plage.help.ip',
            ])
            ->add('end', AddressIpType::class, [
                'label' => 'form.plage.field.ipfin',
                'help_block' => 'form.plage.help.ip',
            ])
            ->add('color', ColorType::class, [
                'label' => 'form.network.field.color',
                'help_block' => 'form.network.help.color',
            ])
            ->add('reason', TextType::class, [
                'label' => 'form.plage.field.reason',
                'help_block' => 'form.plage.help.reason',
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
            'data_class' => 'App\Entity\Plage',
            'render_fieldset' => false,
            'show_legend' => false,
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'checkPlageInNetwork'],
                ]),
            ],
        ));
    }

    /**
     * Check if the IP is in the Network.
     *
     * @param Plage                        $Plage
     * @param ExecutionContextInterface $context
     */
    public function checkPlageInNetwork(Plage $plage, ExecutionContextInterface $context)
    {
        if ($plage->getEnd() > $plage->getNetwork()->getMaxIp() || $plage->getStart() < $plage->getNetwork()->getMinIp()) {
            $context->buildViolation('form.plage.error.plage.network %min% %max%', [
                '%min%' => long2ip($plage->getNetwork()->getMinIp()),
                '%max%' => long2ip($plage->getNetwork()->getMaxIp()),
            ])->addViolation();
        }
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "PlageProfileType" => "plage_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'app_plage';
    }
}
