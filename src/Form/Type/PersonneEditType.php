<?php
namespace App\Form\Type;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Personne;
class PersonneEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sexe', TextType::class)
                ->add('age', NumberType::class)
                ->add('categorie', EntityType::class,
                    array('class' => Categorie::class,
                        'query_builder' => function (CategorieRepository $repo) {
                            return $repo->createQueryBuilder('c');
                        }
                    ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Personne::class,
        ));
    }
}