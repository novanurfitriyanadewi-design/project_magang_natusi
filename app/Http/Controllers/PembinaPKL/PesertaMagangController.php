<?php
namespace App\Http\Controllers\PembinaPKL;
use App\Http\Controllers\Controller;
class PesertaMagangController extends Controller { 
    public function index() { 
        return view("pembina-pkl.dashboard"); 
    } 
}
