import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('appShell', ({ timeoutMinutes = 60, warningMinutes = 5 } = {}) => ({
    sidebarOpen: false,
    sessionWarningVisible: false,
    sessionWarningCountdown: warningMinutes * 60,
    sessionWarningTimer: null,
    initSessionWarning() {
        if (!timeoutMinutes || timeoutMinutes <= warningMinutes) return;

        const warningAfterMs = Math.max(1, timeoutMinutes - warningMinutes) * 60 * 1000;
        window.setTimeout(() => {
            this.sessionWarningVisible = true;
            this.sessionWarningCountdown = warningMinutes * 60;
            this.sessionWarningTimer = window.setInterval(() => {
                this.sessionWarningCountdown = Math.max(0, this.sessionWarningCountdown - 1);
            }, 1000);
        }, warningAfterMs);
    },
    keepSessionAlive() {
        axios.get('/api/notifications').finally(() => {
            this.sessionWarningVisible = false;
            if (this.sessionWarningTimer) {
                window.clearInterval(this.sessionWarningTimer);
            }
            this.initSessionWarning();
        });
    }
}));

Alpine.data('dashboard', () => ({
    loading: false,
    data: null,
    dateFilter: 'all',
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
        }).catch(err => {
            console.error('Dashboard data load failed:', err);
            alert('Failed to load dashboard data. Check console for details.');
        }).finally(() => { this.loading = false; });
    },
    clearFilters() {
        this.dateFilter = 'today';
        this.statusFilter = '';
        this.sectorFilter = '';
        this.loadData();
    },
    logTimeIn() {
        axios.post('/api/member/time-in').then(res => {
            if (res.data.success) {
                this.loadData();
            }
        }).catch(err => {
            alert(err.response?.data?.message || 'Failed to log time in.');
        });
    },
    logTimeOut() {
        axios.post('/api/member/time-out').then(res => {
            if (res.data.success) {
                this.loadData();
            }
        }).catch(err => {
            alert(err.response?.data?.message || 'Failed to log time out.');
        });
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
        this.loadSessions().then(() => {
            if (this.sessions.length === 0 && this.filteredCount === 0) {
                this.syncMessage = 'No attendance data found. Use "Sync MySQL" to import attendance records.';
            }
        });
    },
    loadSessions() {
        this.loading = true;
        return axios.get('/api/sessions', { params: this.getParams() })
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
    applyFilters() {
        this.currentPage = 1;
        this.loadSessions();
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
    exportCSV() {
        if (!this.sessions.length) return;
        const headers = ['Name','Date','Time In','Time Out','Duration (min)','Status','Location','Sector','Integrity'];
        const rows = this.sessions.map(s => [s.full_name, s.date, s.time_in || '', s.time_out || '', s.duration_minutes || '', s.status, s.location || '', s.sector || '', s.integrity_score ? Math.round(s.integrity_score) + '%' : '']);
        const csv = [headers.join(','), ...rows.map(r => r.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'attendance_export.csv';
        document.body.appendChild(a); a.click(); a.remove(); window.URL.revokeObjectURL(url);
    },
    processLocal() {
        this.loading = true;
        axios.post('/api/sessions/process-local').then(res => {
            this.syncMessage = res.data.message;
            this.loadSessions();
        }).catch(() => { this.syncMessage = 'Local processing failed.'; })
        .finally(() => { this.loading = false; });
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
        axios.post('/api/reports/export-csv', { data: this.results?.data || [] }, { responseType: 'blob' })
            .then(res => this.downloadBlob(res.data, 'formal_attendance_report.csv'));
    },
    exportPDF() {
        axios.post('/api/reports/export-pdf', { data: this.results?.data || [] }, { responseType: 'blob' })
            .then(res => this.downloadBlob(res.data, 'formal_attendance_report.pdf'));
    },
    downloadBlob(blob, filename) {
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    },
    toggleFormalTemplate(template) {
        this.selectedTemplate = template;
        this.showFormalTemplate = true;
    },
    closeFormalTemplate() { this.showFormalTemplate = false; }
}));

Alpine.data('timeLog', () => ({
    active: false,
    processing: false,
    timeInDisplay: '',
    elapsed: '0h 0m',
    message: '',
    messageType: 'success',
    statusText: 'No active session',
    timer: null,
    init() {
        this.checkStatus();
    },
    checkStatus() {
        axios.get('/api/member/time-status').then(res => {
            this.active = res.data.active;
            if (this.active) {
                this.timeInDisplay = res.data.time_in;
                this.elapsed = res.data.elapsed;
                this.statusText = 'Active since ' + res.data.since;
                this.startTimer();
            } else {
                this.statusText = 'Ready to log time';
                this.stopTimer();
            }
        });
    },
    timeIn() {
        this.processing = true;
        this.message = '';
        axios.post('/api/member/time-in').then(res => {
            this.message = res.data.message;
            this.messageType = 'success';
            this.active = true;
            this.timeInDisplay = res.data.session.time_in;
            this.elapsed = '0h 0m';
            this.statusText = 'Active since just now';
            this.startTimer();
        }).catch(err => {
            this.message = err.response?.data?.error || 'Failed to log time in.';
            this.messageType = 'error';
        }).finally(() => { this.processing = false; });
    },
    timeOut() {
        this.processing = true;
        this.message = '';
        axios.post('/api/member/time-out').then(res => {
            this.message = res.data.message;
            this.messageType = 'success';
            this.active = false;
            this.stopTimer();
            this.statusText = 'Ready to log time';
        }).catch(err => {
            this.message = err.response?.data?.error || 'Failed to log time out.';
            this.messageType = 'error';
        }).finally(() => { this.processing = false; });
    },
    startTimer() {
        this.stopTimer();
        this.timer = window.setInterval(() => {
            this.elapsed = this.formatElapsed();
        }, 60000);
        this.elapsed = this.formatElapsed();
    },
    stopTimer() {
        if (this.timer) {
            window.clearInterval(this.timer);
            this.timer = null;
        }
    },
    formatElapsed() {
        if (!this.timeInDisplay) return '0h 0m';
        const now = new Date();
        const timeParts = this.timeInDisplay.match(/(\d+):(\d+)\s*(AM|PM)/i);
        if (!timeParts) return '0h 0m';
        let hours = parseInt(timeParts[1]);
        const minutes = parseInt(timeParts[2]);
        const ampm = timeParts[3].toUpperCase();
        if (ampm === 'PM' && hours !== 12) hours += 12;
        if (ampm === 'AM' && hours === 12) hours = 0;
        const timeInDate = new Date();
        timeInDate.setHours(hours, minutes, 0, 0);
        const diffMs = now - timeInDate;
        if (diffMs <= 0) return '0h 0m';
        const diffMins = Math.floor(diffMs / 60000);
        const h = Math.floor(diffMins / 60);
        const m = diffMins % 60;
        return h + 'h ' + m + 'm';
    }
}));

Alpine.data('memberAttendanceApp', () => ({
    loading: false,
    dateFrom: '',
    dateTo: '',
    status: '',
    results: null,
    reportStats: null,
    currentPage: 1,
    perPage: 15,
    get paginatedRecords() {
        const records = this.results?.data?.records || this.results?.data || [];
        const start = (this.currentPage - 1) * this.perPage;
        return records.slice(start, start + this.perPage);
    },
    get totalAttendancePages() {
        const records = this.results?.data?.records || this.results?.data || [];
        return Math.max(1, Math.ceil(records.length / this.perPage));
    },
    get attendancePageNumbers() {
        const pages = [];
        const total = this.totalAttendancePages;
        const cur = this.currentPage;
        if (total <= 7) {
            for (let i = 1; i <= total; i++) pages.push(i);
            return pages;
        }
        pages.push(1);
        if (cur > 3) pages.push('…');
        for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
        if (cur < total - 2) pages.push('…');
        pages.push(total);
        return pages;
    },
    goToAttendancePage(page) {
        if (page < 1 || page > this.totalAttendancePages) return;
        this.currentPage = page;
    },
    generateReport() {
        this.currentPage = 1;
        this.loading = true;
        axios.get('/api/member/attendance', {
            params: { dateFrom: this.dateFrom, dateTo: this.dateTo, status: this.status }
        }).then(res => {
            this.results = res.data.results;
            this.reportStats = res.data.reportStats;
        }).finally(() => { this.loading = false; });
    },
    clearFilters() {
        this.dateFrom = '';
        this.dateTo = '';
        this.status = '';
        this.results = null;
        this.reportStats = null;
        this.currentPage = 1;
    }
}));

Alpine.data('notificationCenter', (fullPage = false) => ({
    open: fullPage,
    fullPage: fullPage,
    notifications: [],
    unreadCount: 0,
    init() {
        this.loadNotifications();
        this.startRealtimeStream();
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
    removeNotification(id) {
        axios.delete(`/api/notifications/${id}`).then(() => this.loadNotifications());
    },
    startRealtimeStream() {
        if (!window.EventSource) {
            window.setInterval(() => this.loadNotifications(), 30000);
            return;
        }

        const stream = new EventSource('/api/notifications/stream');
        stream.addEventListener('notifications', () => this.loadNotifications());
        stream.onerror = () => {
            stream.close();
            window.setTimeout(() => this.startRealtimeStream(), 30000);
        };
    }
}));

Alpine.data('logControl', () => ({
    logging: false,
    hasActiveSession: false,
    activeSince: '',
    elapsedMinutes: 0,
    logMessage: '',
    logSuccess: false,
    timerInterval: null,
    init() {
        this.checkStatus();
    },
    checkStatus() {
        axios.get('/api/member/log-status').then(res => {
            this.hasActiveSession = res.data.hasActiveSession;
            if (res.data.activeSession) {
                this.activeSince = res.data.activeSession.time_in;
                this.elapsedMinutes = res.data.activeSession.duration || 0;
            }
        });
    },
    logTimeIn() {
        this.logging = true;
        this.logMessage = '';
        axios.post('/api/member/time-in').then(res => {
            this.logSuccess = true;
            this.logMessage = res.data.message;
            this.hasActiveSession = true;
            this.activeSince = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            this.elapsedMinutes = 0;
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => { this.elapsedMinutes++; }, 60000);
            setTimeout(() => { this.logMessage = ''; }, 5000);
        }).catch(err => {
            this.logSuccess = false;
            this.logMessage = err.response?.data?.message || 'Failed to log time in.';
        }).finally(() => { this.logging = false; });
    },
    logTimeOut() {
        this.logging = true;
        this.logMessage = '';
        axios.post('/api/member/time-out').then(res => {
            this.logSuccess = true;
            this.logMessage = res.data.message;
            this.hasActiveSession = false;
            this.activeSince = '';
            if (this.timerInterval) clearInterval(this.timerInterval);
            setTimeout(() => { this.logMessage = ''; }, 5000);
        }).catch(err => {
            this.logSuccess = false;
            this.logMessage = err.response?.data?.message || 'Failed to log time out.';
        }).finally(() => { this.logging = false; });
    },
    fmtDuration(mins) {
        if (!mins || mins === 0) return '0m';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return m === 0 ? `${h}h` : `${h}h ${m}m`;
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
    totalPages: 1,
    currentPage: 1,
    perPage: 15,
    total: 0,
    init() {
        this.loadRankings();
    },
    loadRankings() {
        this.loading = true;
        axios.get('/api/rankings', { params: { sortBy: this.sortBy, search: this.search, period: this.period, page: this.currentPage, perPage: this.perPage } })
            .then(res => {
                this.rankings = res.data.rankings;
                this.topThree = res.data.topThree || [];
                this.totalPages = res.data.totalPages || 1;
                this.currentPage = res.data.currentPage || 1;
                this.total = res.data.total || 0;
            }).finally(() => { this.loading = false; });
    },
    applyFilters() {
        this.currentPage = 1;
        this.loadRankings();
    },
    goToPage(page) {
        if (page < 1 || page > this.totalPages) return;
        this.currentPage = page;
        this.loadRankings();
    },
    get pageNumbers() {
        const pages = [];
        const total = this.totalPages;
        const cur = this.currentPage;
        if (total <= 7) {
            for (let i = 1; i <= total; i++) pages.push(i);
            return pages;
        }
        pages.push(1);
        if (cur > 3) pages.push('…');
        for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
        if (cur < total - 2) pages.push('…');
        pages.push(total);
        return pages;
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
        if (!mins || mins === 0) return '0.00h';
        const totalHours = mins / 60;
        return totalHours.toFixed(2) + 'h';
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
    get pageNumbers() {
        const pages = [];
        const total = this.totalPages;
        const cur = this.currentPage;
        if (total <= 7) {
            for (let i = 1; i <= total; i++) pages.push(i);
            return pages;
        }
        pages.push(1);
        if (cur > 3) pages.push('…');
        for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
        if (cur < total - 2) pages.push('…');
        pages.push(total);
        return pages;
    },
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
    applyFilters() {
        this.currentPage = 1;
        this.loadPersonnel();
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
    goToPage(page) {
        if (page < 1 || page > this.totalPages) return;
        this.currentPage = page;
        this.loadPersonnel();
    },
    fmtHours(mins) {
        if (!mins || mins === 0) return '0.00h';
        const totalHours = mins / 60;
        return totalHours.toFixed(2) + 'h';
    },
    getInitials(name) {
        return name.split(' ').map(p => p[0]).join('').toUpperCase().substring(0, 2);
    },
    exportCSV() {
        if (!this.personnel.length) return;
        const headers = ['Name','Email','Role','Sessions','Regular (h)','Overtime (h)','Undertime (h)','Issues','Last Active'];
        const rows = this.personnel.map(p => [
            p.fullName, p.email, p.role, p.sessionCount,
            this.fmtHours(p.totalRegularMinutes), this.fmtHours(p.totalOvertimeMinutes),
            this.fmtHours(p.totalUndertimeMinutes), p.invalidRecordCount, p.lastActive || ''
        ]);
        const csv = [headers.join(','), ...rows.map(r => r.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'personnel_export.csv';
        document.body.appendChild(a); a.click(); a.remove(); window.URL.revokeObjectURL(url);
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
    errorMessage: '',
    init() {
        this.loadAccounts();
    },
    loadAccounts() {
        this.loading = true;
        this.errorMessage = '';
        axios.get('/api/accounts', { params: { search: this.search, statusFilter: this.statusFilter, perPage: this.perPage, page: this.currentPage } })
            .then(res => {
                this.accounts = res.data.accounts;
                this.totalPages = res.data.totalPages;
                this.currentPage = res.data.currentPage;
                this.total = res.data.total;
            }).catch(err => {
                this.errorMessage = 'Failed to load accounts: ' + (err.response?.data?.message || err.message);
            }).finally(() => { this.loading = false; });
    },
    loadAccounts() {
        this.loading = true;
        axios.get('/api/accounts', { params: { search: this.search, statusFilter: this.statusFilter, perPage: this.perPage, page: this.currentPage } })
            .then(res => {
                this.accounts = res.data.accounts || [];
                this.totalPages = res.data.totalPages || 1;
                this.currentPage = res.data.currentPage || 1;
                this.total = res.data.total || 0;
            }).catch(err => {
                this.accounts = [];
                console.error('Failed to load accounts:', err);
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
    },
    exportCSV() {
        if (!this.accounts.length) return;
        const headers = ['Name','Email','Role','Status','Registered'];
        const rows = this.accounts.map(a => [a.full_name, a.email, a.role, a.status, a.created_at]);
        const csv = [headers.join(','), ...rows.map(r => r.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'accounts_export.csv';
        document.body.appendChild(a); a.click(); a.remove(); window.URL.revokeObjectURL(url);
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

Alpine.data('timeLog', () => ({
    loading: false,
    hasActiveSession: false,
    activeSession: null,
    todayTotalMinutes: 0,
    elapsedMinutes: 0,
    timerInterval: null,
    message: '',
    messageType: '',
    init() {
        this.checkStatus();
    },
    checkStatus() {
        axios.get('/api/member/log-status').then(res => {
            this.hasActiveSession = res.data.hasActiveSession;
            this.activeSession = res.data.activeSession;
            this.todayTotalMinutes = res.data.todayTotalMinutes;
            if (this.hasActiveSession && this.activeSession) {
                this.startElapsedTimer();
            }
        });
    },
    startElapsedTimer() {
        this.calculateElapsed();
        this.timerInterval = setInterval(() => { this.calculateElapsed(); }, 60000);
    },
    calculateElapsed() {
        if (this.activeSession?.time_in_raw) {
            const timeIn = new Date(this.activeSession.time_in_raw);
            this.elapsedMinutes = Math.floor((new Date() - timeIn) / 60000);
        }
    },
    logTimeIn() {
        this.loading = true;
        this.message = '';
        axios.post('/api/member/time-in').then(res => {
            this.message = res.data.message;
            this.messageType = 'success';
            this.checkStatus();
        }).catch(err => {
            this.message = err.response?.data?.message || 'Failed to log time in.';
            this.messageType = 'error';
        }).finally(() => { this.loading = false; });
    },
    logTimeOut() {
        this.loading = true;
        this.message = '';
        axios.post('/api/member/time-out').then(res => {
            this.message = res.data.message;
            this.messageType = 'success';
            this.checkStatus();
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
                this.timerInterval = null;
            }
            this.elapsedMinutes = 0;
        }).catch(err => {
            this.message = err.response?.data?.message || 'Failed to log time out.';
            this.messageType = 'error';
        }).finally(() => { this.loading = false; });
    },
    fmtDuration(mins) {
        if (!mins || mins === 0) return '0m';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return h > 0 ? `${h}h ${m}m` : `${m}m`;
    }
}));

Alpine.data('logControl', () => ({
    loading: false,
    hasActiveSession: false,
    activeSession: null,
    activeSince: '',
    todayTotalMinutes: 0,
    elapsedMinutes: 0,
    timerInterval: null,
    logging: false,
    logMessage: '',
    logSuccess: false,
    init() {
        this.checkStatus();
    },
    checkStatus() {
        axios.get('/api/member/log-status').then(res => {
            this.hasActiveSession = res.data.hasActiveSession;
            this.activeSession = res.data.activeSession;
            this.activeSince = res.data.activeSession?.time_in || '';
            this.todayTotalMinutes = res.data.todayTotalMinutes || 0;
            if (this.hasActiveSession) this.startElapsedTimer();
        });
    },
    startElapsedTimer() {
        this.calculateElapsed();
        this.timerInterval = setInterval(() => { this.calculateElapsed(); }, 60000);
    },
    calculateElapsed() {
        if (this.activeSession?.time_in_raw) {
            const timeIn = new Date(this.activeSession.time_in_raw);
            this.elapsedMinutes = Math.floor((new Date() - timeIn) / 60000);
        }
    },
    logTimeIn() {
        this.logging = true;
        this.logMessage = '';
        axios.post('/api/member/time-in').then(res => {
            this.logMessage = res.data.message;
            this.logSuccess = true;
            this.checkStatus();
        }).catch(err => {
            this.logMessage = err.response?.data?.message || 'Failed to log time in.';
            this.logSuccess = false;
        }).finally(() => { this.logging = false; });
    },
    logTimeOut() {
        this.logging = true;
        this.logMessage = '';
        axios.post('/api/member/time-out').then(res => {
            this.logMessage = res.data.message;
            this.logSuccess = true;
            this.checkStatus();
            if (this.timerInterval) { clearInterval(this.timerInterval); this.timerInterval = null; }
            this.elapsedMinutes = 0;
        }).catch(err => {
            this.logMessage = err.response?.data?.message || 'Failed to log time out.';
            this.logSuccess = false;
        }).finally(() => { this.logging = false; });
    },
    fmtDuration(mins) {
        if (!mins || mins === 0) return '0m';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return h > 0 ? `${h}h ${m}m` : `${m}m`;
    }
}));

Alpine.start();
