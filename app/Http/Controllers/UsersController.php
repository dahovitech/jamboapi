<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    
    /**
     * Update user's name
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->save();
        return response()->json(['success' => true]);
    }

    /**
     * Update user's email
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateEmail(Request $request)
    {
        $user_id = auth()->user()->id;
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,'.$user_id,
        ]);

        $user = auth()->user();
        $user->email = $request->email;
        $user->save();
        return response()->json(['success' => true]);
    }

    /**
     * Update user's password
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {   
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(['success' => true]);
    }
}
