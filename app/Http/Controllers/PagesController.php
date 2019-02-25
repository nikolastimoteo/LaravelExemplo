<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    //Chama a pagina principal
    public function index()
    {
        \App::setLocale('pt-br');
        //$title = 'Welcome to Laravel!';
        return view('pages.index');//->with('title', $title);
    }

    //Chama a pagina sobre
    public function about()
    {
        $title = 'About Us';
        return view('pages.about')->with('title', $title);
    }

    //Chama a pagina de serviços
    public function services()
    {
        $data = array(
            'title' => 'Services',
            'services' => ['Web Design', 'Programming', 'SEO']
        );
        return view('pages.services')->with($data);
    }
}
