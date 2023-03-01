<?php

namespace App\Controller;

use App\Entity\Usuarios;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pokemon;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api', name: 'api_')]
class PokemonController extends AbstractController
{
    #[Route('/pokemon', name: 'pokemons', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $pokemons = $doctrine
            ->getRepository(Pokemon::class)
            ->findAll();

        $data = [];

        foreach ($pokemons as $pokemon) {
            $data[] = [
                'id' => $pokemon->getId(),
                'nombre' => $pokemon->getNombre(),
                'tipo' => $pokemon->getTipo(),
                'altura' => $pokemon->getAltura(),
                'peso' => $pokemon->getPeso(),
                'ataques' => $pokemon->getAtaques(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/pokemon/{id}', name: 'mostrar_pokemon', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $pokemon = $doctrine->getRepository(Pokemon::class)->find($id);

        $data = [
            'id' => $pokemon->getId(),
            'nombre' => $pokemon->getNombre(),
            'tipo' => $pokemon->getTipo(),
            'altura' => $pokemon->getAltura(),
            'peso' => $pokemon->getPeso(),
            'ataques' => $pokemon->getAtaques(),
        ];

        return $this->json($data);
    }

    #[Route('/pokemon', name: 'nuevo_pokemon', methods: ['POST'])]
    public function new (ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent(), true);

        $pokemon = new Pokemon();
        $pokemon->setNombre($data['nombre']);
        $pokemon->setTipo($data['tipo']);
        $pokemon->setAltura($data['altura']);
        $pokemon->setPeso($data['peso']);
        $pokemon->setAtaques($data['ataques']);

        $entityManager->persist($pokemon);
        $entityManager->flush();

        return $this->json('Pokemon ' . $pokemon->getNombre() . ' con la id ' . $pokemon->getId() . ' ha sido creado correctamente');
    }

    #[Route('/pokemon/{id}', name: 'actualizar_pokemon', methods: ['PUT'])]
    public function update(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $pokemon = $entityManager->getRepository(Pokemon::class)->find($id);
        $data = json_decode($request->getContent(), true);

        $pokemon->setNombre($data['nombre'] ?? $pokemon->getNombre());
        $pokemon->setTipo($data['tipo'] ?? $pokemon->getTipo());
        $pokemon->setAltura($data['altura'] ?? $pokemon->getAltura());
        $pokemon->setPeso($data['peso'] ?? $pokemon->getPeso());
        $pokemon->setAtaques($data['ataques'] ?? $pokemon->getAtaques());

        $entityManager->flush();

        return $this->json('Pokemon con la id ' . $pokemon->getId() . ' ha sido actualizado correctamente');
    }

    #[Route('/pokemon/{id}', name: 'eliminar_pokemon', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $pokemon = $entityManager->getRepository(Pokemon::class)->find($id);


        $entityManager->remove($pokemon);
        $entityManager->flush();

        return $this->json('Pokemon ' . $pokemon->getNombre() . ' con la id ' . $pokemon->getId() . ' ha sido eliminado correctamente');
    }
}