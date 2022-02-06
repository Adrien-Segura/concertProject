<?php

namespace App\Controller;

use App\Entity\ChangePassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangePasswordType;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/change_password', name: 'user_change_password')]
    #[IsGranted('ROLE_USER')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        ManagerRegistry $doctrine
    ): Response {
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine
                ->getRepository(User::class)
                ->upgradePassword(
                    $user,
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            return $this->render('user/index.html.twig', [
                'controller_name' => 'UserController',
            ]);
        }
        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/favorites', name: 'favorites_user')]
    #[IsGranted('ROLE_USER')]
    public function favorites(): Response
    {
        return $this->render('user/favorites.html.twig', [
            'favoriteConcerts' => $this->getUser()->getFavoriteConcerts(),
            'favoriteBands' => $this->getUser()->getFavoriteBands(),
            'favoriteMembers' => $this->getUser()->getFavoriteMembers(),
        ]);
    }
}
