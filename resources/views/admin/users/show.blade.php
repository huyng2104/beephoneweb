@extends('admin.layouts.app')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="mb-4">
    <a href="/admin/users" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-chevron-left"></i> Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card-ghost">
            <h6 class="mb-3">Thông tin cơ bản</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>ID</strong></td>
                    <td>#{{ $user->id }}</td>
                </tr>
                <tr>
                    <td><strong>Tên</strong></td>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <td><strong>Ngày tạo</strong></td>
                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-ghost">
            <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Chỉnh sửa
            </a>
            <form method="POST" action="/admin/users/{{ $user->id }}" style="display: inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Chắc chắn xóa?')">
                    <i class="bi bi-trash"></i> Xóa
                </button>
            </form>
        </div>
    </div>
</div>
@endsection