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

use App\Form\DataTransformer\TagsTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * TagsType class.
 *
 * @category App\Form\Type
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class TagsType extends AbstractType
{
    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * User user.
     *
     * @var mixed
     */
    private $user;

    /**
     * Tags Type constructor.
     *
     * @param ObjectManager                 $manager
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(ObjectManager $manager, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->manager = $manager;
        $token = $tokenStorage->getToken();
        if (null !== $token && $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->user = $token->getUser();
        }
    }

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
            ->addModelTransformer(new CollectionToArrayTransformer(), true)
            ->addModelTransformer(new TagsTransformer($this->manager, $this->user), true);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'tag-input',
            ],
            'required' => false,
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'checkEachTag'],
                ]),
            ],
        ]);
    }

    /**
     * Check if the Adresse and cidr are valid.
     *
     * @param array                     $data
     * @param ExecutionContextInterface $context
     */
    public function checkEachTag($data, ExecutionContextInterface $context)
    {
        foreach ($data as $tag) {
            if (strlen($tag) > 16) {
                $context->buildViolation('form.tag.error.too-long %tag%', [
                    '%tag%' => $tag,
                ])->addViolation();
            }
        }
    }

    /**
     * Get parent class.
     *
     * @return string
     */
    public function getParent(): string
    {
        return TextType::class;
    }
}
