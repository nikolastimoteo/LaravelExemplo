<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    //Chama a pagina principal
    public function index()
    {
        return view('pages.index');
    }

    //Chama a pagina sobre
    public function about()
    {
        return view('pages.about');
    }

    //Chama a pagina de serviços
    public function services()
    {
        return view('pages.services');
    }
}
