<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{


    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Authorization: Only allow editing own profile or if admin
        if ($user->id !== Auth::user()->id && !(Auth::user()->role = 'admin')) {
            return redirect()->route('dashboard')->with('error', 'You can only edit your own profile.');
        }
        
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Authorization same as above
        if ($user->id !== Auth::user()->id && !(Auth::user()->role = 'admin')) {
            return redirect()->route('dashboard')->with('error', 'You can only edit your own profile.');
        }
        
        // Validate input (add more rules as needed)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:15',
            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        
        // Update only allowed fields (no password or role)
        $user->update($validated);
        
        return redirect()->route('dashboard')->with('message', 'Cập nhật thành công');
    }
}