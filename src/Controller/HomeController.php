<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $user=$this->getUser();
        if($user){
        if(in_array('ROLE_ADMIN',$user->getRoles()))
            return $this->redirectToRoute('app_user_index');
        else if(in_array('ROLE_USER',$user->getRoles()))
            return $this->redirectToRoute('app_commande_index');
        }
        else
            return $this->redirectToRoute('app_login');
      
    }
}
