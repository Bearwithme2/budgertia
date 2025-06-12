<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TesterController extends AbstractController
{
    #[Route('/tester', name: 'app_tester')]
    public function index(): Response
    {
        return $this->render('tester.html.twig');
    }
}
