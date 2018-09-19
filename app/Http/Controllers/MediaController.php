<?php

namespace App\Http\Controllers;

use App\Services\MediaService;
use App\Tools\Code;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;
use Qiniu\Auth;
use function Qiniu\base64_urlSafeEncode;

class MediaController extends Controller
{

    private $mediaService;

    private $accessKey = 'klZ0v30G98DYVipuVm84dibueYGQnuop8RPrbDk2';
    private $secretKey = 'bcQFExbIZhU-TzcZB2iqQ5K2Zn8dAh1G2pVA-ReG';
    private $bucket = 'test';
    private $domain = 'ov8i0dn6x.bkt.clouddn.com/';

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function makeUploadToken(Request $request)
    {
        $userInfo = $request->user;
        if(!$userInfo->is_teacher)
            return response()->json(Code::NO_PERMISSION);
        $rule = [
            'file_name' => 'required',
            'lesson_id' => 'required'
        ];
        $res = ValidationHelper::validateCheck($request->all(), $rule);
        if($res->fails())
            return response()->json([
                'code' => 201,
                'message' => $res->errors()
            ]);
        $mediaInfo = ValidationHelper::getInputData($request, $rule);
        $fileName = $mediaInfo['file_name'];
        $saveKey = $fileName. '_'. time() . str_random(10);
        if($this->mediaService->isMediaExist($fileName))
            return response()->json([
                'code' => '202',
                'message' => '文件名已存在'
            ]);
        $mediaInfo = array_merge($mediaInfo, [
            'file_url' => 'http://'.$this->domain . $fileName,
            'file_name' => $fileName,
        ]);
        $auth = new Auth($this->accessKey, $this->secretKey);
        $expires = 3600;
        $fileLocation = base64_urlSafeEncode($this->bucket . ":" . $fileName);

        $url = base64_urlSafeEncode($this->domain);
        $videoDeal = "avthumb/m3u8/noDomain/0/domain/" . $url . "/vb/500k|saveas/" . $fileLocation;
        $policy = [
            'saveKey' => $saveKey,
            'callbackUrl' => 'mooc.sealbaby.cn/upload/callback',
            'callbackBody' => '{"persistentId":"$(persistentId)","mp4Info":' . json_encode($mediaInfo) . '}',
            'callbackBodyType' => 'application/json',
            'persistentOps' => $videoDeal,
            'persistentPipeline' => "video-pipe",
            'persistentNotifyUrl' => 'mooc.sealbaby.cn/notify'
        ];
        $uploadToken = $auth->uploadToken($this->bucket, null, $expires, $policy,true);
        return response()->json([
            'code' => 0,
            'upload_token' => $uploadToken
        ]);
    }

    public function callback(Request $request)
    {
        $status = $request->persistentId;
        $mediaInfo = $request->mp4Info;
        $mediaInfo['is_available'] = $status;

        $mediaId = $this->mediaService->createMedia($mediaInfo);
        return response()->json([
            'code' => 0,
            'media_id' => $mediaId
        ]);
    }

    public function notify(Request $request)
    {
        $this->mediaService->updateMedia($request->id,[
            'status' =>  $request->code
        ]);
    }
}