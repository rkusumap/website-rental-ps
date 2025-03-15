<?php

use App\Models\Module;
use App\Models\Logs;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

if (! function_exists('insert_log')) {
    function insert_log($activity,$name_table,$ref_id,$json) {
        Logs::insert([
            'user_id' => auth()->user()->id ?? 'register_user',
            'session_id' => session()->getId() ?? null,
            'ip' => request()->ip(),
            'activity' => $activity ?? null,
            'name_table' => $name_table ?? null,
            'ref_id' => $ref_id ?? null,
            'json' => $json ?? null,
            'created_at' => now()
        ]);
    }
}

if (! function_exists('check_stock')) {
    function check_stock($id_product)
    {

        // Sum purchase quantity from tb_transaction_detail (product_trd where type_trx = P)
        $purchaseQty = DB::table('tb_transaction_detail')
        ->where('product_trd', $id_product)
        ->join('tb_transaction', 'tb_transaction.id_trx', '=', 'tb_transaction_detail.trx_trd')
        ->where('tb_transaction.type_trx', 'P')
        ->whereNull('tb_transaction.deleted_at')
        ->whereNull('tb_transaction_detail.deleted_at')
        ->sum('qty_trd');

        // Sum sale quantity from tb_transaction_detail (product_trd where type_trx = S)
        $saleQty = DB::table('tb_transaction_detail')
            ->where('product_trd', $id_product)
            ->join('tb_transaction', 'tb_transaction.id_trx', '=', 'tb_transaction_detail.trx_trd')
            ->where('tb_transaction.type_trx', 'S')
            ->whereNull('tb_transaction.deleted_at')
            ->whereNull('tb_transaction_detail.deleted_at')
            ->sum('qty_trd');

        // Sum purchase quantity from tb_stock_opname (product_so where type_so = P)
        $stockOpnamePurchaseQty = DB::table('tb_stock_opname')
            ->where('product_so', $id_product)
            ->where('type_so', 'P')
            ->whereNull('tb_stock_opname.deleted_at')
            ->sum('qty_so');

        // Sum sale quantity from tb_stock_opname (product_so where type_so = S)
        $stockOpnameSaleQty = DB::table('tb_stock_opname')
            ->where('product_so', $id_product)
            ->where('type_so', 'S')
            ->whereNull('tb_stock_opname.deleted_at')
            ->sum('qty_so');

        $stockRentalKeluar = DB::table('tb_rental_detail')
                        ->join('tb_rental','tb_rental.id_rental','=','tb_rental_detail.rental_rtd')
                        ->select('tb_rental_detail.rental_rtd')
                        ->where('product_rtd', $id_product)
                        // ->where('tb_rental.payment_status_rental', 'success')
                        ->whereIn('tb_rental.return_status_rental', [0,1,2])
                        ->whereNull('tb_rental.deleted_at')
                        ->groupBy('tb_rental_detail.rental_rtd')
                        ->get()
                        ->count();

        // Calculate stock
        $stock_product = $purchaseQty - $saleQty + $stockOpnamePurchaseQty - $stockOpnameSaleQty - $stockRentalKeluar;
        return $stock_product;
    }
}


if (! function_exists('check')) {
    function check($model)
    {
        return $model." activated";
    }
}

// * Reference Function, function that use for reference
if (! function_exists('reference')) {
    function reference($code,$value = null)
    {
        switch ($code) {
            case 'gender':
                (object) $data = [
                    'L' => 'Laki-laki',
                    'P' => 'Perempuan',
                ];
                return ($value == null) ? $data : $data[$value] ;
                break;
            case 'posisi_pegawai':
                (object) $data = [
                    'SHP' => 'Shopper',
                    'DRV' => 'Driver',
                    'ADM' => 'Admin CS',
                    'DIR' => 'Direktur',
                ];
                return ($value==null) ? $data : $data[$value];
                break;
            case 'status':
                (object) $data = [
                    '0' => 'Tidak Aktif',
                    '1' => 'Aktif',
                ];
                return ($value==null) ? $data : $data[$value];
                break;
            case 'type_transaction':
                (object) $data = [
                    'S' => 'Penjualan',
                    'P' => 'Pembelian',
                ];
                return ($value==null) ? $data : $data[$value];
                break;

            case 'status_rental':
                (object) $data = [
                    '0' => 'Menuggu Konfirmasi Admin',
                    '1' => 'Sudah Di Terima User',
                    '2' => 'Perlu Di Kembalikan',
                    '3' => 'Sudah Di Kembalikan',
                ];
                return ($value==null) ? $data : $data[$value];
                break;

            case 'status_bayar_d_none':
                (object) $data = [
                    'pending' => 'ok',
                    'success' => 'd-none',

                ];
                return ($value==null) ? $data : $data[$value];
                break;

            case 'status_bayar_warna':
                (object) $data = [
                    'pending' => 'badge-warning',
                    'success' => 'badge-success',

                ];
                return ($value==null) ? $data : $data[$value];
                break;
            default:
                return "not found";
                break;
        }
    }
}
if (! function_exists('show_image')) {
    function show_image($src=null)
    {
        if (!empty($src)) {
            if (file_exists(public_path($src))) {
                return asset($src);
            }else{
                return asset('static/not_found.png');
            }
        }else{
            return asset('static/not_found.png');
        }

    }
}

