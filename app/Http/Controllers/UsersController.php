<?php

namespace App\Http\Controllers;
//接收浏览器办理入
use Illuminate\Http\Request;
//引入用户模型
use App\Models\User;
//引入用户认证管理
use Auth;
use Mail;

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
        
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱，请注意查收。');
        return redirect()->route('home');
/**
		//注册成功自动登录
		
		Auth::login($user);
		
		//注册成功提示
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        //注册成功跳转
        return redirect()->route('users.show', [$user]);
**/
    }

	protected function sendEmailConfirmationTo($user)
	{
		$view = 'emails.confirm';
		$date = compact('user');
		$from = 'jackyancc@foxmail.com';
		$name = '风花雪月';
		$to = $user->email;
		$subject = '感谢注册 Weibo 应用！请确认你的邮箱。';
		
		Mail::send($view, $date, function($message) use ($from, $name, $to, $subject){
			$message->from($from, $name)->to($to)->subject($subject);
		});
	}

	//进入个人信息更新页
	public function edit(User $user)
	{
		//authorize授权策略Policy，参数1为策略名，参数2为验证数据
		$this->authorize('update', $user);//在app/Policies/UserPolicy.php定义了update策略

		return view('users.edit', compact('user'));
	}

	//更新个人信息
	public function update(User $user, Request $request)
	{
		//authorize授权策略Policy，参数1为策略名，参数2为验证数据
		$this->authorize('update', $user);//在app/Policies/UserPolicy.php定义了update策略		

		//validate验证输入格式
		$this->validate($request, [
			'name' => 'required|max:50',
			'password' => 'nullable|confirmed|min:6'
		]);
		
		$data = [];
		$data['name'] = $request->name;
		if ($request->password) {
			$data['password'] = bcrypt($request->password);
		}
		$user->update($data);
		
		session()->flash('success', '个人资料更新成功！');
		
		return redirect()->route('users.show', $user->id);
	}
	
	//列出所有用户列表
	public function index()
	{
		//paginate 分组输出
		$users = User::paginate(6);
		return view('users.index', compact('users'));
	}

	//删除用户
	public function destroy(User $user)
	{
		$this->authorize('destroy', $user); //authorize调用app/Policies/UserPolicy.php定义的destroy策略
		$user->delete();
		session()->flash('success', '成功删除用户！');
		return back();
	}

	//未登录权限限制
	public function __construct()
	{
		$this->middleware('auth', [
			'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
		]);
		
		$this->middleware('guest', [
			'only' => ['create']
		]);
	}
	
	public function confirmEmail($token)
	{
		$user = User::where('activation_token', $token)->firstOrFail();
		$user->activated = true;
		$user->activation_token = null;
		$user->save();
		
		Auth::login($user);
		session()->flash('success', '恭嘉你，激活成功！');
		return redirect()->route('users.show', [$user]);
	}
	

}
