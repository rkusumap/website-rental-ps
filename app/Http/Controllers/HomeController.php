<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockOpname;
use App\Models\MethodPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use File;
use Auth;

class HomeController extends Controller
{

    public function index(Request $request) {

        if (Auth::user()->role->code_level == 'CUSTOMER') {
            // return view('dashboard');
            return redirect('/rental');
        }

        return view('dashboard');
    }

}