if (! function_exists('isAccess')) {
    function isAccess($act,$module,$level)
    {
        $role = new Role;
        $action = $role->where('level_gmd',$level)->where('module_gmd',$module)->first();

        //tidak memiliki akses
        if($action == NULL)
        {
            return FALSE;
        }
        if (strpos($action->action_gmd, $act) !== false) {
            return TRUE;
        }else{
            return FALSE;
        }
    }
}

if (! function_exists('notAccessBackHome')) {
    function notAccessBackHome($module)
    {
        $act = 'read';
        $level = auth()->user()->level_user;
        $role = new Role;
        $action = $role->where('level_gmd',$level)->where('module_gmd',$module)->first();

        //tidak memiliki akses
        if($action == NULL)
        {
            return FALSE;
        }
        if (strpos($action->action_gmd, $act) !== false) {
            return TRUE;
        }else{
            return FALSE;
        }
    }
}

if (! function_exists('get_module_id')) {
    function get_module_id($module)
    {
        $moduleData = Module::where('link_module',$module)->first();
        return $moduleData->id_module;
    }
}

if (! function_exists('isSelected')) {
    function isSelected($a,$b)
    {
        if($a == $b)
        {
            echo "selected";
        }
    }
}

if (! function_exists('rupiah_format')) {
    function rupiah_format($nominal)
    {
        return number_format($nominal,0,0,".");
    }
}

if (! function_exists('rupiah_value')) {
    function rupiah_value($nominal)
    {
        return str_replace('.','',$nominal);
    }
}

if (! function_exists('number_wa')) {
    function number_wa($number)
    {
        if($number == "")
        {
            return "";
        }
        $code = substr($number,0,1);
        if($code == "0")
        {
            // echo $code;
            $phone = "62".substr($number,1);
        }elseif($code == "62")
        {
            // echo $code;
            $phone = $number;
        }elseif($code == "+62")
        {
            // echo $code;
            $phone = substr($number,1);
        }else{
            $phone = $number;
        }
        return $phone;
    }
}

function xprint_r($print)
{
    echo "<pre>";
        print_r($print);
    echo "</pre>";
}

if (! function_exists('whatNumber')) {
    function whatNumber($number)
    {
        $number = strtolower($number);
        $words = array(
            'a' => "1",
            'b' => "2",
            'c' => "3",
            'd' => "4",
            'e' => "5",
            'f' => "6",
            'g' => "7",
            'h' => "8",
            'i' => "9",
            'j' => "10",
            'k' => "11",
            'l' => "12",
            'm' => "13",
            'n' => "14",
            'o' => "15",
            'p' => "16",
            'r' => "17",
            's' => "18",
            't' => "19",
            'u' => "20",
            'v' => "21",
            'w' => "22",
            'x' => "23",
            'y' => "24",
            'z' => "25",

        );
        return $words[$number];
    }
}

function method1($a,$b)
{
return ($a['item'][0]["group_transaction"] <= $b['item'][0]["group_transaction"]) ? -1 : 1;
}

