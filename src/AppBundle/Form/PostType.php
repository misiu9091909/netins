<?php
namespace AppBundle\Form;

use AppBundle\Entity\Post;
use AppBundle\Transformer\View\StringToFileTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'Title',
            ])
            ->add('body', TextareaType::class, [
                'attr' => ['rows' => 15, 'cols' => 50],
                'label' => 'Body',
            ])
            ->add('author', null, [
                'label' => 'Author',
            ])
            ->add('image_name', FileType::class, [
                'label' => 'Image',
                'required' => false
            ])
            ->add('original_image_name', HiddenType::class)
        ;
        $builder->get('image_name')->addViewTransformer(new StringToFileTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
