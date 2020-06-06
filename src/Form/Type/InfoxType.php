<?php
namespace App\Form\Type;
use App\Entity\Theme;
use App\Repository\InfoxRepository;
use App\Repository\ThemeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Infox;
class InfoxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('intitule', TextType::class)
                ->add('viralite', NumberType::class)
                ->add('theme', EntityType::class,
                    array('class' => Theme::class,
                        'query_builder' => function (ThemeRepository $repo) {
                            return $repo->createQueryBuilder('t');
                        }
                    ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Infox::class,
        ));
    }
}