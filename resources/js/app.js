import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('dashboard', () => ({
    loading: false,
    data: null,
    dateFilter: 'today',
    statusFilter: '',
    sectorFilter: '',
    sectors: [],
    init() {
        this.loadData();
    },
    loadData() {
        this.loading = true;
        axios.get('/api/dashboard/data', {
            params: { dateFilter: this.dateFilter, statusFilter: this.statusFilter, sectorFilter: this.sectorFilter }
        }).then(res => {
            this.data = res.data;
            this.sectors = res.data.sectors || [];
        }).finally(() => { this.loading = false; });
    },
    clearFilters() {
        this.dateFilter = 'today';
        this.statusFilter = '';
        this.sectorFilter = '';
        this.loadData();
    }
}));

Alpine.data('sessionsTable', () => ({
    loading: false,
    sessions: [],
    sectors: [],
    locations: [],
    filteredCount: 0,
    totalMinutes: 0,
    completeCount: 0,
    totalPages: 1,
    currentPage: 1,
    perPage: 25,
    search: '',
    status: '',
    sector: '',
    location: '',
    duration: '',
    integrity: '',
    dateFrom: '',
    dateTo: '',
    syncMessage: '',
    init() {
        this.loadSessions();
    },
    loadSessions() {
        this.loading = true;
        axios.get('/api/sessions', { params: this.getParams() })
            .then(res => {
                this.sessions = res.data.sessions;
                this.sectors = res.data.sectors;
                this.locations = res.data.locations;
                this.filteredCount = res.data.filteredCount;
                this.totalMinutes = res.data.totalMinutes;
                this.completeCount = res.data.completeCount;
                this.totalPages = res.data.totalPages;
                this.currentPage = res.data.currentPage;
            }).finally(() => { this.loading = false; });
    },
    getParams() {
        return {
            search: this.search, status: this.status, sector: this.sector,
            location: this.location, duration: this.duration, integrity: this.integrity,
            dateFrom: this.dateFrom, dateTo: this.dateTo, perPage: this.perPage, page: this.currentPage
        };
    },
    goToPage(page) {
        if (page < 1 || page > this.totalPages) return;
        this.currentPage = page;
        this.loadSessions();
    },
    clearFilters() {
        this.search = ''; this.status = ''; this.sector = ''; this.location = '';
        this.duration = ''; this.integrity = ''; this.dateFrom = ''; this.dateTo = '';
        this.currentPage = 1;
        this.loadSessions();
    },
    syncAttendance() {
        this.loading = true;
        axios.post('/api/sessions/sync').then(res => {
            this.syncMessage = res.data.message;
            this.loadSessions();
        }).catch(() => { this.syncMessage = 'Sync failed. Check MySQL connection.'; })
        .finally(() => { this.loading = false; });
    }
}));

Alpine.data('reportsApp', () => ({
    loading: false,
    reportType: 'user_activity',
    dateFrom: '',
    dateTo: '',
    status: '',
    sector: '',
    personnel: '',
    results: null,
    reportStats: null,
    showFormalTemplate: false,
    selectedTemplate: 'certificate',
    init() {},
    generateReport() {
        this.loading = true;
        axios.post('/api/reports/generate', {
            reportType: this.reportType, dateFrom: this.dateFrom, dateTo: this.dateTo,
            status: this.status, sector: this.sector, personnel: this.personnel
        }).then(res => {
            this.results = res.data.results;
            this.reportStats = res.data.reportStats;
        }).finally(() => { this.loading = false; });
    },
    clearFilters() {
        this.dateFrom = ''; this.dateTo = ''; this.status = ''; this.sector = ''; this.personnel = '';
        this.results = null; this.reportStats = null;
    },
    exportCSV() {
        axios.post('/api/reports/export-csv', { data: this.results?.data || [] });
    },
    exportPDF() {
        axios.post('/api/reports/export-pdf', { data: this.results?.data || [] });
    },
    toggleFormalTemplate(template) {
        this.selectedTemplate = template;
        this.showFormalTemplate = true;
    },
    closeFormalTemplate() { this.showFormalTemplate = false; }
}));

Alpine.data('notificationCenter', (fullPage = false) => ({
    open: fullPage,
    fullPage: fullPage,
    notifications: [],
    unreadCount: 0,
    init() {
        this.loadNotifications();
    },
    loadNotifications() {
        axios.get('/api/notifications').then(res => {
            this.notifications = res.data.notifications;
            this.unreadCount = res.data.unreadCount;
        });
    },
    markAsRead(id) {
        axios.post(`/api/notifications/${id}/read`).then(() => this.loadNotifications());
    },
    markAllAsRead() {
        axios.post('/api/notifications/read-all').then(() => this.loadNotifications());
    },
    delete(id) {
        axios.delete(`/api/notifications/${id}`).then(() => this.loadNotifications());
    }
}));

