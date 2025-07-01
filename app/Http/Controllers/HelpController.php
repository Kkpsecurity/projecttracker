<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display the help center main page.
     */
    public function index()
    {
        return view('help.index');
    }

    /**
     * Display getting started guide.
     */
    public function gettingStarted()
    {
        return view('help.getting-started');
    }

    /**
     * Display user guide.
     */
    public function userGuide()
    {
        return view('help.user-guide');
    }

    /**
     * Display FAQ page.
     */
    public function faq()
    {
        return view('help.faq');
    }

    /**
     * Display contact support page.
     */
    public function contact()
    {
        return view('help.contact');
    }

    /**
     * Display system documentation.
     */
    public function documentation()
    {
        return view('help.documentation');
    }
}
