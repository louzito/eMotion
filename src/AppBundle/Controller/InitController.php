<?php

namespace AppBundle\Controller;

use AppBundle\Service\RedirectionService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class InitController extends Controller
{
//    use filterRechercheTrait;

    public function __construct(RedirectionService $redirectionService)
    {
        $redirectionService->redirectionLogin();
    }

}
