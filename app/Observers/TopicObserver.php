<?php

namespace App\Observers;

use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        //防 xss 攻击过滤
        $topic->body = clean($topic->body, 'user_topic_body');
        //自定义函数截取摘要
        $topic->excerpt = make_excerpt($topic->body);
    }
}
