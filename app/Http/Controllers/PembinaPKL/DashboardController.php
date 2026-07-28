<?php
namespace App\Http\Controllers\PembinaPKL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller { 
    public function index(){ 
        return view("pembina-pkl.dashboard", 
        ["user"=>Auth::user()]); 
    }
}
