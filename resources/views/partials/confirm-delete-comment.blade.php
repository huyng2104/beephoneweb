@once
    <div
        id="delete-comment-confirm"
        class="fixed left-1/2 top-4 z-[9999] w-[min(92vw,360px)] -translate-x-1/2 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl opacity-0 pointer-events-none -translate-y-2 scale-95 transition duration-200 ease-out"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-comment-confirm-title"
        aria-hidden="true"
    >
        <div class="flex items-start gap-3 p-4">
            <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/15 text-primary">
                <span class="material-symbols-outlined text-[20px]">delete</span>
            </div>
            <div class="min-w-0 flex-1">
                <div id="delete-comment-confirm-title" class="text-sm font-bold text-slate-900">
                    Bạn có muốn xóa comment?
                </div>
                <div class="mt-1 text-xs text-slate-500">
                    Thao tác này sẽ xóa cả các phản hồi liên quan (nếu có).
                </div>
                <div class="mt-3 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        data-delete-comment-cancel
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50"
                    >
                        Hủy
                    </button>
                    <button
                        type="button"
                        data-delete-comment-confirm
                        class="inline-flex items-center justify-center rounded-lg bg-primary px-3 py-2 text-xs font-bold text-slate-900 hover:brightness-105"
                    >
                        Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var currentForm = null;
            var box = null;
            var btnCancel = null;
            var btnConfirm = null;

            function ensure() {
                if (box) return true;
                box = document.getElementById('delete-comment-confirm');
                if (!box) return false;
                btnCancel = box.querySelector('[data-delete-comment-cancel]');
                btnConfirm = box.querySelector('[data-delete-comment-confirm]');
                return true;
            }

            function openFor(form) {
                if (!ensure()) return;
                currentForm = form;
                box.setAttribute('aria-hidden', 'false');
                box.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-2', 'scale-95');
                box.classList.add('opacity-100', 'translate-y-0', 'scale-100');
                btnCancel && btnCancel.focus && btnCancel.focus();
            }

            function close() {
                if (!ensure()) return;
                box.setAttribute('aria-hidden', 'true');
                box.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                box.classList.add('opacity-0', 'pointer-events-none', '-translate-y-2', 'scale-95');
                currentForm = null;
            }

            document.addEventListener(
                'submit',
                function (e) {
                    var form = e.target;
                    if (!(form instanceof HTMLFormElement)) return;
                    if (!form.matches('form[data-confirm-delete-comment]')) return;
                    e.preventDefault();
                    openFor(form);
                },
                true
            );

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                if (!ensure()) return;
                if (box.getAttribute('aria-hidden') === 'true') return;
                close();
            });

            document.addEventListener('click', function (e) {
                if (!ensure()) return;
                var target = e.target;
                if (!(target instanceof Element)) return;

                if (target.closest('[data-delete-comment-cancel]')) {
                    e.preventDefault();
                    close();
                    return;
                }

                if (target.closest('[data-delete-comment-confirm]')) {
                    e.preventDefault();
                    var form = currentForm;
                    close();
                    if (form) form.submit();
                }
            });
        })();
    </script>
@endonce