if (! function_exists('smt_reference')) {
    function smt_reference($code,$value = null)
    {
        switch ($code) {
            case 'status_supplier':
                $data = array(
                    '0' => 'Belanja Langsung',
                    '1' => 'Whatsapp'
                );
                if($value != NULL)
                {
                    return $data[$value];
                }else{
                    return $data;
                }
                break;
            case 'supplier_position':
                $data = array(
                    'B' => 'Belakang',
                    'D' => 'Depan',
                    'T' => 'Tengah',
                    'L' => 'Luar'
                );
                if($value != NULL)
                {
                    return $data[$value];
                }else{
                    return $data;
                }
                break;

            case 'status_transaction':
                $data = array(
                    '0' => 'Menunggu Belanja',
                    '1' => 'Selesai Belanja',
                    '2' => 'Selesai'
                );
                if($value != NULL)
                {
                    return $data[$value];
                }else{
                    return $data;
                }
                break;
            case 'status_faq':
                $data = array(
                    '0' => 'Tidak Aktif',
                    '1' => 'Aktif'
                );
                if($value != NULL)
                {
                    return $data[$value];
                }else{
                    return $data;
                }
                break;

            default:
                # code...
                break;
        }
    }
}

if (! function_exists('fdate')) {
    function fdate($value, $format)
    {
        $date = explode("-", $value);

        $tgl = $date[2] ?? '';
        $bln = $date[1];
        $thn = $date[0];

        $return = "";
        switch ($format) {
            case "DDMMYYYY":
                $return = $tgl . " " . fbulan($bln) . " " . $thn;
                break;
            case "DD":
                $return = $tgl;
                break;
            case "MM":
                $return = $bln;
                break;
            case "YYYYY":
                $return = $thn;
                break;
            case "MMYYYY":
                $return = fbulan($bln) . " " . $thn;
                break;
            case "mm":
                $return = fbulan($bln);
                break;
                case "HHDDMMYYYY" :
                    $eks = explode(" ", $tgl);
                    $tgl = $eks[0];
                    $jam = $eks[1];
                list($H,$M,$S) = explode(":",$jam);
                    $return = $tgl." ".fbulan($bln)." ".$thn." | ".$H.":".$M.":".$S;
                break;
        }
        return $return;
    }
}
if(!function_exists('hp')){
    function hp($nohp) {
        // kadang ada penulisan no hp 0811 239 345
        $nohp = str_replace(" ","",$nohp);
        // kadang ada penulisan no hp (0274) 778787
        $nohp = str_replace("(","",$nohp);
        // kadang ada penulisan no hp (0274) 778787
        $nohp = str_replace(")","",$nohp);
        // kadang ada penulisan no hp 0811.239.345
        $nohp = str_replace(".","",$nohp);

        // cek apakah no hp mengandung karakter + dan 0-9
        if(!preg_match('/[^+0-9]/',trim($nohp))){
            // cek apakah no hp karakter 1-3 adalah +62
            if(substr(trim($nohp), 0, 3)=='+62'){
                $hp = trim($nohp);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif(substr(trim($nohp), 0, 1)=='0'){
                $hp = '62'.substr(trim($nohp), 1);
            }
        }
       return $hp;
    }
}

if (! function_exists('fbulan')) {
    function fbulan($bulan)
    {
        $bulan = (int) $bulan;
        if ($bulan == "7") {
            $bln = "Juli";
        } else if ($bulan == "8") {
            $bln = "Agustus";
        } else if ($bulan == "9") {
            $bln = "September";
        } else if ($bulan == "10") {
            $bln = "Oktober";
        } else if ($bulan == "11") {
            $bln = "November";
        } else if ($bulan == "12") {
            $bln = "Desember";
        } else if ($bulan == "1") {
            $bln = "Januari";
        } else if ($bulan == "2") {
            $bln = "Februari";
        } else if ($bulan == "3") {
            $bln = "Maret";
        } else if ($bulan == "4") {
            $bln = "April";
        } else if ($bulan == "5") {
            $bln = "Mei";
        } else if ($bulan == "6") {
            $bln = "Juni";
        } else {
            $bln = "";
        }
        return $bln;
    }
}

if (! function_exists('create_link')) {
    function create_link($string, $separator = '-')
    {
        $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $special_cases = array( '&' => 'and', "'" => '');
        $string = mb_strtolower( trim( $string ), 'UTF-8' );
        $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
        $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
        $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
        $string = preg_replace("/[$separator]+/u", "$separator", $string);
        return $string;
    }
}

if (! function_exists('show_image')) {
    function show_image($source)
    {
        if($source == NULL)
        {
            $source = asset('storage/nofound.png');
        }
        return $source;
    }
}
?>
