<?php
/**
 * Created by PhpStorm.
 * User: andyhui
 * Date: 18-1-31
 * Time: 下午5:50
 */

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TokenService
{
    public static  $EXPIRE_TIME = 2; // 小时

    public function createToken($userId)
    {
        $tokenStr = md5(uniqid());
        $time = new Carbon();
        $outTime = new Carbon();
        $outTime->addHour(self::$EXPIRE_TIME);
        $data = [
            'user_id' => $userId,
            'token' => $tokenStr,
            'created_at' => $time,
            'updated_at' => $time,
            'expires_at' => $outTime
        ];

        DB::table('tokens')->insert($data);
        return $tokenStr;
    }

    private function updateToken($userId)
    {
        $time = new Carbon();
        $outTime = new Carbon();
        $outTime->addHour(self::$EXPIRE_TIME);
        $tokenStr = md5(uniqid());
        $data = [
            'token' => $tokenStr,
            'updated_at' => $time,
            'expires_at' => $outTime
        ];

        DB::table('tokens')->where('user_id', $userId)->update($data);
        return $tokenStr;
    }

    public function makeToken($userId)
    {
        $token  = DB::table('tokens')->where('user_id', $userId)->first();

        if($token == null)
        {
            return $this->createToken($userId);
        }
        else
        {
            return $this->updateToken($userId);
        }
    }

    public function deleteToken($userId)
    {
        DB::table('tokens')->where('user_id', $userId)->delete();
    }

    public function getToken($tokenStr)
    {
        return DB::table('tokens')->where('token',$tokenStr)->first();
    }

    public function verifyToken($tokenStr)
    {
        $res = $this->getToken($tokenStr);
        if($res == null)
            return -1;
        else{
            $time = new Carbon();
            if ($res->expires_at > $time) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function getUserByToken($tokenStr)
    {
        $tokenInfo = $this->getToken($tokenStr);
        $userInfo=DB::table('users')->where('id',$tokenInfo->user_id)->select('id','login_num','name','coin','is_stu','is_teacher')->first();
        if ($userInfo->is_stu == 1 && $userInfo->is_teacher == 0){
            $userInfo->role = "student";
            unset($userInfo->is_stu);
            unset($userInfo->is_teacher);
        }else if ($userInfo->is_stu == 0 && $userInfo->is_teacher == 1){
            $userInfo->role = "teacher";
            unset($userInfo->is_stu);
            unset($userInfo->is_teacher);
        }else{
            $userInfo->role = "error";
            unset($userInfo->is_stu);
            unset($userInfo->is_teacher);
        }
        return $userInfo;
    }
}