Alpine.data('analyticsApp', () => ({
    loading: false,
    chartData: null,
    totalSessions: 0,
    totalHours: 0,
    activeVolunteers: 0,
    period: 'month',
    status: '',
    sector: '',
    dateFrom: '',
    dateTo: '',
    sectors: [],
    insights: {},
    sessionsByStatus: {},
    init() {
        this.loadData();
    },
    loadData() {
        this.loading = true;
        axios.get('/api/analytics/data', {
            params: { period: this.period, status: this.status, sector: this.sector, dateFrom: this.dateFrom, dateTo: this.dateTo }
        }).then(res => {
            this.chartData = res.data.chartData;
            this.totalSessions = res.data.totalSessions;
            this.totalHours = res.data.totalHours;
            this.activeVolunteers = res.data.activeVolunteers;
            this.insights = res.data.insights;
            this.sessionsByStatus = res.data.sessionsByStatus;
            this.sectors = res.data.sectors;
            this.$nextTick(() => { if (this.$refs.chartCanvas) this.drawChart(); });
        }).finally(() => { this.loading = false; });
    },
    filter(period) {
        this.period = period;
        this.dateFrom = '';
        this.dateTo = '';
        this.loadData();
    },
    clearFilters() {
        this.status = ''; this.sector = ''; this.dateFrom = ''; this.dateTo = '';
        this.loadData();
    },
    drawChart() {
        const canvas = this.$refs.chartCanvas;
        if (!canvas || !this.chartData) return;
        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        canvas.style.width = rect.width + 'px';
        canvas.style.height = rect.height + 'px';
        ctx.scale(dpr, dpr);

        const labels = this.chartData.labels || [];
        const sessions = this.chartData.datasets?.[0]?.data || [];
        if (!labels.length) {
            ctx.fillStyle = '#9CA3AF';
            ctx.font = '14px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('No data available', rect.width / 2, rect.height / 2);
            return;
        }

        const padding = { top: 20, right: 20, bottom: 40, left: 50 };
        const chartW = rect.width - padding.left - padding.right;
        const chartH = rect.height - padding.top - padding.bottom;
        const maxSessions = Math.max(...sessions, 1);
        const barWidth = Math.min(chartW / labels.length * 0.35, 30);
        const gap = chartW / labels.length;

        ctx.strokeStyle = '#F3F4F6';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 4; i++) {
            const y = padding.top + (chartH / 4) * i;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(rect.width - padding.right, y);
            ctx.stroke();
        }

        labels.forEach((label, i) => {
            const x = padding.left + gap * i + (gap - barWidth) / 2;
            const barH = (sessions[i] / maxSessions) * chartH;
            const y = padding.top + chartH - barH;
            const grad = ctx.createLinearGradient(x, y, x, padding.top + chartH);
            grad.addColorStop(0, '#F97316');
            grad.addColorStop(1, '#EF4444');
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.roundRect(x, y, barWidth, barH, [3, 3, 0, 0]);
            ctx.fill();
            ctx.fillStyle = '#6B7280';
            ctx.font = '10px Inter, sans-serif';
            ctx.textAlign = 'center';
            const displayLabel = label.length > 7 ? label.substring(0, 7) + '...' : label;
            ctx.fillText(displayLabel, x + barWidth / 2, padding.top + chartH + 16);
            ctx.fillStyle = '#374151';
            ctx.font = 'bold 10px Inter, sans-serif';
            ctx.fillText(sessions[i], x + barWidth / 2, y - 4);
        });
    }
}));

Alpine.data('rankingsApp', () => ({
    loading: false,
    rankings: [],
    topThree: [],
    search: '',
    sortBy: 'total_hours',
    period: 'all',
    showScoringGuide: false,
    init() {
        this.loadRankings();
    },
    loadRankings() {
        this.loading = true;
        axios.get('/api/rankings', { params: { sortBy: this.sortBy, search: this.search, period: this.period } })
            .then(res => {
                this.rankings = res.data.rankings;
                this.topThree = res.data.topThree;
            }).finally(() => { this.loading = false; });
    },
    toggleScoringGuide() { this.showScoringGuide = !this.showScoringGuide; },
    getAchievements(totalMinutes) {
        const hours = totalMinutes / 60;
        if (hours >= 100) return [{ label: 'Century', icon: '🏆', color: 'text-yellow-600' }];
        if (hours >= 50) return [{ label: 'Veteran', icon: '⭐', color: 'text-amber-600' }];
        if (hours >= 25) return [{ label: 'Dedicated', icon: '🔥', color: 'text-orange-600' }];
        if (hours >= 10) return [{ label: 'Rising', icon: '💪', color: 'text-blue-600' }];
        return [{ label: 'Beginner', icon: '🌱', color: 'text-green-600' }];
    },
    fmtHours(mins) {
        if (!mins || mins === 0) return '0h';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return m === 0 ? `${h}h` : `${h}h ${m}m`;
    }
}));

