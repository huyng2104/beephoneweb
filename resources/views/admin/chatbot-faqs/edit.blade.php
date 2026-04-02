@extends('admin.layouts.app')

@section('title', 'Sửa Chatbot FAQ')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-0">Sửa Chatbot FAQ</h1>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.chatbot-faqs.update', $faq->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Câu Hỏi <span class="text-danger">*</span></label>
                    <input type="text" name="question" class="form-control @error('question') is-invalid @enderror" placeholder="Nhập câu hỏi FAQ" value="{{ old('question', $faq->question) }}" required>
                    @error('question')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Trả Lời <span class="text-danger">*</span></label>
                    <textarea name="answer" class="form-control @error('answer') is-invalid @enderror" rows="6" placeholder="Nhập câu trả lời" required>{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Từ Khóa (ngăn cách bằng dấu phẩy)</label>
                    <input type="text" name="keywords" class="form-control" placeholder="Ví dụ: giá, giá cả, bao nhiêu tiền" value="{{ old('keywords', $faq->keywords) }}">
                    <small class="text-muted">Nhập các từ khóa liên quan để chatbot tự động nhận dạng câu hỏi</small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Độ Ưu Tiên</label>
                        <input type="number" name="priority" class="form-control" placeholder="0" value="{{ old('priority', $faq->priority) }}">
                        <small class="text-muted">Số càng cao, FAQ sẽ được ưu tiên trả lời trước</small>
                    </div>

                    <div class="form-group col-md-6">
                        <label>&nbsp;</label>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ $faq->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Kích Hoạt</label>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Cập Nhật FAQ</button>
                    <a href="{{ route('admin.chatbot-faqs.index') }}" class="btn btn-secondary">Quay Lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
