<?php

namespace App\Controller;

use App\Entity\Band;
use App\Form\BandType;
use App\Repository\BandRepository;
use App\Repository\ConcertRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/band')]
class BandController extends AbstractController
{
    #[Route('/', name: 'band_index', methods: ['GET'])]
    public function index(BandRepository $bandRepository): Response
    {
        return $this->render('band/index.html.twig', [
            'bands' => $bandRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'band_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $band = new Band();
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($band);
            $entityManager->flush();

            return $this->redirectToRoute(
                'band_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('band/new.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'band_show', methods: ['GET'])]
    public function show(
        Band $band,
        ConcertRepository $concertRepository
    ): Response {
        return $this->render('band/show.html.twig', [
            'band' => $band,
            'concertsComing' => $concertRepository->findAllComingByBand(
                $band->getId()
            ),
            'isFavorite' => $this->getUser()
                ->getFavoriteBands()
                ->contains($band),
        ]);
    }

    #[Route('/addFavorite/{id}', name : "add_favorite_band", methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function addFavorite(
        Band $band,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'addFavorite' . $band->getId(),
                $request->request->get('_token')
            )
        ) {
            $this->getUser()->addFavoriteBand($band);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/removeFavorite/{id}', name : "remove_favorite_band", methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function removeFavorite(
        Band $band,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'deleteFavorite' . $band->getId(),
                $request->request->get('_token')
            )
        ) {
            $this->getUser()->removeFavoriteBand($band);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/{id}/edit', name: 'band_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(
        Request $request,
        Band $band,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(BandType::class, $band);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'band_show',
                ['id' => $band->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('band/edit.html.twig', [
            'band' => $band,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'band_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(
        Request $request,
        Band $band,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $band->getId(),
                $request->request->get('_token')
            )
        ) {
            try {
                $entityManager->remove($band);
                $entityManager->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->redirectToRoute(
            'band_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
