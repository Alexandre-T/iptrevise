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

use App\Entity\Network;
use App\Entity\Site;
use App\Repository\SiteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Network form builder.
 *
 * @category App\Form\Type
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
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
        $current = $options['data'];
        $sites = $options['sites']??[];

        if ($current instanceof Network && count($current->getIps())) {
            $disabled = true;
            $helpIp = 'form.network.help.ip.readonly';
            $helpCidr = 'form.network.help.cidr.readonly';
        } else {
            $disabled = false;
            $helpIp = 'form.network.help.ip';
            $helpCidr = 'form.network.help.cidr';
        }

        $builder
            ->add('label', null, [
                'label' => 'form.network.field.label',
                'help_block' => 'form.network.help.label',
            ])
            ->add('ip', AddressIpType::class, [
                'label' => 'form.network.field.ip',
                'help_block' => $helpIp,
                'disabled' => $disabled,
                'attr' => [
                    'placeholder' => '192.168.0.0',
                ],
            ])
            ->add('cidr', null, [
                'label' => 'form.network.field.cidr',
                'help_block' => $helpCidr,
                'disabled' => $disabled,
                'attr' => [
                    'placeholder' => '24',
                ],
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'query_builder' => function (SiteRepository $er) use ($sites) {
                  return $er->createQueryBuilder('s')
                      ->where('s.id in (:sites)')
                      ->setParameter('sites', $sites)
                      ->orderBy('s.label', 'ASC');
                },
                'choice_label' => 'label',
                'help_block' => 'form.network.help.site'
              ])
            ->add('color', ColorType::class, [
                'label' => 'form.network.field.color',
                'help_block' => 'form.network.help.color',
            ])
            ->add('description', null, [
                'label' => 'form.network.field.description',
                'help_block' => 'form.network.help.description',
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
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Network',
            'render_fieldset' => false,
            'show_legend' => false,
            'sites' => [],
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'checkNetwork'],
                ]),
            ],
        ]);
    }

    /**
     * Check if the Adresse and cidr are valid.
     *
     * @param Network                   $network
     * @param ExecutionContextInterface $context
     */
    public function checkNetwork(Network $network, ExecutionContextInterface $context)
    {
        $result = $network->getIp() & ($network->getMask());

        if ($result != $network->getIp()) {
            $context->buildViolation('form.network.error.ip.address %address% %mean%', [
                '%address%' => long2ip($network->getIp()).'/'.$network->getCidr(),
                '%mean%' => long2ip($result).'/'.$network->getCidr(),
            ])->addViolation();
        }
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
