<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member')]
class MemberController extends AbstractController
{
    #[Route('/', name: 'member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'member_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute(
                'member_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('member/new.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'member_show', methods: ['GET'])]
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
            'isFavorite' => $this->getUser()
                ->getFavoriteMembers()
                ->contains($member),
        ]);
    }

    #[Route('/addFavorite/{id}', name : "add_favorite_member", methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function addFavorite(
        Member $member,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'addFavorite' . $member->getId(),
                $request->request->get('_token')
            )
        ) {
            $this->getUser()->addFavoriteMember($member);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/removeFavorite/{id}', name : "remove_favorite_member", methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function removeFavorite(
        Member $member,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'deleteFavorite' . $member->getId(),
                $request->request->get('_token')
            )
        ) {
            $this->getUser()->removeFavoriteMember($member);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/{id}/edit', name: 'member_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(
        Request $request,
        Member $member,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'member_show',
                ['id' => $member->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('member/edit.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'member_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(
        Request $request,
        Member $member,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $member->getId(),
                $request->request->get('_token')
            )
        ) {
            try {
                $entityManager->remove($member);
                $entityManager->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->redirectToRoute(
            'member_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
