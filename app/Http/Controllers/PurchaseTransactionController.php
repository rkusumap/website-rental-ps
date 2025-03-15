<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\MethodPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use File;

class PurchaseTransactionController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'id_supplier'        => 'nullable|uuid',
            'id_method_payment'  => 'required|uuid',
            'uang_dikeluarkan' => 'required|integer|min:0',
            'cart'                => 'required|array|min:1', // Ensure cart is not null and is an array
            'cart.*.product'      => 'required|uuid',        // Validate each product ID as a valid UUID
            'cart.*.qty'          => 'required|integer|min:1', // Ensure qty is not 0 or negative
        ];
        $pesan = [
            'id_supplier.uuid'          => 'Id Supplier tidak valid.',
            'id_method_payment.required'   => 'Metode pembayaran wajib diisi.',
            'id_method_payment.uuid'       => 'Metode pembayaran tidak valid.',
            'uang_dikeluarkan.required'  => 'Jumlah pembayaran wajib diisi.',
            'uang_dikeluarkan.integer'   => 'Jumlah pembayaran harus berupa angka.',
            'uang_dikeluarkan.min'       => 'Jumlah pembayaran harus minimal 0.',
            'cart.required'              => 'Keranjang tidak boleh kosong.',
            'cart.array'                 => 'Keranjang harus berupa array.',
            'cart.min'                   => 'Keranjang harus memiliki minimal satu produk.',
            'cart.*.product.required'    => 'Id Produk wajib diisi.',
            'cart.*.product.uuid'        => 'Id Produk tidak valid.',
            'cart.*.qty.required'        => 'Quantity produk wajib diisi.',
            'cart.*.qty.integer'         => 'Quantity produk harus berupa angka.',
            'cart.*.qty.min'             => 'Quantity produk harus minimal 1.',
        ];

        return Validator::make($request,$rule,$pesan);
    }

    public function index()
    {
        $get_module = get_module_id('purchase');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }
        return view('transaction.purchase.index', compact('get_module'));
    }

    public function json()
    {
        $datas = Transaction::with('supplier','method_payment')
        ->where('type_trx', 'P')
        ->orderBy('created_at','desc');
        ;
        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('purchase');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {
                    $btn_detail = '<a class="dropdown-item" href="' . route('purchase.show', $data->id_trx) . '">Detail</a>';
                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" onclick="location.href=' . "'" . route('purchase.edit', $data->id_trx) . "'" . ';" class="btn btn-sm btn-info">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {
                    $btn_hapus = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_trx . '" data-nama="' . $data->code_trx . '">Hapus</a>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '
                    <button type="button" class="btn btn-info btn-sm dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton1">
                        ' . $btn_detail . '
                        ' . $btn_hapus . '

                    </div>
                </div>
              ';
            })

            ->addColumn('total', function ($data) {
                return "Rp. ".rupiah_format($data->grand_total_trx);
            })
            ->addColumn('tanggal',function ($data) {
                return fdate($data->created_at,'HHDDMMYYYY');
            })
            ->rawColumns([])
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function create()
    {
        $dataProduct = Product::orderBy('name_product', 'ASC')->get();
        $dataSupplier = Supplier::orderBy('name_supplier', 'ASC')->get();
        $dataMethodPayment = MethodPayment::orderBy('name_mp', 'ASC')->get();
        return view('transaction.purchase.create',compact('dataProduct','dataSupplier','dataMethodPayment'));
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $transformed = [
                "id_supplier" => $request->id_supplier,
                "id_method_payment" => $request->id_method_payment,
                "uang_dikeluarkan" => rupiah_value($request->uang_dikeluarkan), // Convert to integer or new amount
                "cart" => []
            ];


            // Populate Cart
            foreach ($request->product as $index => $productId) {
                $transformed['cart'][] = [
                    "product" => $productId,
                    "price_per_product" => rupiah_value($request['price'][$index]),
                    "qty" => $request['qty'][$index]
                ];
            }

            $validator = $this->rules($transformed);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'pesan'=>$validator->errors()]);
            }
            else{
                $data = new Transaction;
                $data->type_trx = 'P';
                $data->supplier_trx = $transformed['id_supplier'];

                // Get the current month in Roman numeral format
                $month = date('n'); // Numeric representation of the month (1-12)
                $romanMonth = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][$month - 1];

                // Get the current year
                $year = date('Y');

                // Query to get the maximum transaction code for the current month and year
                $qw_po = DB::table('tb_transaction')
                    ->selectRaw("MAX(SUBSTRING_INDEX(SUBSTRING_INDEX(code_trx, '/', -1), 'P', -1)) AS idmax")
                    ->where('type_trx', 'P')
                    ->whereYear('date_trx', $year)
                    ->whereRaw("SUBSTRING_INDEX(code_trx, '/', 1) = ?", [$romanMonth])
                    ->first();

                // Default starting transaction number
                $no_trx = '0000001';
                if ($qw_po && $qw_po->idmax) {
                    $tmp = ((int)$qw_po->idmax) + 1;
                    $no_trx = str_pad($tmp, 7, "0", STR_PAD_LEFT);
                }

                // Set the transaction code
                $data->code_trx = $romanMonth . '/' . $year . '/P' . $no_trx;

                $data->total_trx = 0;
                if ($request->jenis_diskon == 'persen') {
                    $data->discount_persen_trx = rupiah_value($request->diskon_value);
                    $data->discount_nominal_trx = 0;
                }
                else if($request->jenis_diskon == 'nominal') {
                    $data->discount_persen_trx = 0;
                    $data->discount_nominal_trx = rupiah_value($request->diskon_value);
                }
                else {
                    $data->discount_persen_trx = 0;
                    $data->discount_nominal_trx = 0;
                }

                if ($request->diskon_value != null) {
                    $data->type_discount_trx = $request->jenis_diskon;
                }
                $data->grand_total_trx = 0;
                $data->method_payment_trx = $transformed['id_method_payment'];
                $data->amount_given_trx = $transformed['uang_dikeluarkan'];


                $data->date_trx = date('Y-m-d');
                $data->save();

                $dataLog = $data;
                insert_log(null,'Insert Transaction',$dataLog->getKey(),json_encode($dataLog));

                $totalTransactionDetail = 0;
                foreach ($transformed['cart'] as $key_cart => $cart) {
                    //update hpp product
                    $dataProduct = Product::find($cart['product']);
                    $dataProduct->hpp_product = $cart['price_per_product'];
                    $dataProduct->save();

                    $dataDetail = new TransactionDetail;
                    $dataDetail->trx_trd                = $data->id_trx;
                    $dataDetail->product_trd            = $cart['product'];
                    $dataDetail->order_trd              = $key_cart + 1;
                    $dataDetail->qty_trd                = $cart['qty'];
                    $dataDetail->hpp_trd                = $cart['price_per_product'];
                    $dataDetail->price_trd              = 0;
                    $dataDetail->total_trd              = $cart['qty'] * $cart['price_per_product'];

                    $dataDetail->save();

                    $totalTransactionDetail += $dataDetail->total_trd;

                    $dataLog = $dataDetail;
                    insert_log(null,'Insert Transaction Detail',$dataLog->getKey(),json_encode($dataLog));
                }

                //update transaction
                $dataTrx = Transaction::find($data->id_trx);
                if ($data->discount_persen_trx != 0) {
                    $dataTrx->discount_nominal_trx = $totalTransactionDetail * ($data->discount_persen_trx / 100);
                }
                else if($data->discount_nominal_trx != 0){
                    $dataTrx->discount_persen_trx = $totalTransactionDetail > 0
                                                    ? ($data->discount_nominal_trx / $totalTransactionDetail) * 100
                                                    : 0;
                }
                $dataTrx->total_trx = $totalTransactionDetail;
                $dataTrx->grand_total_trx = $dataTrx->total_trx - $dataTrx->discount_nominal_trx;
                $dataTrx->change_given_trx = $dataTrx->amount_given_trx - $dataTrx->grand_total_trx;
                if ($dataTrx->change_given_trx < 0) {
                    DB::rollBack();
                    return response()->json(['status' => false,'pesan' => 'Uang tidak cukup']);
                }
                $dataTrx->save();
                $dataLog = $dataTrx;
                insert_log(null,'Insert Transaction update grand total',$dataLog->getKey(),json_encode($dataLog));

                $dataTransaction = $dataTrx;
                $name_type = reference('type_transaction', $dataTransaction->type_trx);
                $response = [
                    'id_transaction' => $dataTransaction->id_trx,
                    'type' => $dataTransaction->type_trx,
                    'nama_type' => $name_type,
                    'code' => $dataTransaction->code_trx,
                    'total' => $dataTransaction->total_trx,
                    'discount_persen' => $dataTransaction->discount_persen_trx,
                    'discount_nominal' => $dataTransaction->discount_nominal_trx,
                    'grand_total' => $dataTransaction->grand_total_trx,
                    'id_method_payment' => $dataTransaction->method_payment->id_mp ?? null,
                    'nama_method_payment' => $dataTransaction->method_payment->name_mp ?? null,
                    'uang_dikeluarkan' => $dataTransaction->amount_given_trx,
                    'uang_kembalian' => $dataTransaction->change_given_trx,
                    'id_supplier' => $dataTransaction->supplier->id_supplier ?? null,
                    'nama_supplier' => $dataTransaction->supplier->name_supplier ?? null,
                ];

                DB::commit();
                return response()->json(['status' => true,'pesan' => 'Success insert data']);

            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function edit($id) {
        $get_data = Transaction::find($id);
        $dataProduct = Product::orderBy('name_product', 'ASC')->get();
        $dataSupplier = Supplier::orderBy('name_supplier', 'ASC')->get();
        $dataMethodPayment = MethodPayment::orderBy('name_mp', 'ASC')->get();
        $dataTransactionDetail = TransactionDetail::where('trx_trd', $id)->orderBy('order_trd', 'ASC')->get();
        return view('transaction.purchase.edit', compact('get_data','dataProduct','dataSupplier','dataMethodPayment','dataTransactionDetail'));
    }

    public function update($id_trx,Request $request) {
        DB::beginTransaction();
        try {
            $transformed = [
                "id_supplier" => $request->id_supplier,
                "id_method_payment" => $request->id_method_payment,
                "uang_dikeluarkan" => rupiah_value($request->uang_dikeluarkan), // Convert to integer or new amount
                "cart" => []
            ];


            // Populate Cart
            foreach ($request->product as $index => $productId) {
                $transformed['cart'][] = [
                    "product" => $productId,
                    "price_per_product" => rupiah_value($request['price'][$index]),
                    "qty" => $request['qty'][$index]
                ];
            }

            $validator = $this->rules($transformed);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'pesan'=>$validator->errors()]);
            }
            else{
                // Retrieve existing transaction
                $data = Transaction::findOrFail($id_trx);

                // Update transaction before saving
                $dataLog = $data;
                insert_log(null, 'Update Transaction Before Save', $dataLog->getKey(), json_encode($dataLog));

                $data->supplier_trx = $transformed['id_supplier'];
                if ($request->jenis_diskon == 'persen') {
                    $data->discount_persen_trx = rupiah_value($request->diskon_value);
                    $data->discount_nominal_trx = 0;
                }
                else if($request->jenis_diskon == 'nominal') {
                    $data->discount_persen_trx = 0;
                    $data->discount_nominal_trx = rupiah_value($request->diskon_value);
                }
                else {
                    $data->discount_persen_trx = 0;
                    $data->discount_nominal_trx = 0;
                }

                if ($request->diskon_value != null) {
                    $data->type_discount_trx = $request->jenis_diskon;
                }

                $data->method_payment_trx = $transformed['id_method_payment'];
                $data->amount_given_trx = $transformed['uang_dikeluarkan'];




                $totalTransactionDetail = 0;

                // Update or insert transaction details
                foreach ($transformed['cart'] as $key_cart => $cart) {

                    // Check if the detail exists
                    $dataDetail = TransactionDetail::where('trx_trd', $data->id_trx)
                        ->where('product_trd', $cart['product'])
                        ->first();

                    if ($dataDetail) {
                        // Update existing detail
                        $dataDetail->qty_trd = $cart['qty'];
                        $dataDetail->hpp_trd = $cart['price_per_product'];
                        $dataDetail->price_trd = 0;
                        $dataDetail->total_trd = $cart['qty'] * $cart['price_per_product'];

                        $dataDetail->save();
                    } else {
                        // Insert new detail
                        $dataDetail = new TransactionDetail;
                        $dataDetail->trx_trd = $data->id_trx;
                        $dataDetail->product_trd = $cart['product'];
                        $dataDetail->order_trd = $key_cart + 1;
                        $dataDetail->qty_trd = $cart['qty'];
                        $dataDetail->hpp_trd = $cart['price_per_product'];
                        $dataDetail->price_trd = 0;
                        $dataDetail->total_trd = $cart['qty'] * $cart['price_per_product'];

                        $dataDetail->save();
                    }

                    $totalTransactionDetail += $dataDetail->total_trd;

                    // Log each transaction detail
                    $dataLog = $dataDetail;
                    insert_log(null, 'Update/Insert Transaction Detail', $dataLog->getKey(), json_encode($dataLog));
                }

                // Delete removed transaction details
                $existingProducts = array_column($transformed['cart'], 'product');
                TransactionDetail::where('trx_trd', $data->id_trx)
                    ->whereNotIn('product_trd', $existingProducts)
                    ->delete();

                // Update the transaction totals
                if ($data->discount_persen_trx != 0) {
                    $data->discount_nominal_trx = $totalTransactionDetail * ($data->discount_persen_trx / 100);
                }
                else if($data->discount_nominal_trx != 0){
                    $data->discount_persen_trx = $totalTransactionDetail > 0
                                                    ? ($data->discount_nominal_trx / $totalTransactionDetail) * 100
                                                    : 0;
                }
                $data->total_trx = $totalTransactionDetail;
                $data->grand_total_trx = $data->total_trx - $data->discount_nominal_trx;
                $data->change_given_trx = $data->amount_given_trx - $data->grand_total_trx;
                if ($data->change_given_trx < 0) {
                    DB::rollBack();
                    return response()->json(['status' => false,'pesan' => 'Uang tidak cukup']);
                }
                $data->save();

                // Update transaction log
                $dataLog = $data;
                insert_log(null, 'Update Transaction', $dataLog->getKey(), json_encode($dataLog));

                $dataTransaction = $data;
                $name_type = reference('type_transaction', $dataTransaction->type_trx);
                $response = [
                    'id_transaction' => $dataTransaction->id_trx,
                    'type' => $dataTransaction->type_trx,
                    'nama_type' => $name_type,
                    'code' => $dataTransaction->code_trx,
                    'total' => $dataTransaction->total_trx,
                    'discount_persen' => $dataTransaction->discount_persen_trx,
                    'discount_nominal' => $dataTransaction->discount_nominal_trx,
                    'grand_total' => $dataTransaction->grand_total_trx,
                    'id_method_payment' => $dataTransaction->method_payment->id_mp ?? null,
                    'nama_method_payment' => $dataTransaction->method_payment->name_mp ?? null,
                    'uang_dikeluarkan' => $dataTransaction->amount_given_trx,
                    'uang_kembalian' => $dataTransaction->change_given_trx,
                    'id_supplier' => $dataTransaction->supplier->id_supplier ?? null,
                    'nama_supplier' => $dataTransaction->supplier->name_supplier ?? null,
                ];

                DB::commit();
                return response()->json(['status' => true,'pesan' => 'Success update data']);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show($id) {
        $get_data = Transaction::find($id);
        $dataProduct = Product::orderBy('name_product', 'ASC')->get();
        $dataCustomer = Customer::orderBy('name_customer', 'ASC')->get();
        $dataMethodPayment = MethodPayment::orderBy('name_mp', 'ASC')->get();

        return view('transaction.purchase.show', compact('get_data','dataProduct','dataCustomer','dataMethodPayment'));
    }

    public function json_show($id) {
        $datas = TransactionDetail::with('product')
        ->where('trx_trd', $id)
        ->orderBy('order_trd','desc');


        return Datatables::of($datas)
            ->addColumn('harga', function ($data) {
                return "Rp. ".rupiah_format($data->hpp_trd);
            })

            ->addColumn('total', function ($data) {
                return "Rp. ".rupiah_format($data->grand_total_trd);
            })

            ->rawColumns([])
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function destroy($id_trx) {
        DB::beginTransaction();
        try {
            $data = Transaction::find($id_trx);
            if (!empty($data)) {
                $dataLog = $data;
                insert_log(null,'Delete Transaction',$dataLog->getKey(),json_encode($dataLog));
                $data->delete();
                $dataTrd = TransactionDetail::where('trx_trd', $data->id_trx)->get();
                insert_log(null,'Delete Transaction Detail',$data->id_trx,json_encode($dataTrd));
                TransactionDetail::where('trx_trd', $data->id_trx)->delete();
                DB::commit();
                return response()->json(['status' => true]);
            }
            else{
                return response()->json(['status' => false,'pesan' => 'Data Tidak ditemukan']);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
