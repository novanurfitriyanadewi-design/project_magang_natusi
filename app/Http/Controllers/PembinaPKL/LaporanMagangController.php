<?php
namespace App\Http\Controllers\PembinaPKL;
use App\Http\Controllers\Controller;
class LaporanMagangController extends Controller { 
    public function index() { 
        return view("pembina-pkl.dashboard"); 
    } 
}
