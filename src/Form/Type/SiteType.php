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

use App\Entity\Site;
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
class SiteType extends AbstractType
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
        /*$current = $options['data'];

        if ($current instanceof Site && count($current->getNetworks())) {
            $disabled = true;
            $helpIp = 'form.network.help.ip.readonly';
            $helpCidr = 'form.network.help.cidr.readonly';
        } else {
            $disabled = false;
            $helpIp = 'form.network.help.ip';
            $helpCidr = 'form.network.help.cidr';
        }*/

        $builder
        ->add('label', null, [
            'label' => 'form.site.field.label',
            'help_block' => 'form.site.help.label',
        ])
        ->add('color', ColorType::class, [
            'label' => 'form.network.field.color',
            'help_block' => 'form.network.help.color',
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Site',
            'render_fieldset' => false,
            'show_legend' => false,
            /*'constraints' => [
                new Callback([
                    'callback' => [$this, 'checkSite'],
                ]),
            ],*/
        ]);
    }

    /**
     * Check if the Adress and cidr are valid.
     *
     * @param Network                   $network
     * @param ExecutionContextInterface $context
     */
    /*public function checkSite(Site $site, ExecutionContextInterface $context)
    {
        $result = $network->getIp() & ($network->getMask());

        if ($result != $network->getIp()) {
            $context->buildViolation('form.network.error.ip.address %address% %mean%', [
                '%address%' => long2ip($network->getIp()).'/'.$network->getCidr(),
                '%mean%' => long2ip($result).'/'.$network->getCidr(),
            ])->addViolation();
        }
    }*/

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
        return 'app_site';
    }
}
