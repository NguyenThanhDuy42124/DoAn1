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
        if ($user->id !== Auth::user()->id) {
            return redirect()->route('dashboard')->with('error', 'You can only edit your own profile.');
        }

        return view('general.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Authorization same as above
        if ($user->id !== Auth::user()->id) {
            return redirect()->route('dashboard')->with('error', 'You can only edit your own profile.');
        }

        // Validate input (add more rules as needed)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:15',
            'address' => 'required|string|max:255',
            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'img' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg', // Optional profile image
        ]);
        if ($request->hasFile('img')) {

            $imagePath = $request->file('img')->store('profile_images', 'public');
            $validated['img'] = $imagePath;
        }

        // Update only allowed fields (no password or role)
        $user->update($validated);

        return redirect()->route('dashboard')->with('message', 'Cập nhật thành công');
    }
}
