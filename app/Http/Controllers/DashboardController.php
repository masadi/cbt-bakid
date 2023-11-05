<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
   public function index()
   {
      $data = ['page' => 'dashboard'];
      return response()->json($data);
   }
   public function no_access(){
      abort(403, 'Akses tidak sah!');
   }
}