@once
    <div id="delete-comment-confirm-overlay" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="relative flex min-h-full items-center justify-center p-4">
            <div
                id="delete-comment-confirm"
                class="w-full max-w-md overflow-hidden rounded-2xl border border-white/10 bg-[#141a1e] text-slate-100 shadow-[0_25px_80px_-50px_rgba(0,0,0,0.85)] opacity-0 translate-y-2 scale-95 transition duration-200 ease-out"
                role="dialog"
                aria-modal="true"
                aria-labelledby="delete-comment-confirm-title"
            >
                <div class="flex items-start gap-4 p-5">
                    <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/15 text-primary border border-primary/20">
                        <span class="material-symbols-outlined text-[22px]">delete</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div id="delete-comment-confirm-title" class="text-base font-black text-white">
                            Xóa comment?
                        </div>
                        <div class="mt-1 text-sm text-slate-300">
                            Thao tác này sẽ xóa cả các phản hồi liên quan (nếu có).
                        </div>

                        <div class="mt-3 rounded-xl border border-white/10 bg-black/20 p-3 text-sm text-slate-200 hidden" data-delete-comment-preview></div>

                        <div class="mt-5 flex items-center justify-end gap-2">
                            <button
                                type="button"
                                data-delete-comment-cancel
                                class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-black text-slate-200 hover:border-primary/40 hover:text-primary transition"
                            >
                                Hủy
                            </button>
                            <button
                                type="button"
                                data-delete-comment-confirm
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-black text-slate-900 hover:brightness-105 transition disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <span class="material-symbols-outlined text-[18px] hidden" data-delete-comment-spinner>progress_activity</span>
                                <span data-delete-comment-confirm-label>Xóa</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="delete-comment-toast" class="fixed bottom-5 right-5 z-[10000] hidden">
        <div class="rounded-xl border border-white/10 bg-[#141a1e] px-4 py-3 text-sm font-bold text-slate-100 shadow-[0_16px_45px_-30px_rgba(0,0,0,0.75)]">
            <span data-delete-comment-toast-text>Đã xóa comment.</span>
        </div>
    </div>

    <script>
        (function () {
            var currentForm = null;
            var currentCommentEl = null;
            var overlay = null;
            var box = null;
            var btnCancel = null;
            var btnConfirm = null;
            var btnConfirmLabel = null;
            var btnSpinner = null;
            var preview = null;
            var toast = null;
            var toastText = null;
            var busy = false;

            function ensure() {
                if (box) return true;
                overlay = document.getElementById('delete-comment-confirm-overlay');
                box = document.getElementById('delete-comment-confirm');
                if (!overlay || !box) return false;
                btnCancel = box.querySelector('[data-delete-comment-cancel]');
                btnConfirm = box.querySelector('[data-delete-comment-confirm]');
                btnConfirmLabel = box.querySelector('[data-delete-comment-confirm-label]');
                btnSpinner = box.querySelector('[data-delete-comment-spinner]');
                preview = box.querySelector('[data-delete-comment-preview]');
                toast = document.getElementById('delete-comment-toast');
                toastText = toast ? toast.querySelector('[data-delete-comment-toast-text]') : null;
                return true;
            }

            function setBusy(nextBusy) {
                busy = !!nextBusy;
                if (!btnConfirm) return;
                btnConfirm.disabled = busy;
                if (btnSpinner) btnSpinner.classList.toggle('hidden', !busy);
                if (btnConfirmLabel) btnConfirmLabel.textContent = busy ? 'Đang xóa...' : 'Xóa';
            }

            function showToast(message) {
                if (!toast) return;
                if (toastText && message) toastText.textContent = message;
                toast.classList.remove('hidden');
                setTimeout(function () {
                    toast.classList.add('hidden');
                }, 2200);
            }

            function animateRemove(el) {
                if (!el) return;
                el.style.transition = 'opacity 180ms ease, transform 180ms ease, height 220ms ease, margin 220ms ease, padding 220ms ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-6px)';
                var h = el.getBoundingClientRect().height;
                el.style.height = h + 'px';
                requestAnimationFrame(function () {
                    el.style.height = '0px';
                    el.style.marginTop = '0px';
                    el.style.marginBottom = '0px';
                    el.style.paddingTop = '0px';
                    el.style.paddingBottom = '0px';
                });
                setTimeout(function () {
                    if (el && el.parentNode) el.parentNode.removeChild(el);
                }, 260);
            }

            function openFor(form) {
                if (!ensure()) return;
                if (busy) return;
                currentForm = form;
                currentCommentEl = form.closest('.comment-item');

                overlay.classList.remove('hidden');
                overlay.setAttribute('aria-hidden', 'false');
                requestAnimationFrame(function () {
                    box.classList.remove('opacity-0', 'translate-y-2', 'scale-95');
                    box.classList.add('opacity-100', 'translate-y-0', 'scale-100');
                });

                if (preview) {
                    var content = currentCommentEl ? currentCommentEl.querySelector('.content') : null;
                    var txt = content ? (content.textContent || '').trim() : '';
                    if (txt) {
                        preview.textContent = txt.length > 120 ? txt.slice(0, 120) + '…' : txt;
                        preview.classList.remove('hidden');
                    } else {
                        preview.classList.add('hidden');
                    }
                }

                btnCancel && btnCancel.focus && btnCancel.focus();
            }

            function close() {
                if (!ensure()) return;
                if (busy) return;
                overlay.setAttribute('aria-hidden', 'true');
                box.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                box.classList.add('opacity-0', 'translate-y-2', 'scale-95');
                setTimeout(function () {
                    overlay.classList.add('hidden');
                }, 180);
                currentForm = null;
                currentCommentEl = null;
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
                if (overlay.classList.contains('hidden')) return;
                close();
            });

            document.addEventListener('click', function (e) {
                if (!ensure()) return;
                var target = e.target;
                if (!(target instanceof Element)) return;

                // click backdrop
                if (!overlay.classList.contains('hidden') && target === overlay) {
                    e.preventDefault();
                    close();
                    return;
                }
                if (!overlay.classList.contains('hidden') && target.closest('#delete-comment-confirm-overlay') && !target.closest('#delete-comment-confirm')) {
                    e.preventDefault();
                    close();
                    return;
                }

                if (target.closest('[data-delete-comment-cancel]')) {
                    e.preventDefault();
                    close();
                    return;
                }

                if (target.closest('[data-delete-comment-confirm]')) {
                    e.preventDefault();
                    var form = currentForm;
                    var commentEl = currentCommentEl;
                    if (!form) return;

                    setBusy(true);
                    try {
                        var tokenInput = form.querySelector('input[name=\"_token\"]');
                        var token = tokenInput ? tokenInput.value : '';
                        var body = new URLSearchParams(new FormData(form));

                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': token
                            },
                            body: body
                        })
                        .then(function (res) {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.json().catch(function () { return { ok: true }; });
                        })
                        .then(function () {
                            setBusy(false);
                            overlay.setAttribute('aria-hidden', 'true');
                            box.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                            box.classList.add('opacity-0', 'translate-y-2', 'scale-95');
                            setTimeout(function () {
                                overlay.classList.add('hidden');
                            }, 180);

                            animateRemove(commentEl);
                            showToast('Đã xóa comment.');
                            currentForm = null;
                            currentCommentEl = null;
                        })
                        .catch(function () {
                            setBusy(false);
                            overlay.classList.add('hidden');
                            if (form) form.submit(); // fallback
                        });
                    } catch (err) {
                        setBusy(false);
                        overlay.classList.add('hidden');
                        if (form) form.submit();
                    }
                }
            });
        })();
    </script>
@endonce

