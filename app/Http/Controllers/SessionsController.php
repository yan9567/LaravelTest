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
        
        //Auth::attemp()认证信息比较,系统类,第一个参数是验证数据，第二个参数指令是否生成记忆令牌cookie（没有记住我，默认登录状态保存2小时） 
        //认证成功
        if (Auth::attempt($credentials, $request->has('remember'))) {
            //已注册
            if (Auth::user()->activated) {
            	session()->flash('success', '欢迎回来！');
		    	$fallback = route('users.show', Auth::user());	/*Auth::user()获取用户信息*/
		    	return redirect()->intended($fallback);	/*intended返回上次尝试访问页，为空进入默认地址fallback*/            
            } else {
            	Auth::logout();
            	session()->flash('warning', '你的账号未激活，请检查注册邮箱中的邮件完成激活。');
            	return redirect()->route('home');
            }

        } else {
            //认证失败
            session()->flash('danger', '很抱歉，您的邮箱和密码不区配');
            return redirect()->back()->withInput();/*withInput()返回输入数据到模板old('email')*/
        }
    }
    
    public function destroy()
    {
    	//Auth::logout()退出登录
    	Auth::logout();
    	session()->flash('success', '您已成功退出！');
    	return redirect('login');
    }
    
    //访问限制
    public function __construct()
    {
    	$this->middleware('guest', [
    		'only' => ['create']
    	]);
    }

}
