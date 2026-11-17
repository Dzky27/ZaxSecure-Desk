/* ZaxSecure Desk V2 — Login Form Handler, Password Visibility & Demo Credentials Clipboard */
(function () {
    'use strict';

    function setStatus(message, type) {
        const statusEl = document.getElementById('loginStatus');
        if (!statusEl) return;
        statusEl.textContent = message || '';
        statusEl.className = 'auth-v2__status ' + (type === 'error' ? 'error' : type === 'success' ? 'success' : '');
    }

    function copyTextToClipboard(text, button, labelText) {
        if (!button) return;
        const originalHtml = button.innerHTML;

        const onCopied = () => {
            button.innerHTML = '<span>✓ Tersalin</span>';
            button.style.borderColor = 'rgba(16, 185, 129, 0.5)';
            button.style.color = '#6ee7b7';
            setStatus(labelText || 'Teks berhasil disalin ke papan klip.', 'success');

            setTimeout(() => {
                button.innerHTML = originalHtml;
                button.style.borderColor = '';
                button.style.color = '';
                if (window.lucide) {
                    try { window.lucide.createIcons(); } catch (e) {}
                }
            }, 1800);
        };

        const fallbackCopy = () => {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                onCopied();
            } catch (e) {
                setStatus('Gagal menyalin teks secara otomatis.', 'error');
            }
            document.body.removeChild(textarea);
        };

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(onCopied).catch(fallbackCopy);
        } else {
            fallbackCopy();
        }
    }

    function initCopyButtons() {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        const bindBtn = (id, value, msg, fillField) => {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.addEventListener('click', function () {
                copyTextToClipboard(value, btn, msg);
                if (fillField === 'email' && emailInput) {
                    emailInput.value = value;
                    emailInput.classList.remove('invalid');
                } else if (fillField === 'password' && passwordInput) {
                    passwordInput.value = value;
                    passwordInput.classList.remove('invalid');
                }
            });
        };

        bindBtn('copyAdminEmail', 'admin@zaxsecure.id', 'Email Admin disalin & diisi ke formulir.', 'email');
        bindBtn('copyAdminPassword', 'ChangeMe123!', 'Kata sandi Admin disalin & diisi ke formulir.', 'password');
        bindBtn('copyUserEmail', 'user@zaxsecure.id', 'Email User disalin & diisi ke formulir.', 'email');
        bindBtn('copyUserPassword', 'TestUser123!', 'Kata sandi User disalin & diisi ke formulir.', 'password');

        const copyAllBtn = document.getElementById('copyAllDemo');
        if (copyAllBtn) {
            copyAllBtn.addEventListener('click', function () {
                const allText = [
                    'ADMIN',
                    'Email: admin@zaxsecure.id',
                    'Password: ChangeMe123!',
                    '',
                    'USER',
                    'Email: user@zaxsecure.id',
                    'Password: TestUser123!'
                ].join('\n');
                copyTextToClipboard(allText, copyAllBtn, 'Seluruh kredensial demo (Admin & User) berhasil disalin.');
            });
        }
    }

    function initLoginForm() {
        const form = document.getElementById('loginForm');
        if (!form) return;

        const btn = document.getElementById('loginBtn');
        const btnText = document.getElementById('loginBtnText');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        let safetyTimer = null;

        const resetLoadingState = () => {
            if (safetyTimer) {
                clearTimeout(safetyTimer);
                safetyTimer = null;
            }
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('btn-auth--loading');
            }
            if (btnText) {
                btnText.textContent = 'Masuk ke Sistem';
            }
        };

        // Ensure spinner is never stuck on page load or BFCache restore
        resetLoadingState();
        window.addEventListener('pageshow', resetLoadingState);

        form.addEventListener('submit', function (e) {
            const emailVal = emailInput ? emailInput.value.trim() : '';
            const pwVal = passwordInput ? passwordInput.value : '';

            if (!emailVal) {
                e.preventDefault();
                if (emailInput) emailInput.classList.add('invalid');
                setStatus('Alamat email wajib diisi.', 'error');
                resetLoadingState();
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(emailVal)) {
                e.preventDefault();
                if (emailInput) emailInput.classList.add('invalid');
                setStatus('Masukkan format alamat email yang valid.', 'error');
                resetLoadingState();
                return;
            }

            if (!pwVal) {
                e.preventDefault();
                if (passwordInput) passwordInput.classList.add('invalid');
                setStatus('Kata sandi wajib diisi.', 'error');
                resetLoadingState();
                return;
            }

            // Valid client-side input: activate loading indicator and allow native POST submission
            if (btn) {
                btn.disabled = true;
                btn.classList.add('btn-auth--loading');
            }
            if (btnText) {
                btnText.textContent = 'Memverifikasi Kredensial...';
            }
            setStatus('Sedang memverifikasi kredensial secara aman...', 'success');

            // Safety timeout so loading state always terminates if network hangs or navigation is aborted
            safetyTimer = setTimeout(() => {
                resetLoadingState();
                setStatus('Permintaan memakan waktu terlalu lama. Silakan periksa koneksi dan coba lagi.', 'error');
            }, 12000);
        });
    }

    function init() {
        initCopyButtons();
        initLoginForm();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
