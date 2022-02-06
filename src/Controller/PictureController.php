<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\DBAL\Driver\API\MySQL\ExceptionConverter;
use Doctrine\DBAL\Driver\PDO\Exception;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PDOException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/picture')]
class PictureController extends AbstractController
{
    #[Route('/', name: 'picture_index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('picture/index.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'picture_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['attachment']->getData();
            $picture->setName($file->getClientOriginalName());

            $newfile = $file->move(
                'images',
                $picture->getAlternativeName() .
                    time() .
                    '.' .
                    $file->getClientOriginalExtension()
            );
            $picture->setUrl($newfile->getPathname());
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute(
                'picture_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'picture_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(Picture $picture): Response
    {
        return $this->render('picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    #[Route('/{id}/edit', name: 'picture_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(
        Request $request,
        Picture $picture,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filesystem = new Filesystem();
            $file = $form['attachment']->getData();
            $picture->setName($file->getClientOriginalName());

            $filesystem->remove($picture->getUrl()); //Remove old file

            $newfile = $file->move(
                'images',
                $picture->getAlternativeName() .
                    time() .
                    '.' .
                    $file->getClientOriginalExtension()
            );
            $picture->setUrl($newfile->getPathname());

            $entityManager->flush();

            return $this->redirectToRoute(
                'picture_show',
                ['id' => $picture->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'picture_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(
        Request $request,
        Picture $picture,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $picture->getId(),
                $request->request->get('_token')
            )
        ) {
            try {
                $url = $picture->getUrl();
                $entityManager->remove($picture);
                $filesystem = new Filesystem();
                $filesystem->remove($url); //Remove file
                $entityManager->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                return $this->redirect($request->headers->get('referer'));
            }
        }

        return $this->redirectToRoute(
            'picture_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
