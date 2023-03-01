<?php

namespace App\Controller;

use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController {
    #[Route('/register', name: 'register_user', methods: ['POST'])]
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

    #[Route('/delete/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(Usuarios::class)->find($id);

        if (!$user) {
            $response = [
                'ok' => false,
                'message' => "User not found",
            ];
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $response = [
            'ok' => true,
            'message' => "User deleted",
        ];
        return new JsonResponse($response);
    }

    #[Route('/modificar/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(Usuarios::class)->find($id);

        if (!$user) {
            $response = [
                'ok' => false,
                'message' => "User not found",
            ];
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $content = $request->getContent();
        $user->fromJson($content);

        $entityManager->persist($user);
        $entityManager->flush();

        $response = [
            'ok' => true,
            'message' => "User updated",
        ];
        return new JsonResponse($response);
    }
}
?>