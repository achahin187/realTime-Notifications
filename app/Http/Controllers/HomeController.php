<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Comment;
use App\Post;
use Illuminate\Http\Request;
use Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
         $posts = Post::with(['comments' => function($q){
            $q -> select('id','post_id','comment','user_id');
        }])->get();
        return view('home',compact('posts'));
    }

    public function  saveComment(Request $request){
        Comment::create([
               'post_id' => $request -> post_id ,
               'user_id' => Auth::id(),
               'comment' => $request ->comment,
        ]);

          $data =[
               'user_id' => Auth::id(),
               'user_name'  => Auth::user()->name,
               'comment' => $request ->comment,
               'post_id' =>$request -> post_id ,
          ];

           ///   save  notify in database table ////

         event(new NewNotification($data));

        return redirect() -> back() -> with(['success'=> 'تم اضافه تعليقك بنجاح ']);
    }
}

