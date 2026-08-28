/**
 * LeadDesk Mini - Public Landing Page JavaScript
 * Dark theme always active — no toggle
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Character Counter ────────────────────────────────────────────────
    const messageInput = document.getElementById('message');
    const charCounter  = document.getElementById('charCounter');
    if (messageInput && charCounter) {
        messageInput.addEventListener('input', function () {
            const len = this.value.length;
            charCounter.textContent = `${len} / 1000`;
            charCounter.style.color = len < 10 ? '#f87171' : 'var(--text-muted)';
        });
    }

    // ── Lead Form Validation & AJAX ──────────────────────────────────────
    const leadForm = document.getElementById('leadCaptureForm');
    if (leadForm) {
        leadForm.addEventListener('submit', function (e) {
            e.preventDefault();
            clearFormErrors(leadForm);

            const name    = document.getElementById('name').value.trim();
            const email   = document.getElementById('email').value.trim();
            const budget  = document.getElementById('budget').value.trim();
            const message = document.getElementById('message').value.trim();
            const csrfToken = document.getElementById('csrf_token')?.value || '';

            let isValid = true;

            if (!name || name.length < 3 || name.length > 100) {
                showFieldError('name', name ? 'Name must be 3–100 characters.' : 'Full Name is required.');
                isValid = false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                showFieldError('email', 'Email Address is required.');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                showFieldError('email', 'Please enter a valid email address.');
                isValid = false;
            }

            if (!budget) {
                showFieldError('budget', 'Please select a budget range.');
                isValid = false;
            }

            if (!message) {
                showFieldError('message', 'Message is required.');
                isValid = false;
            } else if (message.length < 10) {
                showFieldError('message', 'Message must be at least 10 characters.');
                isValid = false;
            } else if (message.length > 1000) {
                showFieldError('message', 'Message cannot exceed 1000 characters.');
                isValid = false;
            }

            if (!isValid) return;

            const submitBtn = document.getElementById('submitLeadBtn');
            const origHtml  = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Submitting...';

            fetch('submit-lead.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(leadForm)
            })
            .then(r => r.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origHtml;

                if (data.success) {
                    showToast('success', data.message || 'Lead submitted! Our team will be in touch shortly.');
                    leadForm.reset();
                    if (charCounter) charCounter.textContent = '0 / 1000';
                } else {
                    if (data.errors && typeof data.errors === 'object') {
                        Object.keys(data.errors).forEach(k => showFieldError(k, data.errors[k]));
                    }
                    showToast('error', data.message || 'Please check the fields and try again.');
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origHtml;
                showToast('error', 'Network error. Please try again.');
            });
        });
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        field.classList.add('is-invalid');
        const parent   = field.closest('.form-group') || field.parentElement;
        let feedback   = parent.querySelector('.invalid-feedback-custom');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback-custom';
            parent.appendChild(feedback);
        }
        feedback.textContent = message;
        feedback.style.display = 'block';
        parent.classList.add('has-error');
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
        form.querySelectorAll('.invalid-feedback-custom').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
    }
});

// ── Toast Notification ────────────────────────────────────────────────────
function showToast(type, message) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container-custom';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-custom toast-${type}`;
    const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
    const iconColor = type === 'success' ? 'var(--brand)' : '#f87171';

    toast.innerHTML = `
        <i class="fa-solid ${icon}" style="font-size:1.1rem;color:${iconColor};flex-shrink:0;"></i>
        <div>
            <div style="font-weight:600;font-size:.85rem;color:var(--text-primary);">${type === 'success' ? 'Success' : 'Notice'}</div>
            <div style="font-size:.8rem;color:var(--text-secondary);margin-top:.1rem;">${message}</div>
        </div>
    `;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}
