const clickEffects = new WeakMap();

document.getElementById('logout-form')?.addEventListener('submit', event => {
    if (!window.confirm('Keluar dari akun sekarang?')) event.preventDefault();
});

const passwordToggle = document.querySelector('[data-password-toggle]');
if (passwordToggle) {
    const password = document.getElementById(passwordToggle.getAttribute('aria-controls'));
    const icon = passwordToggle.querySelector('i');
    passwordToggle.hidden = false;
    passwordToggle.addEventListener('click', () => {
        const visible = password.type === 'password';
        password.type = visible ? 'text' : 'password';
        passwordToggle.setAttribute('aria-pressed', String(visible));
        passwordToggle.setAttribute('aria-label', visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        icon.classList.toggle('fa-eye', !visible);
        icon.classList.toggle('fa-eye-slash', visible);
    });
}

const submitProgress = document.querySelector('[data-submit-progress]');
if (submitProgress) {
    const progressLabel = submitProgress.querySelector('[role="status"]');
    let pendingForm;
    let recoveryTimer;

    function clearSubmitProgress() {
        clearTimeout(recoveryTimer);
        pendingForm?.removeAttribute('aria-busy');
        pendingForm = null;
        submitProgress.hidden = true;
    }

    document.addEventListener('submit', event => {
        const form = event.target;
        const method = event.submitter?.getAttribute('formmethod') || form.method;
        const target = event.submitter?.getAttribute('formtarget') || form.target;
        if (event.defaultPrevented || method.toLowerCase() !== 'post' || (target && target !== '_self')) return;

        if (pendingForm) {
            event.preventDefault();
            return;
        }

        pendingForm = form;
        form.setAttribute('aria-busy', 'true');
        progressLabel.textContent = form.dataset.submitMessage || 'Sedang menyimpan…';
        submitProgress.hidden = false;
        recoveryTimer = setTimeout(clearSubmitProgress, 45_000);
    });

    window.addEventListener('pageshow', clearSubmitProgress);
}

document.addEventListener('click', ({ target }) => {
    const action = target.closest('button, a, summary, [role="button"]');
    if (!action || action.matches(':disabled, [aria-disabled="true"]')) return;

    clearTimeout(clickEffects.get(action));
    action.classList.add('btn-clicked');
    clickEffects.set(action, setTimeout(() => action.classList.remove('btn-clicked'), 250));
});

async function refreshPendingLeaveNotice(notifications) {
    const message = notifications.querySelector('[data-notice-message]');
    const badge = notifications.querySelector('[data-notice-count]');
    const link = notifications.querySelector('[data-notice-link]');
    const bell = notifications.querySelector('summary');

    try {
        const response = await fetch(notifications.dataset.url, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Notifikasi tidak tersedia');

        const { count } = await response.json();
        badge.hidden = link.hidden = count === 0;
        badge.textContent = count > 99 ? '99+' : count;
        message.textContent = count > 0
            ? `${count} pengajuan cuti atau izin menunggu persetujuan.`
            : 'Tidak ada pengajuan yang menunggu persetujuan.';
        bell.setAttribute('aria-label', count > 0 ? `Notifikasi, ${count} pengajuan menunggu` : 'Notifikasi');
    } catch {
        badge.hidden = link.hidden = true;
        bell.setAttribute('aria-label', 'Notifikasi');
        message.textContent = 'Notifikasi belum dapat dimuat.';
    }
}

function addMobilePagination(table, scrollArea) {
    const mobile = window.matchMedia('(max-width: 767px)');
    const pageSize = 8;
    let currentPage = 1;

    const controls = document.createElement('div');
    controls.className = 'table-pages';
    controls.hidden = true;
    controls.innerHTML = '<button type="button">Sebelumnya</button><span aria-live="polite"></span><button type="button">Berikutnya</button>';
    scrollArea.after(controls);

    const [previous, status, next] = controls.children;
    const rows = Array.from(table.tBodies).flatMap(body => Array.from(body.rows))
        .filter(row => row.cells.length > 1);

    function render() {
        const visible = rows.filter(row => row.style.display !== 'none' && !row.hidden && !row.classList.contains('hidden'));
        const pageCount = Math.max(1, Math.ceil(visible.length / pageSize));
        currentPage = Math.min(currentPage, pageCount);
        const firstRow = (currentPage - 1) * pageSize;
        const pageRows = new Set(visible.slice(firstRow, firstRow + pageSize));

        rows.forEach(row => row.classList.toggle('is-outside-page', mobile.matches && !pageRows.has(row)));
        controls.hidden = !mobile.matches || pageCount < 2;
        previous.disabled = currentPage === 1;
        next.disabled = currentPage === pageCount;
        status.textContent = `Halaman ${currentPage} dari ${pageCount}`;
    }

    previous.addEventListener('click', () => {
        currentPage--;
        render();
    });
    next.addEventListener('click', () => {
        currentPage++;
        render();
    });
    table.addEventListener('table-filtered', () => {
        currentPage = 1;
        render();
    });
    mobile.addEventListener('change', render);
    render();
}

document.querySelectorAll('.app-content table').forEach(table => {
    let scrollArea = table.parentElement;
    if (!scrollArea.matches('.overflow-x-auto, .table-scroll')) {
        scrollArea = document.createElement('div');
        scrollArea.className = 'table-scroll';
        table.before(scrollArea);
        scrollArea.append(table);
    }

    if (table.hasAttribute('data-mobile-pages')) addMobilePagination(table, scrollArea);
});

document.querySelectorAll('.app-sidebar a').forEach(link => {
    const label = link.querySelector('span')?.textContent.trim();
    if (label) link.setAttribute('aria-label', label);
    if (link.classList.contains('bg-sky-blue')) link.setAttribute('aria-current', 'page');
});

const sidebar = document.getElementById('app-sidebar');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
if (sidebar && sidebarToggle) {
    const sidebarKey = 'kemenkum.sidebar.closed';

    const setSidebarOpen = open => {
        sidebar.hidden = !open;
        sidebarToggle.setAttribute('aria-expanded', String(open));
        sidebarToggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    };

    sidebarToggle.hidden = false;
    try {
        setSidebarOpen(sessionStorage.getItem(sidebarKey) !== 'true');
    } catch {
        setSidebarOpen(true);
    }

    sidebarToggle.addEventListener('click', () => {
        const open = sidebar.hidden;
        setSidebarOpen(open);
        try {
            sessionStorage.setItem(sidebarKey, String(!open));
        } catch {
            // Sidebar can still be toggled if session storage is unavailable.
        }
    });
}

const notifications = document.querySelector('[data-notifications]');
if (notifications) {
    if (notifications.dataset.url) {
        let pendingRefresh;
        const refresh = () => {
            pendingRefresh ??= refreshPendingLeaveNotice(notifications).finally(() => {
                pendingRefresh = undefined;
            });
        };
        refresh();
        notifications.addEventListener('toggle', () => {
            if (notifications.open) refresh();
        });
    }

    document.addEventListener('click', event => {
        if (notifications.open && !notifications.contains(event.target)) notifications.open = false;
    });
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape' || !notifications.open) return;
        notifications.open = false;
        notifications.querySelector('summary').focus();
    });
}