Alpine.data('personnelApp', () => ({
    loading: false,
    personnel: [],
    totalPersonnel: 0,
    cleanCount: 0,
    issueCount: 0,
    totalHours: 0,
    totalPages: 1,
    currentPage: 1,
    search: '',
    sortBy: 'name',
    sortDirection: 'asc',
    complianceFilter: 'all',
    viewMode: 'list',
    showFormula: false,
    selectedPersonnelName: null,
    historySessions: [],
    init() {
        this.loadPersonnel();
    },
    loadPersonnel() {
        this.loading = true;
        axios.get('/api/personnel', { params: this.getParams() })
            .then(res => {
                this.personnel = res.data.personnel;
                this.totalPersonnel = res.data.totalPersonnel;
                this.cleanCount = res.data.cleanCount;
                this.issueCount = res.data.issueCount;
                this.totalHours = res.data.totalHours;
                this.totalPages = res.data.totalPages;
                this.currentPage = res.data.currentPage;
            }).finally(() => { this.loading = false; });
    },
    getParams() {
        return { search: this.search, sortBy: this.sortBy, sortDirection: this.sortDirection, complianceFilter: this.complianceFilter, viewMode: this.viewMode, page: this.currentPage };
    },
    toggleSort(field) {
        if (this.sortBy === field) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortBy = field;
            this.sortDirection = 'asc';
        }
        this.loadPersonnel();
    },
    toggleFormula() { this.showFormula = !this.showFormula; },
    viewHistory(name) {
        this.selectedPersonnelName = name;
        axios.get('/api/personnel/history', { params: { name } })
            .then(res => { this.historySessions = res.data.sessions; });
    },
    closeHistory() { this.selectedPersonnelName = null; this.historySessions = []; },
    goToPage(page) { this.currentPage = page; this.loadPersonnel(); },
    fmtHours(mins) {
        if (!mins || mins === 0) return '0h';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return m === 0 ? `${h}h` : `${h}h ${m}m`;
    },
    getInitials(name) {
        return name.split(' ').map(p => p[0]).join('').toUpperCase().substring(0, 2);
    }
}));

Alpine.data('accountsApp', () => ({
    loading: false,
    accounts: [],
    totalPages: 1,
    currentPage: 1,
    total: 0,
    search: '',
    statusFilter: '',
    perPage: 25,
    bulkAction: '',
    selectAll: false,
    selectedAccounts: [],
    init() {
        this.loadAccounts();
    },
    loadAccounts() {
        this.loading = true;
        axios.get('/api/accounts', { params: { search: this.search, statusFilter: this.statusFilter, perPage: this.perPage, page: this.currentPage } })
            .then(res => {
                this.accounts = res.data.accounts;
                this.totalPages = res.data.totalPages;
                this.currentPage = res.data.currentPage;
                this.total = res.data.total;
            }).finally(() => { this.loading = false; });
    },
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedAccounts = this.accounts.map(a => a.id);
        } else {
            this.selectedAccounts = [];
        }
    },
    toggleAccount(id) {
        const idx = this.selectedAccounts.indexOf(id);
        if (idx > -1) { this.selectedAccounts.splice(idx, 1); } else { this.selectedAccounts.push(id); }
        this.selectAll = this.selectedAccounts.length === this.accounts.length && this.accounts.length > 0;
    },
    approve(id) {
        axios.post(`/api/accounts/${id}/approve`).then(() => this.loadAccounts());
    },
    reject(id) {
        axios.post(`/api/accounts/${id}/reject`).then(() => this.loadAccounts());
    },
    suspend(id) {
        axios.post(`/api/accounts/${id}/suspend`).then(() => this.loadAccounts());
    },
    executeBulkAction() {
        if (!this.bulkAction || !this.selectedAccounts.length) return;
        axios.post('/api/accounts/bulk-action', { ids: this.selectedAccounts, action: this.bulkAction })
            .then(() => { this.selectedAccounts = []; this.selectAll = false; this.bulkAction = ''; this.loadAccounts(); });
    }
}));

Alpine.data('auditLogsApp', () => ({
    loading: false,
    logs: [],
    totalPages: 1,
    currentPage: 1,
    total: 0,
    search: '',
    type: '',
    dateFrom: '',
    dateTo: '',
    perPage: 50,
    init() {
        this.loadLogs();
    },
    loadLogs() {
        this.loading = true;
        axios.get('/api/audit-logs', { params: { search: this.search, type: this.type, dateFrom: this.dateFrom, dateTo: this.dateTo, perPage: this.perPage, page: this.currentPage } })
            .then(res => {
                this.logs = res.data.logs;
                this.totalPages = res.data.totalPages;
                this.currentPage = res.data.currentPage;
                this.total = res.data.total;
            }).finally(() => { this.loading = false; });
    },
    clearFilters() {
        this.search = ''; this.type = ''; this.dateFrom = ''; this.dateTo = '';
        this.currentPage = 1;
        this.loadLogs();
    },
    exportLogs() {
        axios.get('/api/audit-logs/export', { params: { search: this.search, type: this.type, dateFrom: this.dateFrom, dateTo: this.dateTo } });
    }
}));

Alpine.start();
