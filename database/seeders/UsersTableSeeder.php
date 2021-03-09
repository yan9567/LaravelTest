<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //用户模型工厂factory()方法征收成count(N) n个数据
        User::factory()->count(50)->create();
        
        $user = User::find(1);
        $user->name = "风花雪月";
        $user->email= "jackyancc@foxmail.com";
        $user->is_admin= true;
        $user->password = bcrypt('123456');
        $user->save();
    }
}
