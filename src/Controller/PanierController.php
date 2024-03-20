<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Form\LigneCommandeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(Request $request): Response
    {
        $session=$request->getSession();
        $panier=$session->get('panier');
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }
    #[Route('/panier/{id}', name: 'app_panier_add', methods: ['GET', 'POST'])]
    public function newInPanier(Request $request, Produit $produit): Response
    {
        $ligneCommande = new LigneCommande();
        $ligneCommande->setProduit($produit);
        $form = $this->createForm(LigneCommandeType::class, $ligneCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ligneCommande->setProduit($produit);
            $session=$request->getSession();
            $panier=$session->get('panier');
            if(!$panier)
                $panier=[];
            $panier[]=$ligneCommande;
            $session->set('panier',$panier);
            return $this->redirectToRoute('app_produit_index',[], Response::HTTP_SEE_OTHER);
        }
        return $this->render('panier/addPanier.html.twig', [
            'form' => $form,
            'produit' => $produit
        ]);
    }
}
