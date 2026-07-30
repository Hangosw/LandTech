<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        // For demonstration, fetch all users. You can add pagination/filtering later.
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Update the user status.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending,banned',
        ]);

        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái của người dùng ' . $user->name . ' thành ' . $user->status . '.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::with('properties')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'nationality' => 'nullable|string|max:255',
            'user_type' => 'required|in:seller,buyer,owner,renter,agent,admin',
            'status' => 'required|in:active,inactive,pending,banned',
        ], [
            'phone.unique' => 'Số điện thoại này đã được sử dụng bởi tài khoản khác.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
        ]);

        try {
            $user->update($request->all());
            return redirect()->route('admin.users.edit', $user->id)->with('success', 'Đã cập nhật thông tin người dùng thành công.');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'email')) {
                return redirect()->back()->withInput()->with('error', 'Email này đã được sử dụng bởi tài khoản khác.');
            }
            if (str_contains($msg, 'phone')) {
                return redirect()->back()->withInput()->with('error', 'Số điện thoại này đã được sử dụng bởi tài khoản khác.');
            }
            return redirect()->back()->withInput()->with('error', 'Thông tin bị trùng lặp. Vui lòng kiểm tra lại.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại sau.');
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'inactive';
        $user->save();
        
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa (ẩn) người dùng thành công.');
    }
}
