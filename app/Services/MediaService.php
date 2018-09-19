<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/9/18
 * Time: 下午9:26
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MediaService
{
    private $tbName = 'medias';

    public function createMedia($mediaInfo)
    {
        $time = new Carbon();
        $mediaInfo = array_merge($mediaInfo, [
            'created_at' => $time
        ]);
        $mediaId = DB::table($this->tbName)->insertGetId($mediaInfo);
        return $mediaId;
    }

    public function updateMedia($mediaId, $mediaInfo)
    {
        DB::table($this->tbName)->where('id', $mediaId)->update($mediaInfo);
    }

    public function getMediaById($mediaId)
    {
        $mediaInfo = DB::table($this->tbName)->where('id', $mediaId)->first();
        return $mediaInfo;
    }

    public function getMediaByName($mediaName)
    {
        $mediaInfo = DB::table($this->tbName)->where('name', $mediaName)->first();
        return $mediaInfo;
    }

    public function isMediaExist($mediaName)
    {
        $media = $this->getMediaByName($mediaName);
        return $media != null;
    }

}