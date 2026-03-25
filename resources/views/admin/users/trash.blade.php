@extends('admin.layouts.app')

@section('title', 'User đã xóa')
@section('subtitle', 'Danh sách người dùng đã bị xóa mềm')

@section('content')

<div class="flex flex-wrap justify-between items-end gap-4">
    <div class="flex flex-col gap-2">
        <h1 class="text-[#181611] text-3xl font-black">User đã xóa mềm</h1>
        <p class="text-[#8a8060] text-base">Quản lý người dùng đã bị xóa</p>
    </div>

    <a href="{{ route('admin.users.index') }}"
        class="flex items-center gap-2 rounded-lg h-11 px-6 bg-white border border-[#e6e3db] text-sm font-bold hover:bg-gray-50">

        <span class="material-symbols-outlined">arrow_back</span>
        Quay lại
    </a>
</div>


<div class="flex flex-col gap-6 bg-white rounded-xl shadow-sm border border-[#e6e3db] p-6">

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">

            <thead>
                <tr class="border-b border-[#e6e3db]">
                    <th class="py-4 px-4 text-xs font-bold text-[#8a8060]">ID</th>
                    <th class="py-4 px-4 text-xs font-bold text-[#8a8060]">Người dùng</th>
                    <th class="py-4 px-4 text-xs font-bold text-[#8a8060]">Email</th>
                    <th class="py-4 px-4 text-xs font-bold text-[#8a8060]">Vai trò</th>
                    <th class="py-4 px-4 text-xs font-bold text-[#8a8060]">Ngày xóa</th>
                    <th class="py-4 px-4 text-xs font-bold text-right text-[#8a8060]">Thao tác</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[#f5f3f0]">

                @forelse($users as $user)

                <tr class="hover:bg-gray-50">

                    <td class="py-4 px-4 text-sm font-mono">
                        #US-{{ $user->id }}
                    </td>

                    <td class="py-4 px-4 font-bold">
                        {{ $user->name }}
                    </td>

                    <td class="py-4 px-4 text-[#8a8060]">
                        {{ $user->email }}
                    </td>

                    <td class="py-4 px-4">
                        {{ ucfirst($user->role) }}
                    </td>

                    <td class="py-4 px-4 text-sm text-[#8a8060]">
                        {{ $user->deleted_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="py-4 px-4">
                        <div class="flex justify-end gap-2">

                            {{-- Restore --}}
                            <form method="POST" action="{{ route('admin.users.restore', $user->id) }}">
                                @csrf
                                <button
                                    class="p-2 text-green-600 hover:text-green-800"
                                    title="Khôi phục">

                                    <span class="material-symbols-outlined">
                                        restore
                                    </span>

                                </button>
                            </form>


                            {{-- Force delete --}}
                            <form method="POST"
                                action="{{ route('admin.users.forceDelete', $user->id) }}"
                                onsubmit="return confirm('Xóa vĩnh viễn user này?')">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="p-2 text-red-600 hover:text-red-800"
                                    title="Xóa vĩnh viễn">

                                    <span class="material-symbols-outlined">
                                        delete_forever
                                    </span>

                                </button>

                            </form>

                        </div>
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="py-10 text-center text-[#8a8060]">
                        Không có user nào trong thùng rác
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>
    </div>

</div>

@endsection