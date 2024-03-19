<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use App\Repository\ClientRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(#[CurrentUser] ?User $user,ClientRepository $clientRepository, CommandeRepository $commandeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if($this->isGranted('ROLE_ADMIN'))
            $query=$commandeRepository->findAll();
        else{
            $client=$clientRepository->findOneBy(array('user' => $user));
            $query=$commandeRepository->findBy(array('client' => $client));
        }
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );
        
        return $this->render('commande/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(#[CurrentUser] ?User $user,ClientRepository $clientRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $commande->setDate(new \DateTime());
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!$this->isGranted('ROLE_ADMIN')){
                $client=$clientRepository->findOneBy(array('user' =>$user));
                $commande->setClient($client);
            }
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        $produits=$commande->getLigneCommandes();
        $total=0;
        foreach ($produits as $p){
            $qt=$p->getQt();
            $prix=$p->getProduit()->getPrix();
            $total+=$prix*$qt;
        }
        return $this->render('commande/show.html.twig', [
            'commande' => $commande, 'total' =>$total
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
