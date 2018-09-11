<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/9/11
 * Time: 下午5:36
 */
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserService
{
    public function register($userInfo)
    {
        $uniques = [
            'login_num',
        ];

        foreach ($uniques as $unique) {
            if (DB::table('users')->where($unique, $userInfo[$unique])->count() >= 1) {
                return -1;
            }
        }

        $time = new Carbon();
        $userInfo['name'] = "user" . uniqid();
        $userInfo['password'] = bcrypt($userInfo['password']);
        $userInfo['coin'] = 0;
        if ($userInfo['is_teacher'] == 1){
            $userInfo['is_teacher'] = 1;
            $userInfo['is_stu'] = 0;
        }else{
            $userInfo['is_teacher'] = 0;
            $userInfo['is_stu'] = 1;
        }

        $userInfo = array_merge($userInfo,[
            'created_at' => $time,
            'updated_at' => $time
        ]);
        $userId = DB::table('users')->insertGetId($userInfo);
        return $userId;
    }
    public function login($loginNum,$password)
    {

        $user = DB::table('users')->where('login_num',$loginNum)->first();
        if ($user == null)
            return -1;

        if (! Hash::check($password, $user->password))
            return -2;
        else
            return $user->id;
    }

}