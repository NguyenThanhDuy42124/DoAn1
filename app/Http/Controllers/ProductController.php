<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // tạm thời return view rỗng, sau này mày nhét CRUD vào
        return view('admin.products.index');
    }
}