@php
    $isAdminRoute = request()->routeIs('admin.*');
@endphp

@if(!$comment->is_hidden || $isAdminRoute)
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
                $isAdmin = false;
                if ($comment->user) {
                    $roleNameRaw = $comment->user->role?->name ?? $comment->user->role_slug ?? null;
                    $roleName = is_string($roleNameRaw) ? strtolower($roleNameRaw) : null;
                    $isAdmin = in_array($roleName, ['admin', 'staff'], true);
                }
            @endphp
            @if($isAdmin)
                <span class="badge admin">Admin</span>
            @endif
            @if($comment->verified_purchase)
                <span class="badge verified">Đã mua</span>
            @endif
            @if($comment->is_hidden)
                <span class="badge" style="background:#334155;color:#e2e8f0;border:1px solid rgba(255,255,255,.12)">Ẩn</span>
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
            @php
                $roleNameRaw = auth()->check()
                    ? (auth()->user()->role?->name ?? auth()->user()->role_slug ?? null)
                    : null;
                $roleName = is_string($roleNameRaw) ? strtolower(trim($roleNameRaw)) : null;
                $canDelete = auth()->check()
                    && (
                        app()->environment('local')
                        || in_array($roleName, ['admin', 'staff'], true)
                        || ($comment->user_id && (int) $comment->user_id === (int) auth()->id())
                    );
            @endphp
            @if($isAdminRoute && auth()->check() && $roleName === 'admin')
                <form action="{{ route('admin.comments.toggle_hidden', $comment) }}" method="POST" style="display:inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="delete-btn" style="background:#0f172a;border-color:rgba(255,255,255,.12)">
                        {{ $comment->is_hidden ? 'Unhide' : 'Hide' }}
                    </button>
                </form>
            @endif
            @if($canDelete)
                <form
                    action="{{ $isAdminRoute ? route('admin.comments.destroy', $comment) : route('comments.destroy', $comment) }}"
                    method="POST"
                    style="display:inline"
                    data-confirm-delete-comment
                >
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

@include('partials.confirm-delete-comment')

@endif