const attendanceChart = document.querySelector('[data-attendance-chart]');
if (attendanceChart) {
    const status = attendanceChart.querySelector('[data-chart-status]');
    let refreshing = false;

    async function refreshChart() {
        if (document.hidden || refreshing) return;
        refreshing = true;
        try {
            const response = await fetch(attendanceChart.dataset.url, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Statistik belum tersedia');
            const stats = await response.json();
            attendanceChart.querySelectorAll('[data-chart-value]').forEach(value => {
                value.textContent = stats[value.dataset.chartValue];
            });
            attendanceChart.querySelectorAll('[data-chart-meter]').forEach(meter => {
                meter.max = Math.max(1, stats.total_karyawan);
                meter.value = stats[meter.dataset.chartMeter];
            });
            status.textContent = 'Pembaruan otomatis setiap 30 detik';
        } catch {
            status.textContent = 'Pembaruan tertunda. Grafik menampilkan data terakhir.';
        } finally {
            refreshing = false;
        }
    }

    setInterval(refreshChart, 30_000);
    document.addEventListener('visibilitychange', refreshChart);
}

const statValues = [...document.querySelectorAll('[data-count-up], [data-chart-value], [data-chart-meter]')];
if (statValues.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const counters = statValues.map(element => {
        const property = element.tagName === 'METER' ? 'value' : 'textContent';
        const finalValue = Number(element[property]);
        return { element, property, finalValue };
    }).filter(({ finalValue }) => Number.isFinite(finalValue) && finalValue > 0);

    if (counters.length) {
        const duration = 650;
        let startTime;
        counters.forEach(({ element, property }) => { element[property] = 0; });

        function animateStats(time) {
            startTime ??= time;
            const progress = Math.min((time - startTime) / duration, 1);
            const eased = 1 - (1 - progress) ** 3;
            counters.forEach(({ element, property, finalValue }) => {
                element[property] = property === 'value' ? finalValue * eased : Math.round(finalValue * eased);
            });
            if (progress < 1) requestAnimationFrame(animateStats);
        }

        requestAnimationFrame(animateStats);
    }
}
