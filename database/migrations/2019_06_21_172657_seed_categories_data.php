<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedCategoriesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //生成帖子的基础分类
        $categories = [
            [
                'name'          =>      '分享',
                'description'   =>      '分享创造，分享发现',
            ],
            [
                'name'          =>      '教程',
                'description'   =>      '变美技巧，术前术后准备等',
            ],
            [
                'name'          =>      '问答',
                'description'   =>      '请保持友善，互帮互助',
            ],
            [
                'name'          =>      '公告',
                'description'   =>      '站点公告',
            ],
        ];

        DB::table('categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //清空帖子分类表
        DB::table('categories')->truncate();
    }
}
