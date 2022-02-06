<?php

namespace App\Controller;

use App\Entity\Concert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ConcertType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ConcertController extends AbstractController
{

    

    #[Route('/concert', name : "list_concerts")]
    public function list(ManagerRegistry $doctrine): Response
    {
        return $this->render('concert/list.html.twig', [
            "concerts" => $doctrine->getRepository(Concert::class)->findAll()
        ]);
    }

    

    #[Route('/concerts/archives', name : "archives_concert")]
    public function archives(ManagerRegistry $doctrine) : Response
    {
        return $this->render('concert/archives.html.twig', [
            "concertsPassed" => $doctrine->getRepository(Concert::class)->findAllPassed(),
            "concertsComing" => $doctrine->getRepository(Concert::class)->findAllComing()

        ]);
    }

    #[Route('/concert/create', name : "concert_create")]
    #[IsGranted("ROLE_ADMIN")]
    public function createConcert(Request $request) : Response
    {
        $show = new Concert();
        $form = $this->createForm(ConcertType::class, $show);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $show = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($show);
            $entityManager->flush();
            return $this->redirectToRoute('concert_success', ['id' => $show->getId()]);
        }
        return $this->render('concert/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/concert/{id}', name : "show_concert")]
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        $concert = $doctrine->getRepository(Concert::class)->find($id);

        return $this->render('concert/show.html.twig', [
            "concert" => $concert,
            "isFavorite" => $this->getUser()->getFavoriteConcerts()->contains($concert)
        ]);
    }

    #[Route('/concert/addFavorite/{id}', name : "add_favorite_concert", methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function addFavorite(ManagerRegistry $doctrine, $id, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $concert = $doctrine->getRepository(Concert::class)->find($id);
        if (
            $this->isCsrfTokenValid(
                'addFavorite' . $concert->getId(),
                $request->request->get('_token')
            )
        ) {
            $this->getUser()->addFavoriteConcert($concert);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }

       
        
        return  $this->redirectToRoute('show_concert', ["id" => $id]);
    }

    #[Route('/concert/removeFavorite/{id}', name : "remove_favorite_concert", methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function removeFavorite(ManagerRegistry $doctrine, $id, EntityManagerInterface $entityManager, Request $request) : Response
    {
        $concert = $doctrine->getRepository(Concert::class)->find($id);
        if (
            $this->isCsrfTokenValid(
                'deleteFavorite' . $concert->getId(),
                $request->request->get('_token')
            )
        ) {
            $this->getUser()->removeFavoriteConcert($concert);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }
        
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/concert/delete/{id}', name : "concert_delete")]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteConcert(Request $request, Concert $concert) : Response
    {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($concert);
            $entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $e) {
            return $this->redirect($request->headers->get('referer'));
        }
        

        return $this->redirectToRoute('list_concerts');
    }

    #[Route('/concert/edit/{id}', name : "concert_update")]
    #[IsGranted("ROLE_ADMIN")]
    public function updateConcert(Request $request, Concert $concert,) : Response
    {
        $show = $concert;
        $form = $this->createForm(ConcertType::class, $show);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $show = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($show);
            $entityManager->flush();
            return $this->redirectToRoute('concert_success', ['id' => $show->getId()]);
        }
        return $this->render('concert/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/concert/success/{id}', name : "concert_success")]
    #[IsGranted("ROLE_ADMIN")]
    public function successConcert(ManagerRegistry $doctrine, $id) : Response
    {
        return $this->render('concert/success.html.twig', ["concert" => $doctrine->getRepository(Concert::class)->find($id)]);
    }

    #[Route('/page/{page}', name : "upcoming_concert")]
    public function upcoming(ManagerRegistry $doctrine, $page = 1): Response
    {
        $limit = 5;
        $concerts = $doctrine->getRepository(Concert::class)->findAllComingByPage($page, $limit);
        $totalConcerts = $concerts->count();
        $maxPages = ceil($totalConcerts / $limit);
        $thisPage = $page;
        return $this->render('concert/upcoming.html.twig', [
            "concertsComing" => $concerts,
            "maxPages" => $maxPages,
            "thisPage" => $thisPage
        ]);
    }
} 
