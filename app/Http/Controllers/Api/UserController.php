<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function store(Request $request)
    {
        try{
            $user = $request->user()->toArray();
            return $this->success($user);
        }catch (\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }
    }

    public function register(Request $request)
    {
        try{
            $rule = [
                'user_mobile'    => 'required|unique:users,user_mobile',
                'user_password'    => 'required',
            ];
            $messages = [];
            $validator = Validator::make($request->all(), $rule, $messages);
            if ($validator->fails()) {
                return $this->error(101, $validator->errors()->first());
            }

            $data['user_mobile'] = $request->user_mobile;
            $data['user_password'] = Hash::make($request->user_password);
            $res = User::create($data)->toArray();

        }catch (\Exception $e) {
            return $this->error($e->getCode(), $e->getMessage());
        }

        return $this->success($res);
    }


    public function login(Request $request)
    {
        $token = auth('api')->attempt($request->all());
        if(!$token){
            return $this->error(401, '验证失败');
        }
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
        return $this->success($data);
    }

    public function loginOut()
    {
        auth('api')->logout();
        return $this->success('退出成功');
    }

    public function refresh()
    {
        $refresh_token = auth('api')->refresh();
        return $this->success(['refresh_token' => $refresh_token]);
    }

}
