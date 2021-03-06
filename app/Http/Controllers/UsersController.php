<?php

namespace App\Http\Controllers;
//接收浏览器办理入
use Illuminate\Http\Request;
//引入用户模型
use App\Models\User;
//引入用户认证管理
use Auth;

class UsersController extends Controller
{
    //
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function store(Request $request)
    {
        //验证表单提效数据有效性
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        
        //创建用户
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)

        ]);

	//注册成功自动登录
	Auth::login($user);
		
	
        //注册成功提示
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        //注册成功跳转
        return redirect()->route('users.show', [$user]);

    }



}
