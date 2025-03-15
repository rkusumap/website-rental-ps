<?php
namespace App\Helpers;

class ResponseApi
{
    public static function success($data = null, $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($message = 'Error', $data = null,$code = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ],$code);
    }

    public static function validationError($errors)
    {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $errors
        ], 422);
    }
}
?>
