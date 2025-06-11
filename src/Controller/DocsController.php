<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocsController extends AbstractController
{
    #[Route('/docs', name: 'app_docs')]
    public function index(): Response
    {
        $path = dirname(__DIR__, 2) . '/docs/API_USAGE.md';
        $content = is_readable($path) ? file_get_contents($path) : 'Documentation not found.';

        return $this->render('docs.html.twig', [
            'doc_content' => $content,
        ]);
    }
}
