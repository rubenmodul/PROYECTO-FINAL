<?php

namespace App\Controller;

use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'new_contact_api', methods: ['POST'])]
    public function apiRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuarios();
        $content=$request->getContent();
        $user->fromJson($content);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
            $user,
            $user->getPassword()
            )
        );

        $numUsers=$entityManager->getRepository(Usuarios::class)
            ->createQueryBuilder('user')
            ->select('count(user.id)')
            ->getQuery()
            ->getSingleScalarResult();
        if($numUsers < 1) {
            $user->setRoles(['ROLE_ADMIN']);
        }
        $entityManager->persist($user);
        $entityManager->flush();
        $response = [
            'ok' => true,
            'message' => "User inserted",
            ];
        return new JsonResponse($response);
    }
}
