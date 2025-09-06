<?php

namespace App\Controllers;

use App\Core\Controller;

class LegalController extends Controller
{
    public function mentionsLegales()
    {
        $this->render('legal/mentions-legales', [
            'pageTitle' => 'Mentions légales - Belgium Video Gaming',
            'pageDescription' => 'Mentions légales du site Belgium Video Gaming'
        ]);
    }

    public function politiqueConfidentialite()
    {
        $this->render('legal/politique-confidentialite', [
            'pageTitle' => 'Politique de confidentialité - Belgium Video Gaming',
            'pageDescription' => 'Politique de confidentialité et protection des données personnelles'
        ]);
    }

    public function cgu()
    {
        $this->render('legal/cgu', [
            'pageTitle' => 'Conditions générales d\'utilisation - Belgium Video Gaming',
            'pageDescription' => 'Conditions générales d\'utilisation du site Belgium Video Gaming'
        ]);
    }

    public function cookies()
    {
        $this->render('legal/cookies', [
            'pageTitle' => 'Politique des cookies - Belgium Video Gaming',
            'pageDescription' => 'Politique d\'utilisation des cookies sur Belgium Video Gaming'
        ]);
    }
}
