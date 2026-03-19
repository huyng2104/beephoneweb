<div id="comment-{{ $comment->id }}" class="comment-item">
    <div class="avatar">
        @php
            $email = $comment->user->email ?? $comment->guest_email ?? '';
            $hash = md5(strtolower(trim($email)));
            $avatar = "https://www.gravatar.com/avatar/{$hash}?s=64&d=identicon";
            $displayName = $comment->user->name ?? $comment->guest_name ?? 'Guest';
        @endphp
        <img src="{{ $avatar }}" alt="avatar">
    </div>
    <div class="comment-body">
        <div class="meta">
            <strong>{{ $displayName }}</strong>
            @php
                $verified = $comment->user
                    ? \App\Models\Purchase::where('user_id', $comment->user->id)->where('product_id', $product->id)->exists()
                    : false;
            @endphp
            @if($verified)
                <span class="badge verified">Verified Purchase</span>
            @endif
            <span class="rating">@if($comment->rating) {{ $comment->rating }}★ @endif</span>
            <span class="time">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        <div class="content">{{ $comment->content }}</div>
        @if($comment->image_path)
            <div class="comment-image">
                <img src="{{ asset('storage/' . $comment->image_path) }}" alt="comment image">
            </div>
        @endif

        <div class="actions">
            <a href="#reply-{{ $comment->id }}" class="reply-toggle">Reply</a>
            @if(app()->environment('local') || (auth()->check() && auth()->user()->role === 'admin'))
                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            @endif
        </div>

        <div class="replies">
            @foreach($comment->children as $child)
                @include('components.comment', ['comment' => $child, 'product' => $product])
            @endforeach
        </div>

        <div class="reply-form" id="reply-{{ $comment->id }}">
            <form action="{{ route('products.comments.store', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                @guest
                <input type="text" name="guest_name" required placeholder="Your name">
                <input type="email" name="guest_email" placeholder="Your email (optional)">
                @endguest
                <textarea name="content" rows="2" required placeholder="Write a reply..."></textarea>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="comment-file-input">
                <button type="submit">Reply</button>
            </form>
        </div>
    </div>
</div>
