@extends('admin.layouts.app')

@section('title', 'Quản Lý Chatbot FAQ')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Quản Lý Chatbot FAQ</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('admin.chatbot-faqs.create') }}" class="btn btn-primary">+ Thêm FAQ</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Câu Hỏi</th>
                        <th>Từ Khóa</th>
                        <th>Độ Ưu Tiên</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td>{{ Str::limit($faq->question, 60) }}</td>
                            <td>{{ Str::limit($faq->keywords ?? 'N/A', 40) }}</td>
                            <td><span class="badge badge-info">{{ $faq->priority }}</span></td>
                            <td>
                                @if($faq->is_active)
                                    <span class="badge badge-success">Kích Hoạt</span>
                                @else
                                    <span class="badge badge-danger">Tắt</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.chatbot-faqs.edit', $faq->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('admin.chatbot-faqs.destroy', $faq->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Chắc chắn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Chưa có FAQ nào</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{ $faqs->links() }}
        </div>
    </div>
</div>
@endsection
