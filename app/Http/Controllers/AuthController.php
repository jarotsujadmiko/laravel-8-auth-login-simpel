<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Session;
use App\Models\User;

class AuthController extends Controller
{
    public function showFormController(){
        // cek session field di users, nanti bisa dipanggil via Auth
        if (Auth::check()){
            //login sukkses
            return redirect()->route('home');
        }
        //jika tidak ada session
        return view('login');
    }

    public function login(Request $request){
        $rules=[
            'email'     =>'required|email',
            'password'  =>'required|string'
        ];

        $message=[
            'email.required'    =>'email wajib di isi',
            'email.email'       =>'email tidak valid',
            'password.required' =>'password wajib di isi',
            'password.string'   =>'passsword harus berupa kata'
        ];

        $validator = Validator::make($request->all(),$rules,$messages);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }

        $data=[
            'email'     =>$request->input('email'),
            'password'  =>$request->input('password')
        ];

        Auth::attemt($data);

        if(Auth::check()){
            return redirect()->route('home');
        }
        else{
            Session::flash('error','Email atau password salah');
            return redirect()->route('login');
        }
    }

    public function showFormRegister(){
        return view('register');
    }

    public function register(Request $request){
        $rules=[
            'name'      =>'required|min:3|max:30',
            'email'     =>'required|email|unique:users',
            'passsword' =>'required|confirmed'     
        ];

        $messages=[
            'name.required'     =>'nama wajib di isi',
            'name.min'          =>'nama minimal 3 kata',
            'name.max'          =>'nama terlalu panjang',
            'email.required'    =>'email wajib di isi',
            'email.email'       =>'email tidak valid',
            'email.unique'      =>'email sudah terdaftar',
            'password.required' =>'pasword wajib di isi',
            'password.confirmed'=>'password tidak sama dengan konfirmasi'
        ];

        $validator=Validator::make($request->all(),$rules,$messages);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }

        $user   = new User;
        $user->name     =ucwords(strtolower($request->name));
        $user->email    =strtolower($request->email);
        $user->password =Hash::make($request->password);
        $user->email_verified_at = \Carbon\Carbon::now();
        $simpan         =$user->save();

        if($simpan){
            Session::flash('success','Register berhasil silahkan login');
            return redirect()->route('login');
        }
        else{
            Session::falsh('errors',[''=>'Register gagal, silahkan register ulang ']);
            return redirect()->route('register');
        }
    }
    public function logout(){
        //menghapus session yang aktif
        Auth::logout();
        return redirect()->route('login');
    }
}
