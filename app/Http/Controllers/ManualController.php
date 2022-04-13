<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManualController extends Controller
{
    public function index()
    {
        return response()
            ->download(storage_path("app/manuals/manual.pdf"), 
                'manual.pdf',
                 ['Content-type' => 'application/pdf']);
    }
}
