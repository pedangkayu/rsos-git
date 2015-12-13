<?php

namespace App\Http\Middleware;

use Closure;

class AksesPageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){

        \Me::setOnline();

        if(\Auth::check()){
            $access = \Menu::access()['return'];
            if($access == false){
                return redirect('/');
            }

            if(!empty(\Session::get('lock')) && \Request::path() != 'lockscreen'){
                return redirect('/lockscreen');
            }
        }

        if(\Auth::guest()){
            if(!empty(\Session::get('lock')))
                \Session::forget('lock');
        }

        return $next($request);
    }
}
