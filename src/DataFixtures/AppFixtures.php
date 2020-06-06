<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Personne;
use App\Entity\Infox;
use App\Entity\Theme;
use App\Entity\Categorie;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $theme1 = new Theme();
        $theme1->setIntitule("Sante");
        $manager->persist($theme1);
        $theme2 = new Theme();
        $theme2->setIntitule("Franc-Maçon");
        $manager->persist($theme2);
        $theme3 = new Theme();
        $theme3->setIntitule("Complot");
        $manager->persist($theme3);

        $categorie1 = new Categorie();
        $categorie1->setIntitule("Cadre Superieur");
        $manager->persist($categorie1);
        $categorie2 = new Categorie();
        $categorie2->setIntitule("Ouvrier");
        $manager->persist($categorie2);
        $categorie3 = new Categorie();
        $categorie3->setIntitule("Retraite");
        $manager->persist($categorie3);
        $categorie4 = new Categorie();
        $categorie4->setIntitule("Gilet Jaune");
        $manager->persist($categorie4);

        $infox1 = new Infox();
        $infox1->setIntitule("La terre est plate");
        $infox1->setTheme($theme3);
        $infox1->setViralite(1.1);
        $manager->persist($infox1);
        $infox2 = new Infox();
        $infox2->setIntitule("Coronavirus 2");
        $infox2->setTheme($theme1);
        $infox2->setViralite(1.2);
        $manager->persist($infox2);
        $infox3 = new Infox();
        $infox3->setIntitule("La fin du monde");
        $infox3->setTheme($theme1);
        $infox3->setViralite(1.3);
        $manager->persist($infox3);
        $infox4 = new Infox();
        $infox4->setIntitule("Jamais allé sur la lune");
        $infox4->setTheme($theme3);
        $infox4->setViralite(1.4);
        $manager->persist($infox4);

        $personne1 = new Personne();
        $personne1->setNom("Jules");
        $personne1->setAge(53);
        $personne1->setSexe("M");
        $personne1->setCategorie($categorie1);
        $personne2 = new Personne();
        $personne2->setNom("Emmanuel");
        $personne2->setAge(42);
        $personne2->setSexe("M");
        $personne2->setCategorie($categorie1);
        $personne3 = new Personne();
        $personne3->setNom("Nadine");
        $personne3->setAge(18);
        $personne3->setSexe("F");
        $personne3->setCategorie($categorie2);
        $personne4 = new Personne();
        $personne4->setNom("Nadege");
        $personne4->setAge(35);
        $personne4->setSexe("F");
        $personne4->setCategorie($categorie2);

        $personne1->addLike($infox1);
        $personne1->addLike($infox2);
        $personne2->addLike($infox1);
        $personne2->addLike($infox3);
        $personne3->addLike($infox2);

        $manager->persist($personne1);
        $manager->persist($personne2);
        $manager->persist($personne3);
        $manager->persist($personne4);

        $manager->flush();
    }
}
