<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//身份认证类
use Auth;

//会话控制器
class SessionsController extends Controller
{
    //
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        
        //Auth认证信息比较 系统类 
        if (Auth::attempt($credentials)){
            //认证成功
            session()->flash('success', '欢迎回来！');
            return redirect()->route('users.show', [Auth::user()]);/*Auth::user()获取用户信息*/
        } else {
            //认证失败
            session()->flash('danger', '很抱歉，您的邮箱和密码不区配');
            return redirect()->back()->withInput();/*withInput()返回输入数据到模板old('email')*/
        }


    }

}
