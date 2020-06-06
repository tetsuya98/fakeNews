<?php
namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Infox;
use App\Entity\Theme;
use App\Entity\Personne;
use App\Form\Type\ThemeType;
use App\Form\Type\InfoxType;
use App\Form\Type\PersonneType;
use App\Form\Type\PersonneEditType;
use App\Form\Type\CategorieType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends AbstractController {
    public function accueil() {
        return $this->render('news/accueil.html.twig');
    }

    public function themes() {
        $errMSG = "";
        $themes = $this->getDoctrine()->getRepository(Theme::class)->findAll();
        return $this->render('news/themes.html.twig',
            array('themes' => $themes, 'errMSG' => $errMSG));
    }

    public function theme($id) {
        $theme = $this->getDoctrine()->getRepository(Theme::class)->find($id);
        $infoxs = $this->getDoctrine()->getRepository(Infox::class)->findByTheme($id);
        $personnes = new ArrayCollection();
        foreach ($infoxs as $infox) {
            $personnes[] = $infox->getLike();
        }
        $homme = 0; $femme = 0; $total = 0;
        foreach ($personnes as $personne){
            foreach ($personne as $pers) {
                if ($pers->getSexe() == "M") {
                    $homme = $homme + 1;
                }
                if ($pers->getSexe() == "F") {
                    $femme = $femme + 1;
                }
                $total = $total + 1;
            }
        }
        if ($total != 0) {
            $pariteH = round(($homme * 100) / $total, 1);
            $pariteF = 100 - $pariteH;
        }else{
            $pariteH = 0; $pariteF = 0;
        }

        if(!$theme)
            throw $this->createNotFoundException('Theme[id='.$id.'] inexistante');
        return $this->render('news/theme.html.twig',
            array('theme' => $theme,'infoxs' => $infoxs, 'pariteH' => $pariteH, 'pariteF' => $pariteF));
    }

    public function themeRemove($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $theme = $this->getDoctrine()->getRepository(Theme::class)->find($id);
        $infoxs = $this->getDoctrine()->getRepository(Infox::class)->findByTheme($id);
        $tmp = count($infoxs);
        $errMSG = "";
        if(!$theme) {
            throw $this->createNotFoundException('Theme[id='.$id.'] inexistante');
        }else{
            if ($tmp > 0){
                $errMSG = "Ce theme à des infoxs.";
                $themes = $this->getDoctrine()->getRepository(Theme::class)->findAll();

                return $this->render('news/themes.html.twig',
                    array('themes' => $themes, 'errMSG' => $errMSG));
            }else{
                $entityManager->remove($theme);
                $entityManager->flush();
                $themes = $this->getDoctrine()->getRepository(Theme::class)->findAll();
                return $this->render('news/themes.html.twig',
                    array('themes' => $themes, 'errMSG' => $errMSG));
            }

        }
    }

    public function themeAjouter(Request $request) {
        $errMSG = "";
        $theme = new Theme;
        $form = $this->createForm(ThemeType::class, $theme, ['action' => $this->generateUrl('fake_news_theme_ajouter')]);
        $form->add('submit', SubmitType::class, array('label' => 'Save'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($theme);
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_theme',
                array('id' => $theme->getId(), 'errMSG' => $errMSG));
        }
        return $this->render('news/theme_ajouter.html.twig',
            array('monFormulaire' => $form->createView()));
    }

    public function themeEdit($id, Request $request) {
        $errMSG = "";
        $entityManager = $this->getDoctrine()->getManager();
        $theme = $entityManager->getRepository(Theme::class)->find($id);
        if (!$theme) {
            throw $this->createNotFoundException('Theme[id='.$id.'] inexistante');
        }
        $form = $this->createForm(ThemeType::class, $theme, ['action' => $this->generateUrl('fake_news_theme_edit', array('id' => $id))]);
        $form->add('submit', SubmitType::class, array('label' => 'Edit'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_theme',
                array('id' => $theme->getId(), 'errMSG' => $errMSG));
        }
        return $this->render('news/theme_ajouter.html.twig',
            array('monFormulaire' => $form->createView()));
    }

    public function categories() {
        $errMSG = "";
        $categories = $this->getDoctrine()->getRepository(Categorie::class)->findAll();
        return $this->render('news/categories.html.twig',
            array('categories' => $categories, 'errMSG' => $errMSG));
    }

    public function categorie($id) {
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);
        $personnes = $this->getDoctrine()->getRepository(Personne::class)->findByCategorie($id);
        $infoxs = new ArrayCollection();
        foreach ($personnes as $personne) {
            $infoxs[] = $personne->getLike();
        }
        $viralite = 0; $nbInf = 0;
        foreach ($infoxs as $infox){
            foreach ($infox as $inf) {
                $nbInf = $nbInf + 1;
                $viralite = $viralite + $inf->getViralite();
            }
        }
        if ($nbInf != 0) {
            $moyViralite = round($viralite / $nbInf,2);
        }else {
            $moyViralite = 0;
        }
        if(!$categorie)
            throw $this->createNotFoundException('Categorie[id='.$id.'] inexistante');
        return $this->render('news/categorie.html.twig',
            array('categorie' => $categorie,'personnes' => $personnes, 'moyViralite' => $moyViralite));
    }

    public function categorieRemove($id) {
        $errMSG = "";
        $entityManager = $this->getDoctrine()->getManager();
        $categorie = $this->getDoctrine()->getRepository(Categorie::class)->find($id);
        $personnes = $this->getDoctrine()->getRepository(Personne::class)->findByCategorie($id);
        $tmp = count($personnes);
        if(!$categorie) {
            throw $this->createNotFoundException('Categorie[id='.$id.'] inexistante');
        }else{
            if ($tmp > 0) {
                $errMSG = "Cette catégorie à des personnes.";
                $categories= $this->getDoctrine()->getRepository(Categorie::class)->findAll();
                return $this->render('news/categories.html.twig',
                    array('categories' => $categories, 'errMSG' => $errMSG));
            }else{
                $entityManager->remove($categorie);
                $entityManager->flush();
                $categories= $this->getDoctrine()->getRepository(Categorie::class)->findAll();
                return $this->render('news/categories.html.twig',
                    array('categories' => $categories, 'errMSG' => $errMSG));
            }
        }
    }

    public function categorieAjouter(Request $request) {
        $errMSG = "";
        $categorie = new Categorie;
        $form = $this->createForm(CategorieType::class, $categorie, ['action' => $this->generateUrl('fake_news_categorie_ajouter')]);
        $form->add('submit', SubmitType::class, array('label' => 'Save'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_categorie',
                array('id' => $categorie->getId(), 'errMSG' => $errMSG));
        }
        return $this->render('news/categorie_ajouter.html.twig',
            array('monFormulaire' => $form->createView()));
    }

    public function categorieEdit($id, Request $request) {
        $errMSG = "";
        $entityManager = $this->getDoctrine()->getManager();
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException('Categorie[id='.$id.'] inexistante');
        }
        $form = $this->createForm(CategorieType::class, $categorie, ['action' => $this->generateUrl('fake_news_categorie_edit', array('id' => $id))]);
        $form->add('submit', SubmitType::class, array('label' => 'Edit'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_categorie',
                array('id' => $categorie->getId(), 'errMSG' => $errMSG));
        }
        return $this->render('news/categorie_ajouter.html.twig',
            array('monFormulaire' => $form->createView()));
    }

    public function infoxs() {
        $infoxs = $this->getDoctrine()->getRepository(Infox::class)->findAll();
        return $this->render('news/infoxs.html.twig',
            array('infoxs' => $infoxs));
    }

    public function infox($id) {
        $infox = $this->getDoctrine()->getRepository(Infox::class)->find($id);
        $personnes = $infox->getLike();
        $homme = 0; $femme = 0;
        $total = count($personnes);
        foreach ($personnes as $pers) {
            if ($pers->getSexe() == "M") {
                $homme = $homme + 1;
            }
            if ($pers->getSexe() == "F") {
                $femme = $femme + 1;
            }
        }
        if ($total != 0) {
            $pariteH = round(($homme * 100) / $total, 1);
            $pariteF = 100 - $pariteH;
        }else{
            $pariteH = 0; $pariteF = 0;
        }

        if(!$infox)
            throw $this->createNotFoundException('Infox[id='.$id.'] inexistante');
        return $this->render('news/infox.html.twig',
            array('infox' => $infox, 'personnes' => $personnes, 'pariteH' => $pariteH, 'pariteF' => $pariteF));
    }

    public function infoxRemove($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $infox = $this->getDoctrine()->getRepository(Infox::class)->find($id);
        if(!$infox) {
            throw $this->createNotFoundException('Infox[id='.$id.'] inexistante');
        }else{
            $entityManager->remove($infox);
            $entityManager->flush();
            $infoxs= $this->getDoctrine()->getRepository(Infox::class)->findAll();
            return $this->render('news/infoxs.html.twig',
                array('infoxs' => $infoxs));
        }
    }

    public function infoxAjouter(Request $request) {
        $infox = new Infox();
        $form = $this->createForm(InfoxType::class, $infox, ['action' => $this->generateUrl('fake_news_infox_ajouter')]);
        $form->add('submit', SubmitType::class, array('label' => 'Save'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($infox);
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_infoxs',
                array('id' => $infox->getId()));
        }
        return $this->render('news/infox_ajouter.html.twig',
            array('monFormulaire' => $form->createView()));
    }

    public function infoxEdit($id, Request $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $infox = $entityManager->getRepository(Infox::class)->find($id);
        if (!$infox) {
            throw $this->createNotFoundException('Infox[id='.$id.'] inexistante');
        }
        $form = $this->createForm(InfoxType::class, $infox, ['action' => $this->generateUrl('fake_news_infox_edit', array('id' => $id))]);
        $form->add('submit', SubmitType::class, array('label' => 'Edit'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_infox',
                array('id' => $infox->getId()));
        }
        return $this->render('news/infox_ajouter.html.twig',
            array('monFormulaire' => $form->createView()));
    }

    public function personnes() {
        $personnes = $this->getDoctrine()->getRepository(Personne::class)->findAll();
        return $this->render('news/personnes.html.twig',
            array('personnes' => $personnes));
    }

    public function personne($id) {
        $personne = $this->getDoctrine()->getRepository(Personne::class)->find($id);
        $infoxs = $this->getDoctrine()->getRepository(Infox::class)->findByPersonne($id);
        if(!$personne)
            throw $this->createNotFoundException('Personne[id='.$id.'] inexistante');
        return $this->render('news/personne.html.twig',
            array('personne' => $personne, 'infoxs' => $infoxs));
    }

    public function personneAjouter(Request $request) {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne, ['action' => $this->generateUrl('fake_news_personne_ajouter')]);
        $form->add('submit', SubmitType::class, array('label' => 'Save'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_personnes',
                array('id' => $personne->getId()));
        }
        return $this->render('news/personne_ajouter.html.twig',
            array('monFormulaire' => $form->createView()));
    }

    public function personneEdit($id, Request $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $personne = $entityManager->getRepository(Personne::class)->find($id);
        if (!$personne) {
            throw $this->createNotFoundException('Personne[id='.$id.'] inexistante');
        }
        $form = $this->createForm(PersonneEditType::class, $personne, ['action' => $this->generateUrl('fake_news_personne_edit', array('id' => $id))]);
        $form->add('submit', SubmitType::class, array('label' => 'Edit'));
        $form->handleRequest($request);
        if ($form->isSubmitted()  && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('fake_news_personne',
                array('id' => $personne->getId()));
        }
        return $this->render('news/personne_edit.html.twig',
            array('monFormulaire' => $form->createView(), 'personne' => $personne));
    }

    public function personneRemove($id) {
        $entityManager = $this->getDoctrine()->getManager();
        $personne = $this->getDoctrine()->getRepository(Personne::class)->find($id);
        if(!$personne) {
            throw $this->createNotFoundException('Perosnne[id='.$id.'] inexistante');
        }else{
            $entityManager->remove($personne);
            $entityManager->flush();
            $personnes= $this->getDoctrine()->getRepository(Personne::class)->findAll();
            return $this->render('news/personnes.html.twig',
                array('personnes' => $personnes));
        }
    }

    public function gotoShareInfox($id) {
        $personnes = $this->getDoctrine()->getRepository(Personne::class)->findAll();
        $persLike = $this->getDoctrine()->getRepository(Personne::class)->findByInfox($id);
        $size = count($persLike);
        $persToSend = new ArrayCollection();
        $check = 0;
        foreach ($personnes as $pers) {
            foreach ($persLike as $like) {
                if ($like != $pers) {
                    $check = $check + 1;
                }else {
                    $check = 0;
                }
            }
            if ($check == $size) {
                $persToSend[] = $pers;
                $check = 0;
            }
        }
        $infox = $this->getDoctrine()->getRepository(Infox::class)->find($id);
        return $this->render('news/share_infox.html.twig', array('personnes' => $persToSend, 'infox' => $infox));
    }

    public function infoxShare($id, $pers) {
        $entityManager = $this->getDoctrine()->getManager();
        $infox = $this->getDoctrine()->getRepository(Infox::class)->find($id);
        $personne = $this->getDoctrine()->getRepository(Personne::class)->find($pers);
        $infox->addLike($personne);
        $entityManager->flush();
        $personnes = $this->getDoctrine()->getRepository(Personne::class)->findByInfox($id);

        $homme = 0; $femme = 0;
        $total = count($personnes);
        foreach ($personnes as $pers) {
            if ($pers->getSexe() == "M") {
                $homme = $homme + 1;
            }
            if ($pers->getSexe() == "F") {
                $femme = $femme + 1;
            }
        }
        if ($total != 0) {
            $pariteH = round(($homme * 100) / $total, 1);
            $pariteF = 100 - $pariteH;
        }else{
            $pariteH = 0; $pariteF = 0;
        }

        return $this->render('news/infox.html.twig', array('infox' => $infox, 'personnes' => $personnes, 'pariteH' => $pariteH, 'pariteF' => $pariteF));
    }

    public function infoxUnshare($id, $pers) {
        $entityManager = $this->getDoctrine()->getManager();
        $infox = $this->getDoctrine()->getRepository(Infox::class)->find($id);
        $personne = $this->getDoctrine()->getRepository(Personne::class)->find($pers);
        $infox->removeLike($personne);
        $entityManager->flush();
        $personnes = $this->getDoctrine()->getRepository(Personne::class)->findByInfox($id);

        $homme = 0; $femme = 0;
        $total = count($personnes);
        foreach ($personnes as $pers) {
            if ($pers->getSexe() == "M") {
                $homme = $homme + 1;
            }
            if ($pers->getSexe() == "F") {
                $femme = $femme + 1;
            }
        }
        if ($total != 0) {
            $pariteH = round(($homme * 100) / $total, 1);
            $pariteF = 100 - $pariteH;
        }else{
            $pariteH = 0; $pariteF = 0;
        }

        return $this->render('news/infox.html.twig', array('infox' => $infox, 'personnes' => $personnes, 'pariteH' => $pariteH, 'pariteF' => $pariteF));
    }

    public function personneUnshare($id, $pers) {
        $entityManager = $this->getDoctrine()->getManager();
        $infox = $this->getDoctrine()->getRepository(Infox::class)->find($id);
        $personne = $this->getDoctrine()->getRepository(Personne::class)->find($pers);
        $infox->removeLike($personne);
        $entityManager->flush();
        $infoxs = $this->getDoctrine()->getRepository(Infox::class)->findByPersonne($pers);
        return $this->render('news/personne.html.twig', array('infoxs' => $infoxs, 'personne' => $personne));
    }
}