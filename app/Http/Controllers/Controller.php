<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($data = null,$message="success", $toString = true, $cacheTime = 0){
        $rData = [
            'status' => '200', 'data' => $data, 'msg' => $message
        ];

        return $this->jsonReturn($rData, $cacheTime, $toString);
    }

    public function error($httpStatusCode, $message = "error", $data = null,$toString = false, $exception = null)
    {
        $rData = [
            'status' => (string) $httpStatusCode, 'data' => $data, 'msg' => $message
        ];
        if (app('config')->get('APP_DEBUG') && $message instanceof \Throwable) {
            $exceptionError = [
                'msg' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ];
            $rData["exceptionError"] = $exceptionError;
        }
        return $this->jsonReturn($rData, 0, $toString);
    }

    public function jsonReturn($data, $cacheTime = 0, $toString = true){
        if($toString){
            array_walk_recursive($data, function (&$value) {
                $value = (string)$value;
            });
        }

        $headers['x-time'] = date('Y-m-d H:i:s');

        // CDN缓存
        if ($cacheTime>0) {
            $headers["Pragma"] = 'public';
            $headers["Cache-Control"] = sprintf("max-age=%d, s-maxage=%d", $cacheTime, $cacheTime);
            $headers["Expires"] = gmdate("r", time()+$cacheTime)." GMT";
        } else {
            $headers["Pragma"] = 'no-cache';
            $headers["Cache-Control"] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
            $headers["Expires"] = gmdate("r", 0)." GMT";
        }

        return response()->json($data)->withHeaders($headers);
    }

    public function validator($data,$rules,$messages){
        $validator = Validator::make($data,$rules,$messages);

        if($validator->fails()){
            $errors = $validator->errors();
            return $this->error(400, $errors->first());
        }
    }
}
