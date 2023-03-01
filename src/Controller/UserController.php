<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use ContainerORvOApe\getErrorHandler_ErrorRenderer_HtmlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use function MongoDB\BSON\toJSON;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(Request $request, User $user): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if($currentUser->getId() == $user->getId()) {
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        } else {
            return $this->redirectToRoute('app_product_index');
        }
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if($currentUser->getId() == $user->getId()){
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $userRepository->add($user, true);
                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        } else {
            return $this->redirectToRoute('app_product_index');
        }

    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
