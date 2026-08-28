/**
 * LeadDesk Mini - Admin Dashboard Interactive Script
 * Updated for dark design system
 */

document.addEventListener('DOMContentLoaded', function () {
    let statusChartInstance = null;
    let budgetChartInstance = null;

    let currentFilter = 'All';
    let currentSearch = '';

    const searchInput    = document.getElementById('searchLeadInput');
    const filterPills    = document.querySelectorAll('.filter-pill');
    const leadsTableBody = document.getElementById('leadsTableBody');

    // ── Chart.js global defaults for dark theme ───────────────────────────
    if (typeof Chart !== 'undefined') {
        Chart.defaults.color           = '#8b949e';
        Chart.defaults.borderColor     = 'rgba(255,255,255,0.06)';
        Chart.defaults.font.family     = "'Inter', system-ui, sans-serif";
        Chart.defaults.font.size       = 11;
    }

    // ── Initialize Charts ─────────────────────────────────────────────────
    initDashboardCharts();

    // ── Live Search (debounced) ───────────────────────────────────────────
    if (searchInput) {
        let debounce;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounce);
            debounce = setTimeout(() => {
                currentSearch = this.value.trim();
                fetchLeadsData();
            }, 300);
        });
    }

    // ── Filter Pills ──────────────────────────────────────────────────────
    filterPills.forEach(pill => {
        pill.addEventListener('click', function () {
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            fetchLeadsData();
        });
    });

    // ── Status Select Delegation ──────────────────────────────────────────
    if (leadsTableBody) {
        leadsTableBody.addEventListener('change', function (e) {
            if (e.target.classList.contains('status-select')) {
                const sel       = e.target;
                const leadId    = sel.getAttribute('data-id');
                const newStatus = sel.value;
                const csrf      = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                if (confirm(`Change Lead #${leadId} status to "${newStatus}"?`)) {
                    updateLeadStatus(leadId, newStatus, csrf, sel);
                } else {
                    sel.value = sel.getAttribute('data-original');
                }
            }
        });
    }

    // ── AJAX: Update Status ───────────────────────────────────────────────
    function updateLeadStatus(id, status, csrf, sel) {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('status', status);
        fd.append('csrf_token', csrf);

        fetch('update-status.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message || `Lead #${id} → ${status}`);
                if (sel) {
                    sel.setAttribute('data-original', status);
                    updateSelectStyle(sel, status);
                }
                updateMetrics(data.metrics);
                refreshStatusChart(data.metrics);
            } else {
                showToast('error', data.message || 'Status update failed.');
                if (sel) sel.value = sel.getAttribute('data-original');
            }
        })
        .catch(() => {
            showToast('error', 'Network error. Please try again.');
            if (sel) sel.value = sel.getAttribute('data-original');
        });
    }

    // ── AJAX: Fetch Leads Table ───────────────────────────────────────────
    function fetchLeadsData() {
        const url = `leads.php?ajax=1&filter=${encodeURIComponent(currentFilter)}&search=${encodeURIComponent(currentSearch)}`;
        if (leadsTableBody) leadsTableBody.style.opacity = '0.4';

        fetch(url)
        .then(r => r.json())
        .then(data => {
            if (leadsTableBody) {
                leadsTableBody.style.opacity = '1';
                leadsTableBody.innerHTML = data.html;
            }
            if (data.metrics) {
                updateMetrics(data.metrics);
                refreshStatusChart(data.metrics);
            }
        })
        .catch(() => {
            if (leadsTableBody) leadsTableBody.style.opacity = '1';
        });
    }

    // ── DOM: Update metric card values ────────────────────────────────────
    function updateMetrics(m) {
        if (!m) return;
        const ids = { total: 'metricTotal', new: 'metricNew', contacted: 'metricContacted', closed: 'metricClosed' };
        Object.keys(ids).forEach(k => {
            const el = document.getElementById(ids[k]);
            if (el && m[k] !== undefined) el.textContent = m[k].toLocaleString();
        });
    }

    // ── DOM: Update status select styling ────────────────────────────────
    function updateSelectStyle(sel, status) {
        sel.className = 'status-select';
        if (status === 'New')       sel.classList.add('badge-new');
        else if (status === 'Contacted') sel.classList.add('badge-contacted');
        else if (status === 'Closed')    sel.classList.add('badge-closed');
    }

    // ── Chart.js Initialization ───────────────────────────────────────────
    function initDashboardCharts() {
        const statusCanvas = document.getElementById('statusChart');
        const budgetCanvas = document.getElementById('budgetChart');

        if (statusCanvas && typeof Chart !== 'undefined') {
            const n = parseInt(statusCanvas.dataset.new       || 0);
            const c = parseInt(statusCanvas.dataset.contacted || 0);
            const cl= parseInt(statusCanvas.dataset.closed    || 0);

            statusChartInstance = new Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['New', 'Contacted', 'Closed'],
                    datasets: [{
                        data: [n, c, cl],
                        backgroundColor: ['#58646f', '#1aff9c', '#fbbf24', '#f87171'],
                        borderWidth: 3,
                        borderColor: '#161b22',
                        hoverBorderColor: '#161b22',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1c2230',
                            borderColor: 'rgba(255,255,255,0.08)',
                            borderWidth: 1,
                            titleColor: '#e6edf3',
                            bodyColor: '#8b949e',
                            padding: 10
                        }
                    }
                }
            });
        }

        if (budgetCanvas && typeof Chart !== 'undefined') {
            const b1 = parseInt(budgetCanvas.dataset.b1 || 0);
            const b2 = parseInt(budgetCanvas.dataset.b2 || 0);
            const b3 = parseInt(budgetCanvas.dataset.b3 || 0);
            const b4 = parseInt(budgetCanvas.dataset.b4 || 0);

            budgetChartInstance = new Chart(budgetCanvas, {
                type: 'bar',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','May','Jun'],
                    datasets: [{
                        label: 'Budget Leads',
                        data: [b1, b2, b3, b4, Math.max(b3,b4), Math.max(b1+b2, b3+b4)],
                        backgroundColor: 'rgba(129,140,248,0.25)',
                        borderColor: 'transparent',
                        borderRadius: 6,
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(26,255,156,0.5)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1c2230',
                            borderColor: 'rgba(255,255,255,0.08)',
                            borderWidth: 1,
                            titleColor: '#e6edf3',
                            bodyColor: '#8b949e',
                            padding: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#58646f' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255,255,255,0.04)' },
                            ticks: { color: '#58646f', precision: 0 },
                            border: { display: false }
                        }
                    }
                }
            });
        }
    }

    // ── Refresh Donut Chart on status change ──────────────────────────────
    function refreshStatusChart(m) {
        if (statusChartInstance && m) {
            statusChartInstance.data.datasets[0].data = [m.new, m.contacted, m.closed, 0];
            statusChartInstance.update();
        }
    }
});

