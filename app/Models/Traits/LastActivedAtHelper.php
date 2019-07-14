<?php

namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    //缓存相关
    protected $hash_prefix = 'ym_last_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {

        //dd(Carbon::now()->toDateTimeString());

        // Redis 哈希表的命名，如：ym_last_actived_at_2019-10-10
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        //字段名称 如：user_1
        $field = $this->getHashField();

        //dd(Redis::hGetAll($hash));

        //当前时间 如： 2019-10-10 10：10：10
        $now = Carbon::now()->toDateTimeString();

        //数据写入 Redis ,字段已存在会被更新
        Redis::hSet($hash, $field, $now);
    }

    public function syncUserActivedAt()
    {

        // Redis 哈希表的命名
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());

        //从 Redis 中获取所有的哈希表的数据
        $dates = Redis::hGetAll($hash);

        //遍历，同步到数据库中
        foreach ($dates as $user_id => $actived_at) {
            //将 user_1 转换为 1
            $user_id = str_replace($this->field_prefix, '', $user_id);

            //只有当前用户存在时才更新到数据库
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        //以数据为中心的存储 即已同步，即刻删除
        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {

        //Redis 表命名
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        // 字段名称
        //
        $field = $this->getHashField();

        // 三元运算 优先选择 Redis 数据 ，否则使用数据库数据
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在 返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        }else{
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date)
    {
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix . $date;
    }

    public function getHashField()
    {
        // 字段名称，如：user_1
        return $this->field_prefix . $this->id;
    }
}
