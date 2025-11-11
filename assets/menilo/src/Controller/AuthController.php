<?php

namespace App\Controller;

use App\Controller\AppController;

class AuthController extends AppController
{
    public function authemailverify()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function authforgotpassword()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function authresetpassword()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function authsignin()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function authsignout()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function authsignup()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function authtwostepverify()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function comingsoon()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function error()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function notauthorize()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
    public function undermaintenance()
    {
        $this->viewBuilder()->setLayout('auth'); // Optional if your layout is named 'default'
        $this->set('title', 'Your Page Title');
    }
}