// ── Lead Detail Modal ─────────────────────────────────────────────────────
function openLeadModal(jsonStr) {
    try {
        const lead = JSON.parse(jsonStr);
        document.getElementById('modalLeadId').textContent       = '#' + lead.id;
        document.getElementById('modalLeadName').textContent     = lead.name;
        document.getElementById('modalLeadEmailLink').textContent= lead.email;
        document.getElementById('modalLeadEmailLink').href       = 'mailto:' + lead.email;
        document.getElementById('modalLeadBudget').textContent   = lead.budget;
        document.getElementById('modalLeadMessage').textContent  = lead.message;
        document.getElementById('modalLeadStatus').innerHTML     = statusBadgeHtml(lead.status);
        document.getElementById('modalLeadCreated').textContent  = lead.created_at;
        document.getElementById('modalLeadUpdated').textContent  = lead.updated_at || lead.created_at;

        const el = document.getElementById('leadDetailModal');
        if (el && typeof bootstrap !== 'undefined') new bootstrap.Modal(el).show();
    } catch (e) {
        console.error('Modal error:', e);
    }
}

function statusBadgeHtml(status) {
    const map = {
        'New':       ['badge-new',       'fa-bolt'],
        'Contacted': ['badge-contacted', 'fa-paper-plane'],
        'Closed':    ['badge-closed',    'fa-circle-check']
    };
    const [cls, icon] = map[status] || map['New'];
    return `<span class="badge-status ${cls}"><i class="fa-solid ${icon}"></i> ${status}</span>`;
}
