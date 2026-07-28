<?php
namespace App\Http\Controllers\PembinaPKL;
use App\Http\Controllers\Controller;
class TugasController extends Controller { 
    public function index() { 
        return view("pembina-pkl.dashboard"); 
    } 
}
