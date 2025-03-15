<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOpname;
use App\Models\LogStockCashier;
use App\Models\StockCashier;
use App\Models\Product;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class StockByCashierController extends Controller
{
    public function rules($request)
    {
        $rule = [
            'product_sbc' => 'required',

            'qty_sbc' => 'required|min:1',
        ];
        $pesan = [
            'product_sbc.required' => 'Produk Wajib di isi',

            'qty_sbc.required' => 'Jumlah Wajib di isi',
            'qty_sbc.min' => 'Jumlah Minimal 1',
        ];
        return Validator::make($request, $rule, $pesan);
    }


    public function rulesApprove($request)
    {
        $rule = [
            'hpp_stock_opname' => 'required',

        ];
        $pesan = [
            'hpp_stock_opname.required' => 'Harga Beli Produk Wajib di isi',

        ];
        return Validator::make($request, $rule, $pesan);
    }

    public function index() {
        $get_module = get_module_id('stock-by-cashier');
        if (!notAccessBackHome($get_module)) {
            return redirect('/home');
        }

        $dataProduct = Product::where('company_product', auth()->user()->company_user)->orderBy('name_product', 'ASC')->get();

        return view('stock-by-cashier', compact('get_module','dataProduct'));
    }

    public function json()
    {
        $datas = StockCashier::with('product')->orderBy('created_at', 'desc');
        $datas = $datas->where('company_sbc', auth()->user()->company_user);
        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                //get module akses
                $id_module = get_module_id('stock-by-cashier');

                //detail
                $btn_detail = '';
                if (isAccess('read', $id_module, auth()->user()->level_user)) {

                }

                //edit
                $btn_edit = '';
                if (isAccess('update', $id_module, auth()->user()->level_user)) {
                    $btn_edit = '<button type="button" data-id="' . $data->id_sbc . '" class="btn btn-sm btn-info btn-edit">Edit</button>';
                }

                //delete
                $btn_hapus = '';
                if (isAccess('delete', $id_module, auth()->user()->level_user)) {

                    $text = $data->product->name_product . ' dengan jumlah ' . $data->qty_sbc.', tanggal terakhir input '.fdate($data->updated_at,'HHDDMMYYYY');
                    $btn_hapus = '<button type="button" data-nama="'.$text.'" data-id="' . $data->id_sbc . '" class="btn btn-sm btn-danger btn-hapus">Hapus</button>';
                }
                return '
                <div class="btn-group">
                    ' . $btn_edit . '

                    ' . $btn_hapus . '
                </div>
              ';
            })

            ->addColumn('tgl', function ($data) {
                return fdate($data->updated_at,'HHDDMMYYYY');
            })

            ->addColumn('status', function ($data) {

                if ($data->status_sbc == 0) {
                    $text = 'Belum di Cek';
                    $color = 'btn-danger';
                    $btn_approve = 'btn-approve';
                }
                else if($data->status_sbc == 1){
                    $text = 'Sudah di Cek';
                    $color = 'btn-success';
                    $btn_approve = '';
                }
                $btn_status = '<button type="button" data-id="' . $data->id_sbc . '" class="btn '.$btn_approve.' btn-sm '.$color.'">'.$text.'</button>';
                return $btn_status;
            })
            ->rawColumns(['action', 'status'])
            ->addIndexColumn() //increment
            ->make(true);
    }

    public function show($id)
    {
        $get_data = StockCashier::find($id);
        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {

                foreach ($request->product_sbc as $keyProduct => $productVal) {
                    $checkProduct = StockCashier::where('product_sbc', $productVal)
                    ->where('company_sbc', auth()->user()->company_user)
                    ->first();

                    if ($checkProduct) {
                        $dataLog = $checkProduct;
                        insert_log('Update Stock By Cashier','Stock By Cashier',$dataLog->getKey(),json_encode($dataLog));

                        $checkProduct->qty_sbc = $request->qty_sbc[$keyProduct];
                        $checkProduct->status_sbc = 0;
                        $checkProduct->save();
                    }
                    else{
                        $new_data = new StockCashier();

                        $new_data->product_sbc = $productVal;
                        $new_data->qty_sbc = rupiah_value($request->qty_sbc[$keyProduct]);
                        $new_data->status_sbc = 0;
                        $new_data->company_sbc = auth()->user()->company_user;
                        $new_data->save();

                        $dataLog = $new_data;
                        insert_log('Add Stock By Cashier','Stock By Cashier',$dataLog->getKey(),json_encode($dataLog));
                    }

                    $new_data = new LogStockCashier();
                    $new_data->product_lsbc = $productVal;
                    $new_data->qty_lsbc = rupiah_value($request->qty_sbc[$keyProduct]);
                    $new_data->company_lsbc = auth()->user()->company_user;
                    $new_data->save();

                    $dataLog = $new_data;
                    insert_log('Add Stock By Cashier Log','Stock By Cashier Log',$dataLog->getKey(),json_encode($dataLog));
                }




                DB::commit();
                return response()->json(['status' => true,'type' => 'add']);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update($id,Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rules($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {
                $update_data =  StockCashier::find($id);

                $update_data->product_sbc = $request->product_sbc;
                $update_data->qty_sbc = rupiah_value($request->qty_sbc);
                $update_data->status_sbc = 0;
                $update_data->company_sbc = auth()->user()->company_user;
                $update_data->save();

                $dataLog = $update_data;
                insert_log('Update Stock By Cashier','Stock Opname',$dataLog->getKey(),json_encode($dataLog));

                $new_data = new LogStockCashier();
                $new_data->product_lsbc = $request->product_sbc;
                $new_data->qty_lsbc = rupiah_value($request->qty_sbc);
                $new_data->company_lsbc = auth()->user()->company_user;
                $new_data->save();

                $dataLog = $new_data;
                insert_log('Add Stock By Cashier Log','Stock By Cashier Log',$dataLog->getKey(),json_encode($dataLog));

                DB::commit();
                return response()->json(['status' => true,'type' => 'edit']);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $data = StockCashier::find($id);
            $dataLog = $data;
            insert_log('Delete Stock By Cashier','Stock By Cashier',$dataLog->getKey(),json_encode($dataLog));
            StockCashier::destroy($id);
            DB::commit();
            return response()->json(['status' => true]);
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function get_approve($id) {

        $response = array();
        $dataStockCashier = StockCashier::find($id);

        $response['input_id_sbc'] = $id;
        $response['name_product_approve'] = $dataStockCashier->product->name_product;
        $response['input_product_approve'] = $dataStockCashier->product_sbc;
        $response['qty_dari_kasir'] = $dataStockCashier->qty_sbc;

        $purchaseQty = DB::table('tb_transaction_detail')
            ->where('product_trd', $dataStockCashier->product_sbc)
            ->join('tb_transaction', 'tb_transaction.id_trx', '=', 'tb_transaction_detail.trx_trd')
            ->where('tb_transaction.type_trx', 'P')
            ->whereNull('tb_transaction.deleted_at')
            ->whereNull('tb_transaction_detail.deleted_at')
            ->sum('qty_trd');

        // Sum sale quantity from tb_transaction_detail (product_trd where type_trx = S)
        $saleQty = DB::table('tb_transaction_detail')
            ->where('product_trd', $dataStockCashier->product_sbc)
            ->join('tb_transaction', 'tb_transaction.id_trx', '=', 'tb_transaction_detail.trx_trd')
            ->where('tb_transaction.type_trx', 'S')
            ->whereNull('tb_transaction.deleted_at')
            ->whereNull('tb_transaction_detail.deleted_at')
            ->sum('qty_trd');

        // Sum purchase quantity from tb_stock_opname (product_so where type_so = P)
        $stockOpnamePurchaseQty = DB::table('tb_stock_opname')
            ->where('product_so', $dataStockCashier->product_sbc)
            ->where('type_so', 'P')
            ->whereNull('tb_stock_opname.deleted_at')
            ->sum('qty_so');

        // Sum sale quantity from tb_stock_opname (product_so where type_so = S)
        $stockOpnameSaleQty = DB::table('tb_stock_opname')
            ->where('product_so', $dataStockCashier->product_sbc)
            ->where('type_so', 'S')
            ->whereNull('tb_stock_opname.deleted_at')
            ->sum('qty_so');

        // Calculate stock
        $stock_product = $purchaseQty - $saleQty + $stockOpnamePurchaseQty - $stockOpnameSaleQty;

        $response['qty_dari_sistem'] = $stock_product;
        $response['stock_sesuai'] = 1;
        $response['input_jenis_stock_opname'] = '';
        $response['name_jenis_stock_opname'] = '';
        $response['qty_stock_opname'] = '';
        if ($stock_product < $dataStockCashier->qty_sbc) {
            $response['stock_sesuai'] = 0;
            $response['input_jenis_stock_opname'] = 'P';
            $response['name_jenis_stock_opname'] = 'Masuk';
            $response['qty_stock_opname'] = $dataStockCashier->qty_sbc - $stock_product;
        }
        else if($stock_product > $dataStockCashier->qty_sbc) {
            $response['stock_sesuai'] = 0;
            $response['input_jenis_stock_opname'] = 'S';
            $response['name_jenis_stock_opname'] = 'Keluar';
            $response['qty_stock_opname'] = $stock_product - $dataStockCashier->qty_sbc;
        }
        $response['hpp_stock_opname'] = $dataStockCashier->product->hpp_product;

        return response()->json(['data' => $response]);

    }

    public function post_approve(Request $request) {
        DB::beginTransaction();
        try {
            $validator = $this->rulesApprove($request->all());
            if ($validator->fails()) {
                return response()->json(['status' => false, 'pesan' => $validator->errors()]);
            } else {

                $id_sbc = $request->input_id_sbc;
                $update_data = StockCashier::find($id_sbc);
                $update_data->status_sbc = 1;
                $update_data->save();

                if ($request->stock_sesuai == '0') {
                    $new_data = new StockOpname();
                    $new_data->type_so = $request->jenis_stock_opname;
                    $new_data->product_so = $request->product_approve;
                    $new_data->qty_so = rupiah_value($request->qty_stock_opname);
                    $new_data->hpp_so = rupiah_value($request->hpp_stock_opname);

                    $new_data->grand_total_so = $new_data->qty_so * $new_data->hpp_so;
                    $new_data->description_so = $request->description_so;
                    $new_data->date_so = date('Y-m-d');
                    $new_data->day_so= date('d');
                    $new_data->month_so= date('m');
                    $new_data->year_so= date('Y');
                    $new_data->company_so = auth()->user()->company_user;
                    $new_data->save();

                    $dataLog = $new_data;
                    insert_log('Add Stock Opname','Stock Opname',$dataLog->getKey(),json_encode($dataLog));
                }

                DB::commit();
                return response()->json(['status' => true,'type' => 'add']);
            }
        }
        catch (Exception  $e) {
            DB::rollBack();
            insert_log(null,'Error '.date('Y-m-d H:i:s'),'Error','error',json_encode($e));
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
