<?php

namespace App\Controller;

use App\Entity\Concert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ConcertType;

class ConcertController extends AbstractController
{
    #[Route('/concert', name: 'concert')]
    public function index(): Response
    {
        return $this->render('concert/index.html.twig', [
            'controller_name' => 'Licence APIDAE !',
        ]);
    }

    #[Route('/concerts/list', name : "list_concerts")]
    public function list(ManagerRegistry $doctrine): Response
    {
        return $this->render('concert/list.html.twig', [
            "concerts" => $doctrine->getRepository(Concert::class)->findAll()
        ]);
    }

    #[Route('/concert/{id}', name : "show_concert")]
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        return $this->render('concert/show.html.twig', [
            "concert" => $doctrine->getRepository(Concert::class)->find($id)
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

    #[Route('/concert/admin/{admin_id}', name : "concert_create")]
    public function createConcert(Request $request, $admin_id) : Response
    {
        $show = new Concert();
        $form = $this->createForm(ConcertType::class, $show);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $show = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($show);
            $entityManager->flush();
            return $this->redirectToRoute('concert_success', ['admin_id' => $admin_id, 'id' => $show->getId()]);
        }
        return $this->render('concert/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/concert/admin/{admin_id}/delete/{id}', name : "concert_delete")]
    public function deleteConcert(Request $request, Concert $concert) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($concert);
        $entityManager->flush();

        return $this->redirectToRoute('list_concerts');
    }

    #[Route('/concert/admin/{admin_id}/edit/{id}', name : "concert_update")]
    public function updateConcert(Request $request, Concert $concert, $admin_id) : Response
    {
        $show = $concert;
        $form = $this->createForm(ConcertType::class, $show);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $show = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($show);
            $entityManager->flush();
            return $this->redirectToRoute('concert_success', ['admin_id' => $admin_id, 'id' => $show->getId()]);
        }
        return $this->render('concert/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/concert/admin/{admin_id}/{id}', name : "concert_success")]
    public function successConcert(ManagerRegistry $doctrine, $id) : Response
    {
        return $this->render('concert/success.html.twig', ["concert" => $doctrine->getRepository(Concert::class)->find($id)]);
    }
} 
