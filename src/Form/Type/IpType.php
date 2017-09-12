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

use App\Entity\Ip;
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
 * @license Cerema 2017
 */
class IpType extends AbstractType
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
        $builder
            ->add('ip', AddressIpType::class, [
                'label' => 'form.ip.field.ip',
                'help_block' => 'form.ip.help.ip',
            ])
            ->add('reason', TextType::class, [
                'label' => 'form.ip.field.reason',
                'help_block' => 'form.ip.help.reason',
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
            'data_class' => 'App\Entity\Ip',
            'render_fieldset' => false,
            'show_legend' => false,
            'constraints'        => [
                new Callback([
                    'callback' => [$this, 'checkIpInNetwork'],
                ]),
            ],
        ));
    }

    /**
     * Check if the IP is in the Network
     *
     * @param Ip $ip
     * @param ExecutionContextInterface $context
     */
    public function checkIpInNetwork(Ip $ip, ExecutionContextInterface $context)
    {
        dump($ip->getIp(),$ip->getNetwork()->getMinIp(),$ip->getNetwork()->getMaxIp());
        if ($ip->getIp() > $ip->getNetwork()->getMaxIp() || $ip->getIp() < $ip->getNetwork()->getMinIp())
            $context->buildViolation('form.ip.error.ip.network %min% %max%',[
                '%min%' => long2ip($ip->getNetwork()->getMinIp()),
                '%max%' => long2ip($ip->getNetwork()->getMaxIp()),
            ])->addViolation();
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "IpProfileType" => "ip_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'app_ip';
    }
}
