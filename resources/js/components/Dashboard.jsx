import React, { useState, useMemo, useCallback } from 'react';
import { FilterBar } from './FilterBar';
import { DutySessionCard } from './DutySessionCard';
import { Card, Alert } from './ui';
import {
  RefreshCw, SearchX, Star, Database, Shield, X,
  Clock, ArrowUpRight, TrendingDown, LayoutDashboard,
  Zap, TrendingUp, Users, Target, Award, Activity, Calendar,
  CheckCircle2, AlertCircle, BarChart3, PieChart,
  ChevronLeft, ChevronRight, ChevronDown, ChevronUp,
} from 'lucide-react';
import { format, startOfWeek, endOfWeek, isWithinInterval, subDays, parse } from 'date-fns';
import { motion, AnimatePresence } from 'motion/react';
import { User, DutySession, AttendanceStats } from '../types';
import { calculateVolunteerMetrics } from '../services/metrics';
import { mergeSimilarNameSessions, areNamesSimilar } from '../utils/nameNormalization';
import { HistoryModal } from './Personnel';

// ─── MetricTile (matches Personnel page styling) ────────────────────────────
interface MetricTileProps {
  icon: React.ReactNode;
  label: string;
  value: string;
  sub: string;
  accent?: 'default' | 'blue' | 'rose' | 'emerald';
}

const accentMap = {
  default:  { bg: 'bg-slate-50 border border-slate-200',   border: 'border-slate-200',  label: 'text-slate-600',  icon: 'text-slate-600'  },
  blue:     { bg: 'bg-amber-50', border: 'border-amber-200',   label: 'text-amber-600',   icon: 'text-amber-600'   },
  rose:     { bg: 'bg-red-50', border: 'border-rose-500/10',   label: 'text-red-500',   icon: 'text-red-500'   },
  emerald:  { bg: 'bg-green-50', border: 'border-emerald-500/10',label: 'text-green-600',icon: 'text-green-600'},
} as const;

const MetricTile: React.FC<MetricTileProps> = ({ icon, label, value, sub, accent = 'default' }) => {
  const s = accentMap[accent];
  return (
    <div className={`flex flex-col gap-3 p-4 ${s.bg} border ${s.border} rounded-xl`}>
      <div className="flex items-center justify-between">
        <span className={`text-[10px] font-black uppercase tracking-widest ${s.label}`}>{label}</span>
        <span className={`${s.icon} w-3.5 h-3.5`}>{icon}</span>
      </div>
      <div>
        <p className="text-lg font-black text-slate-900 leading-none">{value}</p>
        <p className="text-[10px] text-slate-600 mt-1 font-semibold">{sub}</p>
      </div>
    </div>
  );
};

// ─── fmtHours helper ───────────────────────────────────────────────────────────
const fmtHours = (mins: number): string => {
  if (mins === 0) return '0h';
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  return m === 0 ? `${h}h` : `${h}h ${m}m`;
};

// ─── Issue types and helpers (matches Personnel page) ──────────────────────
interface IssueDetail {
  date: string;
  type: 'MISSING_TIMEOUT' | 'DUPLICATE' | 'OVERLAP' | 'ZERO_DURATION' | 'FUTURE_DATE' | 'UNKNOWN';
  description: string;
}

const deriveIssues = (session: DutySession): IssueDetail[] => {
  const issues: IssueDetail[] = [];
  if (!session.timeOut) {
    issues.push({
      date: session.date,
      type: 'MISSING_TIMEOUT',
      description: `No time-out recorded on ${session.date}. Session appears still open.`,
    });
  }
  if (session.timeIn && session.timeOut) {
    const inMs  = new Date(session.timeIn).getTime();
    const outMs = new Date(session.timeOut).getTime();
    if (outMs - inMs <= 0) {
      issues.push({
        date: session.date,
        type: 'ZERO_DURATION',
        description: `Time-out is not after time-in on ${session.date}.`,
      });
    }
  }
  const today = new Date();
  if (session.date && new Date(session.date) > today) {
    issues.push({
      date: session.date,
      type: 'FUTURE_DATE',
      description: `Session date ${session.date} is in the future.`,
    });
  }
  return issues;
};

const ISSUE_TYPE_META: Record<IssueDetail['type'], { label: string; color: string; bg: string; border: string }> = {
  MISSING_TIMEOUT:  { label: 'Missing Time-Out',  color: 'text-red-500',   bg: 'bg-red-50',    border: 'border-red-200'   },
  DUPLICATE:        { label: 'Duplicate Entry',   color: 'text-amber-600', bg: 'bg-amber-50',  border: 'border-amber-200' },
  OVERLAP:          { label: 'Overlapping Shift', color: 'text-purple-400',  bg: 'bg-purple-500/10',  border: 'border-purple-500/20' },
  ZERO_DURATION:    { label: 'Zero Duration',     color: 'text-red-400',    bg: 'bg-red-500/10',     border: 'border-red-500/20'    },
  FUTURE_DATE:      { label: 'Future Date',       color: 'text-sky-400',    bg: 'bg-sky-500/10',     border: 'border-sky-500/20'    },
  UNKNOWN:          { label: 'Unknown Issue',     color: 'text-slate-600',  bg: 'bg-slate-500/10',   border: 'border-slate-500/20'  },
};

// ─── Types ────────────────────────────────────────────────────────────────────
interface DashboardProps {
  sessions: DutySession[];
  isLoading: boolean;
  refresh: () => Promise<void>;
  user: User;
}

type SortKey = 'newest' | 'oldest' | 'duration' | 'name';

interface WeeklyMetrics {
  totalHours: string;
  regularHours: string;
  overtime: string;
  undertime: string;
  shiftCount: number;
  completion: number;
}

// ─── Constants ─────────────────────────────────────────────────────────────────
const ANIMATION_STAGGER = {
  hidden: { opacity: 0 },
  show:   { opacity: 1, transition: { staggerChildren: 0.06 } },
};

const ANIMATION_FADE_UP = {
  hidden: { opacity: 0, y: 10 },
  show:   { opacity: 1, y: 0 },
};

// ─── Sub-components ───────────────────────────────────────────────────────────
const FilterChip: React.FC<{ label: string; onClear: () => void }> = ({ label, onClear }) => (
  <div className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-200 backdrop-blur-sm rounded-full text-xs font-semibold transition-colors hover:bg-orange-100 hover:border-orange-300">
    <span>{label}</span>
    <button
      onClick={onClear}
      className="hover:opacity-70 transition-opacity ml-0.5 flex-shrink-0"
      aria-label={`Remove ${label} filter`}
    >
      <X className="w-3 h-3" />
    </button>
  </div>
);

// ─── Metric Card ─────────────────────────────────────────────────────────────
interface MetricCardProps {
  label: string;
  value: string | number;
  trend?: string;
  icon: React.ElementType;
  accentClass: string;
  subtitle?: string;
}

const MetricCard: React.FC<MetricCardProps> = ({
  label, value, trend, icon: Icon, accentClass, subtitle,
}) => (
  <Card variant="default" padding="lg" className="relative overflow-hidden group bg-white shadow-sm border border-orange-200">
    <div className="absolute inset-0 bg-gradient-to-br from-white/[0.03] to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" />
    {/* Watermark */}
    <div className="absolute -right-3 -bottom-3 text-slate-900/5 group-hover:text-slate-900/[0.08] transition-opacity pointer-events-none">
      <Icon className="w-20 h-20" />
    </div>
    {/* Top accent stripe */}
    <div className={`absolute top-0 left-0 right-0 h-0.5 ${accentClass} rounded-t-2xl`} />
    {/* Ambient glow */}
    <div className={`absolute -top-20 -right-20 w-40 h-40 rounded-full blur-3xl opacity-10 ${accentClass}`} />
    <div className="relative">
      <p className="text-xs font-black text-slate-600 uppercase tracking-[0.2em] mb-4">{label}</p>
      <div className="flex items-baseline gap-2 flex-wrap">
        <span className="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">{value}</span>
        {trend && (
          <span className={`inline-flex items-center gap-0.5 text-xs font-bold px-4 py-2 rounded ${trend.startsWith('+')
            ? 'bg-green-50 text-green-600 border border-green-200'
            : 'bg-red-50 text-red-600 border border-red-200'
          }`}>
            {trend.startsWith('+') ? <ArrowUpRight className="w-3 h-3" /> : <TrendingDown className="w-3 h-3" />}
            {trend}
          </span>
        )}
      </div>
      {subtitle && (
        <p className="text-xs font-bold text-slate-600 uppercase tracking-widest mt-4">{subtitle}</p>
      )}
    </div>
  </Card>
);

// ─── Weekly Compliance Bar ────────────────────────────────────────────────────
const WeeklyBar: React.FC<{ pct: number }> = ({ pct }) => {
  const color = pct >= 80 ? 'bg-green-600' : pct >= 50 ? 'bg-blue-600' : 'bg-orange-500';
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <span className="text-xs font-bold text-orange-600 uppercase tracking-widest">Weekly Target</span>
        <span className="text-xs font-black text-orange-600">{pct}%</span>
      </div>
      <div className="h-1.5 bg-slate-200 rounded-full overflow-hidden">
        <motion.div
          className={`h-full ${color} rounded-full`}
          initial={{ width: 0 }}
          animate={{ width: `${pct}%` }}
          transition={{ duration: 1.2, ease: 'easeOut' }}
        />
      </div>
    </div>
  );
};

// ─── Member Hero Section ──────────────────────────────────────────────────
interface MemberHeroProps {
  user: User;
  metrics: WeeklyMetrics;
  sessions: DutySession[];
  onViewHistory: (name: string) => void;
}

const MemberHero: React.FC<MemberHeroProps> = ({ user, metrics, sessions, onViewHistory }) => {
  const totalMinutes = parseFloat(metrics.regularHours) * 60 + parseFloat(metrics.overtime) * 60;
  return (
    <motion.div className="space-y-6" variants={ANIMATION_STAGGER} initial="hidden" animate="show">
      {/* User info header - Logo Inspired */}
      <motion.div variants={ANIMATION_FADE_UP} className="relative overflow-hidden rounded-[3rem]">
        <div className="absolute inset-0 bg-gradient-to-br from-orange-50 to-red-50" />
        <div className="absolute inset-0 nsr-pattern opacity-30" />
        <div className="relative z-10 p-5 sm:p-6">
          <div className="flex flex-col xl:flex-row xl:items-center gap-5 xl:gap-6">
            <div className="flex items-center gap-4 xl:min-w-[300px]">
              <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-600 to-red-600 flex items-center justify-center shadow-lg shadow-orange-600/30">
                <Users className="w-6 h-6 text-white" />
              </div>
              <div className="min-w-0">
                <h3 className="text-base font-black text-slate-900 leading-tight truncate">{user.fullName}</h3>
                <div className="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                  <span className="text-[11px] font-mono text-orange-700 font-bold">#{user.id}</span>
                  <span className="w-1 h-1 bg-orange-300 rounded-full" />
                  <span className="text-[11px] text-slate-600 font-semibold capitalize">{user.role}</span>
                  <span className="w-1 h-1 bg-orange-300 rounded-full" />
                  <span className="text-[11px] text-slate-600">{sessions.length} sessions</span>
                </div>
              </div>
            </div>
            {/* Metrics grid - Logo Inspired */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 flex-1">
              <MetricTile
                icon={<Clock />}
                label="Regular"
                value={fmtHours(parseFloat(metrics.regularHours) * 60)}
                sub="≤ 8h / day"
                accent="default"
              />
              <MetricTile
                icon={<TrendingUp />}
                label="Overtime"
                value={fmtHours(parseFloat(metrics.overtime) * 60)}
                sub="> 8h / day"
                accent="blue"
              />
              <MetricTile
                icon={<TrendingDown />}
                label="Undertime"
                value={fmtHours(parseFloat(metrics.undertime) * 60)}
                sub="<1h"
                accent={parseFloat(metrics.undertime) > 0 ? 'rose' : 'default'}
              />
              {/* History CTA - Logo Inspired */}
              <motion.button
                onClick={() => onViewHistory(user.fullName)}
                whileHover={{ scale: 1.02, y: -2 }}
                whileTap={{ scale: 0.98 }}
                className="bg-white border border-orange-200 hover:border-orange-400 rounded-2xl flex flex-col justify-between transition-all group/btn text-left p-4 shadow-lg hover:shadow-xl"
              >
                <span className="text-[10px] font-black text-orange-700 group-hover/btn:text-orange-800 uppercase tracking-widest transition-colors">History</span>
                <div className="flex items-center justify-between mt-auto pt-2">
                  <span className="text-sm font-black text-slate-900">View</span>
                  <ChevronRight className="w-4 h-4 text-orange-600 group-hover/btn:text-orange-700 group-hover/btn:translate-x-1 transition-all" />
                </div>
              </motion.button>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Hero card - Logo Inspired Design */}
      <motion.div
        variants={ANIMATION_FADE_UP}
        className="relative bg-gradient-to-br from-orange-600 via-red-600 to-blue-600 rounded-[4rem] p-6 sm:p-10 text-white overflow-hidden shadow-2xl shadow-orange-600/30"
      >
        {/* Logo watermark background */}
        <div className="absolute inset-0 opacity-[0.03] flex items-center justify-center pointer-events-none">
          <img src="/nsrc_logo.png" alt="" className="w-96 h-96 object-contain" />
        </div>
        {/* Animated gradient overlay */}
        <motion.div
          className="absolute inset-0 opacity-20"
          animate={{
            background: [
              'radial-gradient(circle at 20% 50%, rgba(249,115,22,0.3) 0%, transparent 50%)',
              'radial-gradient(circle at 80% 50%, rgba(220,38,38,0.3) 0%, transparent 50%)',
              'radial-gradient(circle at 20% 50%, rgba(249,115,22,0.3) 0%, transparent 50%)',
            ]
          }}
          transition={{ duration: 8, repeat: Infinity }}
        />
        <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 items-center">
          {/* Left copy */}
          <div className="lg:col-span-7 space-y-5">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              className="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm border border-orange-300/30 rounded-full text-xs font-black uppercase tracking-[0.2em]"
            >
              <Star className="w-3 h-3 text-orange-300" />
              Personnel Spotlight
            </motion.div>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
            >
              <h2 className="text-2xl sm:text-3xl font-black tracking-tight leading-tight">
                Keep Your<br /><span className="text-orange-300">Service Strong.</span>
              </h2>
              <p className="text-orange-100/80 text-sm font-medium leading-relaxed mt-4 max-w-sm">
                Your total attendance: <span className="font-bold text-white">{metrics.totalHours}h</span> across {metrics.shiftCount} shifts.
              </p>
            </motion.div>
            <WeeklyBar pct={metrics.completion} />
            {/* Stat pills - Logo Colors */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4 }}
              className="flex flex-wrap gap-4 pt-2"
            >
              {[
                { label: 'Current Sector', value: user.sector || 'Unassigned', color: 'from-orange-50/10 to-orange-100/10 border-orange-300/20' },
                { label: 'Status', value: 'Active', valueClass: 'text-green-300', color: 'from-green-50/10 to-green-100/10 border-green-300/20' },
                { label: 'Total Shifts', value: String(metrics.shiftCount), color: 'from-red-50/10 to-red-100/10 border-red-300/20' },
              ].map((pill, i) => (
                <motion.div
                  key={pill.label}
                  whileHover={{ scale: 1.05, y: -2 }}
                  className={`px-5 sm:px-6 py-3 bg-gradient-to-br ${pill.color} backdrop-blur-sm rounded-2xl border`}
                >
                  <p className="text-xs font-black text-orange-200 uppercase tracking-widest mb-1">{pill.label}</p>
                  <p className={`text-sm font-black uppercase ${pill.valueClass ?? 'text-white'}`}>{pill.value}</p>
                </motion.div>
              ))}
            </motion.div>
          </div>
          {/* Right QR panel - Logo Inspired */}
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: 0.3 }}
            className="lg:col-span-5 bg-white/10 backdrop-blur-md border border-amber-300/20 p-5 sm:p-6 rounded-[3rem] relative overflow-hidden"
          >
            {/* Decorative corner accents */}
            {[
              { top: '-8px', left: '-8px' },
              { top: '-8px', right: '-8px' },
              { bottom: '-8px', left: '-8px' },
              { bottom: '-8px', right: '-8px' },
            ].map((pos, i) => (
              <motion.div
                key={i}
                animate={{ scale: [1, 1.3, 1] }}
                transition={{ duration: 2, repeat: Infinity, delay: i * 0.5 }}
                className="absolute w-3 h-3 bg-amber-400 rounded-full shadow-lg shadow-amber-400/50"
                style={pos}
              />
            ))}
            <p className="text-xs font-black text-orange-200 uppercase tracking-[0.2em] mb-6">Your Terminal ID</p>
            <div className="flex flex-col items-center">
              <motion.div
                className="w-36 sm:w-40 h-36 sm:h-40 bg-white/10 rounded-2xl p-4 shadow-2xl mb-6 cursor-pointer relative group border border-orange-300/30"
                onClick={() => window.open('https://docs.google.com/forms/d/e/1FAIpQLSfZ33v8y8FMAfyeWVsGQrr6Q7CaKTY4U-50IhRqVdCZyp7XPQ/viewform?usp=dialog', '_blank')}
                whileHover={{ scale: 1.04 }}
                animate={{ y: [0, -4, 0] }}
                transition={{ duration: 3, repeat: Infinity, ease: 'easeInOut' }}
              >
                <img
                  src="/QR.png"
                  alt="Personal QR"
                  className="w-full h-full object-contain"
                  onError={e => {
                    (e.target as HTMLImageElement).src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=login`;
                  }}
                />
                <div className="absolute inset-0 bg-gradient-to-br from-orange-600/90 to-red-600/90 backdrop-blur-sm rounded-2xl flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-center p-4">
                  <Zap className="w-6 h-6 mb-2" />
                  <p className="text-xs font-black uppercase tracking-wider">Open Login Form</p>
                </div>
              </motion.div>
              <p className="text-center text-xs font-bold text-orange-100/60 uppercase tracking-[0.18em] leading-relaxed">
                Scan or click to access<br />the login form.
              </p>
            </div>
          </motion.div>
        </div>
      </motion.div>
    </motion.div>
  );
};

// ─── Empty & Loading States ───────────────────────────────────────────────────
const LoadingState: React.FC = () => (
  <div className="py-24 flex flex-col items-center text-slate-600">
    <RefreshCw className="w-8 h-8 animate-spin mb-3 opacity-40" />
    <p className="text-xs font-bold uppercase tracking-widest">Loading sessions…</p>
  </div>
);

const EmptyDataState: React.FC<{ onRefresh: () => void }> = ({ onRefresh }) => (
  <div className="py-24 flex flex-col items-center text-center bg-orange-50 border-2 border-dashed border-orange-200 backdrop-blur-sm rounded-2xl">
    <Database className="w-12 h-12 mb-6 text-orange-600" />
    <h4 className="text-base font-black text-slate-900 mb-2 uppercase tracking-tight">No Data</h4>
    <p className="text-xs text-slate-600 max-w-xs mb-8 font-medium">Connect your terminal to start recording sessions.</p>
    <button
      onClick={onRefresh}
      className="px-6 py-2.5 bg-orange-500 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-orange-600 transition-all active:scale-95 shadow-lg shadow-orange-500/20"
    >
      Connect Provider
    </button>
  </div>
);

const EmptyFilterState: React.FC = () => (
  <div className="py-24 flex flex-col items-center text-slate-600">
    <SearchX className="w-12 h-12 mb-3 opacity-20" />
    <p className="text-xs font-black uppercase tracking-[0.25em] text-slate-600 mt-2">No matches found</p>
    <p className="text-xs text-slate-600 mt-2">Try adjusting your search or filters.</p>
  </div>
);

// ─── Enhanced Analytics Components ─────────────────────────────────────────────
interface InsightCardProps {
  title: string;
  value: string | number;
  subtitle?: string;
  icon: React.ElementType;
  trend?: { value: number; direction: 'up' | 'down' };
  color: 'success' | 'warning' | 'danger' | 'info' | 'primary';
}

const InsightCard: React.FC<InsightCardProps> = ({ title, value, subtitle, icon: Icon, trend, color }) => {
  const colorMap = {
    success: 'bg-green-50 text-green-600 border border-green-200',
    warning: 'bg-orange-50 text-orange-600 border border-orange-200',
    danger: 'bg-red-50 text-red-600 border border-red-200',
    info: 'bg-blue-50 text-blue-600 border border-blue-200',
    primary: 'bg-orange-50 text-orange-600 border border-orange-200',
  };
  const glowMap = {
    success: 'bg-green-50',
    warning: 'bg-orange-50',
    danger: 'bg-red-50',
    info: 'bg-blue-50',
    primary: 'bg-orange-50',
  };
  return (
    <Card variant="default" padding="lg" className="relative overflow-hidden bg-white shadow-sm border border-orange-200">
      {/* Ambient glow */}
      <div className={`absolute -top-12 -right-12 w-24 h-24 rounded-full blur-2xl pointer-events-none ${glowMap[color]}`} />
      <div className="relative flex items-start justify-between">
        <div className="flex-1">
          <p className="text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">{title}</p>
          <p className="text-2xl sm:text-3xl font-black text-slate-100 tracking-tight">{value}</p>
          {subtitle && <p className="text-xs text-slate-600 mt-2">{subtitle}</p>}
        </div>
        <div className={`w-12 h-12 rounded-lg flex items-center justify-center shrink-0 ${colorMap[color]}`}>
          <Icon className="w-6 h-6" />
        </div>
      </div>
      {trend && (
        <div className="mt-4 flex items-center gap-2">
          <div className={`flex items-center gap-1 text-xs font-bold px-2 py-1 rounded ${trend.direction === 'up' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500'}`}>
            {trend.direction === 'up' ? <ArrowUpRight className="w-3 h-3" /> : <TrendingDown className="w-3 h-3" />}
            {Math.abs(trend.value)}%
          </div>
          <span className="text-xs text-slate-600">vs last week</span>
        </div>
      )}
    </Card>
  );
};

// ─── Activity Timeline ─────────────────────────────────────────────────────────
interface ActivityTimelineProps {
  sessions: DutySession[];
  limit?: number;
}

const ActivityTimeline: React.FC<ActivityTimelineProps> = ({ sessions, limit = 5 }) => {
  const recentSessions = useMemo(
    () => sessions.sort((a, b) => new Date(b.timeIn).getTime() - new Date(a.timeIn).getTime()).slice(0, limit),
    [sessions, limit]
  );

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'COMPLETE':
        return 'bg-green-50 text-green-600 border border-green-200';
      case 'ONGOING':
        return 'bg-orange-50 text-orange-600 border border-orange-200';
      case 'MISSING_TIMEOUT':
        return 'bg-orange-50 text-orange-600 border border-orange-200';
      default:
        return 'bg-red-50 text-red-600 border border-red-200';
    }
  };

  return (
    <Card variant="default" padding="lg" className="bg-white shadow-sm border border-slate-200">
      <div className="flex items-center gap-2 mb-6">
        <Activity className="w-5 h-5 text-orange-600" />
        <h3 className="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Recent Activity</h3>
      </div>
      <div className="space-y-4">
        {recentSessions.length === 0 ? (
          <p className="text-xs text-slate-600 text-center py-8">No recent activity</p>
        ) : (
          recentSessions.map((session, idx) => (
            <motion.div
              key={`${session.traceId}-${idx}`}
              initial={{ opacity: 0, x: -10 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: idx * 0.05 }}
              className="flex items-center gap-3 pb-4 border-b border-slate-200 last:border-0"
            >
              <div className="w-2 h-2 rounded-full bg-blue-600 shrink-0 shadow-lg shadow-blue-600/30" />
              <div className="flex-1 min-w-0">
                <p className="text-sm font-bold text-slate-900 truncate">{session.fullName}</p>
                <p className="text-xs text-slate-600">
                  {session.timeIn && session.timeIn !== 'N/A'
                    ? format(new Date(session.timeIn), 'MMM dd, HH:mm')
                    : 'Invalid time'}
                </p>
              </div>
              <span className={`text-xs font-bold px-2 py-1 rounded-full shrink-0 ${getStatusColor(session.status)}`}>
                {session.status === 'COMPLETE' ? 'Done' : session.status === 'ONGOING' ? 'Active' : 'Alert'}
              </span>
            </motion.div>
          ))
        )}
      </div>
    </Card>
  );
};

// ─── Attendance Distribution ───────────────────────────────────────────────────
interface AttendanceDistributionProps {
  sessions: DutySession[];
}

const AttendanceDistribution: React.FC<AttendanceDistributionProps> = ({ sessions }) => {
  const distribution = useMemo(() => {
    const stats = {
      complete: sessions.filter(s => s.status === 'COMPLETE').length,
      ongoing: sessions.filter(s => s.status === 'ONGOING').length,
      missing: sessions.filter(s => s.status === 'MISSING_TIMEOUT').length,
      invalid: sessions.filter(s => s.status === 'INVALID_LOG').length,
    };
    const total = sessions.length || 1;
    return {
      complete: Math.round((stats.complete / total) * 100),
      ongoing: Math.round((stats.ongoing / total) * 100),
      missing: Math.round((stats.missing / total) * 100),
      invalid: Math.round((stats.invalid / total) * 100),
      ...stats,
    };
  }, [sessions]);

  return (
    <Card variant="default" padding="lg" className="bg-white shadow-sm border border-orange-200">
      <div className="flex items-center gap-2 mb-6">
        <PieChart className="w-5 h-5 text-orange-600" />
        <h3 className="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Status Distribution</h3>
      </div>
      <div className="space-y-4">
        {[
          { label: 'Completed', value: distribution.complete, color: 'bg-green-600', count: distribution.complete },
          { label: 'Ongoing', value: distribution.ongoing, color: 'bg-blue-600', count: distribution.ongoing },
          { label: 'Missing Timeout', value: distribution.missing, color: 'bg-orange-500', count: distribution.missing },
          { label: 'Invalid', value: distribution.invalid, color: 'bg-red-600', count: distribution.invalid },
        ].map((item) => (
          <div key={item.label}>
            <div className="flex items-center justify-between mb-2">
              <span className="text-xs font-bold text-slate-600">{item.label}</span>
              <span className="text-xs font-black text-slate-900">{item.value}%</span>
            </div>
            <div className="h-2 bg-slate-200 rounded-full overflow-hidden">
              <motion.div
                className={`h-full ${item.color} rounded-full`}
                initial={{ width: 0 }}
                animate={{ width: `${item.value}%` }}
                transition={{ duration: 0.8, ease: 'easeOut' }}
              />
            </div>
          </div>
        ))}
      </div>
    </Card>
  );
};

// ─── Top Performers ───────────────────────────────────────────────────────────
interface TopPerformersProps {
  sessions: DutySession[];
  limit?: number;
}

const TopPerformers: React.FC<TopPerformersProps> = ({ sessions, limit = 5 }) => {
  const performers = useMemo(() => {
    const grouped = sessions.reduce((acc, session) => {
      if (!acc[session.fullName]) {
        acc[session.fullName] = { name: session.fullName, hours: 0, sessions: 0 };
      }
      acc[session.fullName].hours += (session.durationMinutes || 0) / 60;
      acc[session.fullName].sessions += 1;
      return acc;
    }, {} as Record<string, { name: string; hours: number; sessions: number }>);
    return Object.values(grouped).sort((a, b) => b.hours - a.hours).slice(0, limit);
  }, [sessions, limit]);

  return (
    <Card variant="default" padding="lg" className="bg-white shadow-sm border border-orange-200">
      <div className="flex items-center gap-2 mb-6">
        <Award className="w-5 h-5 text-orange-600" />
        <h3 className="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Top Performers</h3>
      </div>
      <div className="space-y-3">
        {performers.length === 0 ? (
          <p className="text-xs text-slate-600 text-center py-8">No data available</p>
        ) : (
          performers.map((performer, idx) => (
            <motion.div
              key={performer.name}
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: idx * 0.05 }}
              className="flex items-center gap-3"
            >
              <div
                className={`flex items-center justify-center w-8 h-8 rounded-full text-xs font-black ${
                  idx === 0
                    ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30'
                    : idx === 1
                    ? 'bg-slate-700 text-slate-200'
                    : idx === 2
                    ? 'bg-slate-800 text-slate-300'
                    : 'bg-white/5 text-slate-600'
                }`}
              >
                {idx + 1}
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-sm font-bold text-slate-900 truncate">{performer.name}</p>
                <p className="text-xs text-slate-600">{performer.sessions} sessions</p>
              </div>
              <div className="text-right shrink-0">
                <p className="text-sm font-black text-slate-900">{performer.hours.toFixed(1)}h</p>
              </div>
            </motion.div>
          ))
        )}
      </div>
    </Card>
  );
};

// ─── Main Component ───────────────────────────────────────────────────────────
export const Dashboard: React.FC<DashboardProps> = ({ sessions, isLoading, refresh, user }) => {
  const isAdmin = user.role === 'admin';
  const [selectedName, setSelectedName] = useState<string | null>(null);

  // ── Filter state
  const [search, setSearch]               = useState('');
  const [startDate, setStartDate]         = useState('');
  const [endDate, setEndDate]             = useState('');
  const [statusFilter, setStatusFilter]   = useState('All');
  const [locationFilter, setLocationFilter] = useState('');
  const [sortBy, setSortBy]               = useState<SortKey>('newest');
  const [currentPage, setCurrentPage]     = useState(1);

  const ITEMS_PER_PAGE = 10;

  const clearAllFilters = useCallback(() => {
    setSearch('');
    setStartDate('');
    setEndDate('');
    setStatusFilter('All');
    setLocationFilter('');
    setSortBy('newest');
    setCurrentPage(1);
  }, []);

  const handleSortChange = useCallback((val: string) => {
    setSortBy(val as SortKey);
    setCurrentPage(1);
  }, []);

  const handleSearchChange = useCallback((val: string) => {
    setSearch(val);
    setCurrentPage(1);
  }, []);

  const handleStartDateChange = useCallback((val: string) => {
    setStartDate(val);
    setCurrentPage(1);
  }, []);

  const handleEndDateChange = useCallback((val: string) => {
    setEndDate(val);
    setCurrentPage(1);
  }, []);

  const handleStatusChange = useCallback((val: string) => {
    setStatusFilter(val);
    setCurrentPage(1);
  }, []);

  const handleLocationChange = useCallback((val: string) => {
    setLocationFilter(val);
    setCurrentPage(1);
  }, []);

  const hasFilters = Boolean(search || startDate || endDate || locationFilter || statusFilter !== 'All');

  // ── Member-scoped sessions
  const memberSessions = useMemo(() => {
    // First merge similar names in all sessions
    const mergedSessions = mergeSimilarNameSessions(sessions);
    console.log('[Dashboard] All sessions after merge:', {
      count: mergedSessions.length,
      uniqueNames: [...new Set(mergedSessions.map(s => s.fullName))],
    });

    // Then filter for this member using fuzzy matching
    const filtered = mergedSessions.filter(s => {
      const isExactMatch = s.fullName === user.fullName;
      const isFuzzyMatch = areNamesSimilar(s.fullName, user.fullName, 0.85);
      const matches = isExactMatch || isFuzzyMatch;
      if (matches) {
        console.log('[Dashboard] Session matched:', {
          sessionName: s.fullName,
          userName: user.fullName,
          isExactMatch,
          isFuzzyMatch,
        });
      }
      return matches;
    });

    console.log('[Dashboard] Member sessions filtered:', {
      userFullName: user.fullName,
      matchedCount: filtered.length,
      totalAvailable: mergedSessions.length,
    });

    if (filtered.length === 0 && sessions.length > 0) {
      console.warn(`[Dashboard] No sessions matched for user "${user.fullName}". Available names:`,
        [...new Set(sessions.map(s => s.fullName))]);
    }

    return filtered;
  }, [sessions, user.fullName]);

  // ── Weekly metrics
  const weeklyMetrics = useMemo<WeeklyMetrics>(() => {
    // Calculate ALL-TIME metrics for the member (not just weekly)
    const volunteerMetrics = calculateVolunteerMetrics(memberSessions);
    const userMetrics = volunteerMetrics.find(m => m.fullName === user.fullName);

    if (userMetrics) {
      const regHrs = userMetrics.totalRegularMinutes / 60;
      const otHrs = userMetrics.totalOvertimeMinutes / 60;
      const utHrs = userMetrics.totalUndertimeMinutes / 60;
      const totalHrs = regHrs + otHrs;

      return {
        totalHours:  totalHrs.toFixed(1),
        regularHours: regHrs.toFixed(1),
        overtime:    otHrs.toFixed(1),
        undertime:   utHrs.toFixed(1),
        shiftCount:  memberSessions.length,
        completion:  Math.min(100, Math.round((totalHrs / 40) * 100)),
      };
    }

    // Fallback: calculate manually if no metrics found
    const dayMap: Record<string, number> = {};
    memberSessions.forEach(s => {
      if (s.durationMinutes !== null) {
        dayMap[s.date] = (dayMap[s.date] || 0) + s.durationMinutes;
      }
    });

    const STANDARD_MINUTES = 480;
    const MIN_SESSION_MINUTES = 60;
    let totalReg = 0;
    let totalOT = 0;
    let totalUT = 0;
    let totalMins = 0;

    Object.values(dayMap).forEach(minutes => {
      totalMins += minutes;
      if (minutes < MIN_SESSION_MINUTES) {
        totalUT += minutes;
      } else {
        totalReg += Math.min(minutes, STANDARD_MINUTES);
        totalOT += Math.max(0, minutes - STANDARD_MINUTES);
      }
    });

    const regHrs = totalReg / 60;
    const otHrs = totalOT / 60;
    const utHrs = totalUT / 60;
    const totalHrs = totalMins / 60;

    return {
      totalHours:  totalHrs.toFixed(1),
      regularHours: regHrs.toFixed(1),
      overtime:    otHrs.toFixed(1),
      undertime:   utHrs.toFixed(1),
      shiftCount:  memberSessions.length,
      completion:  Math.min(100, Math.round((totalHrs / 40) * 100)),
    };
  }, [memberSessions, user.fullName]);

  // ── Filtered + sorted sessions
  const filteredSessions = useMemo<DutySession[]>(() => {
    let pool = isAdmin ? [...sessions] : [...memberSessions];

    if (search) {
      const q = search.toLowerCase();
      pool = pool.filter(s =>
        s.fullName.toLowerCase().includes(q) ||
        (s.traceId ?? '').toLowerCase().includes(q),
      );
    }

    if (startDate)          pool = pool.filter(s => s.date >= startDate);
    if (endDate)            pool = pool.filter(s => s.date <= endDate);
    if (statusFilter !== 'All') pool = pool.filter(s => s.status === statusFilter);
    if (locationFilter) {
      const q = locationFilter.toLowerCase();
      pool = pool.filter(s =>
        (s.location ?? '').toLowerCase().includes(q) ||
        (s.sector    ?? '').toLowerCase().includes(q),
      );
    }

    return pool.sort((a, b) => {
      switch (sortBy) {
        case 'newest':   return new Date(b.timeIn).getTime() - new Date(a.timeIn).getTime();
        case 'oldest':   return new Date(a.timeIn).getTime() - new Date(b.timeIn).getTime();
        case 'duration': return (b.durationMinutes ?? 0) - (a.durationMinutes ?? 0);
        case 'name':     return a.fullName.localeCompare(b.fullName);
        default:         return 0;
      }
    });
  }, [search, startDate, endDate, statusFilter, locationFilter, sortBy, sessions, isAdmin, memberSessions]);

  // ── Attendance stats for admin
  const stats: AttendanceStats = useMemo(() => ({
    totalRecords:    sessions.length,
    todayCount:      sessions.filter(s => s.date === format(new Date(), 'yyyy-MM-dd')).length,
    activeNow:       sessions.filter(s => s.status === 'ONGOING').length,
    missingTimeouts: sessions.filter(s => s.status === 'MISSING_TIMEOUT').length,
    avgDuration:     '4.2h',
  }), [sessions]);

  return (
    <div className="space-y-8 sm:space-y-10 max-w-7xl mx-auto px-1 relative">
      {/* Background Pattern */}
      <div className="absolute inset-0 nsr-pattern opacity-20 pointer-events-none" />
      <div className="relative z-10">
        {/* ── Page Header - Premium Hero ───────────────────────────────────── */}
        <motion.div
          initial={{ opacity: 0, y: -30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-orange-50 to-red-50 border border-orange-200 p-8 sm:p-10 shadow-lg"
        >
          {/* Background pattern */}
          <div className="absolute inset-0 nsr-pattern opacity-20 pointer-events-none" />
          {/* Animated gradient overlay */}
          <motion.div
            className="absolute inset-0 opacity-30"
            animate={{
              background: [
                'radial-gradient(circle at 20% 50%, rgba(5,150,105,0.2) 0%, transparent 50%)',
                'radial-gradient(circle at 80% 50%, rgba(245,158,11,0.2) 0%, transparent 50%)',
                'radial-gradient(circle at 20% 50%, rgba(5,150,105,0.2) 0%, transparent 50%)',
              ]
            }}
            transition={{ duration: 8, repeat: Infinity }}
          />
          <div className="relative z-10 flex items-start justify-between gap-6">
            <div className="flex-1">
              <div className="flex items-center gap-4 mb-3">
                <motion.div
                  className="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-600 to-red-600 flex items-center justify-center shadow-lg shadow-orange-600/30 shrink-0"
                  animate={{ scale: [1, 1.05, 1] }}
                  transition={{ duration: 3, repeat: Infinity }}
                >
                  <LayoutDashboard className="w-6 h-6 text-white" />
                </motion.div>
                <div>
                  <h1 className="text-3xl sm:text-4xl font-black tracking-tight text-slate-900">Dashboard</h1>
                  <p className="text-sm text-slate-600 font-bold mt-1">Real-time attendance overview</p>
                </div>
              </div>
              <p className="text-base text-slate-700 font-medium ml-16">
                Welcome back, <span className="text-green-700 font-black text-lg">{user.fullName}</span>
              </p>
            </div>
            <motion.button
              onClick={refresh}
              disabled={isLoading}
              whileHover={{ scale: 1.08, rotate: 180 }}
              whileTap={{ scale: 0.95 }}
              className="inline-flex items-center gap-2 px-6 sm:px-7 py-3 bg-gradient-to-r from-green-700 to-green-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:from-green-800 hover:to-green-700 transition-all disabled:opacity-50 shadow-xl shadow-green-700/30 active:scale-95 shrink-0 relative overflow-hidden group"
              aria-label="Refresh sessions"
            >
              <div className="absolute inset-0 bg-gradient-to-r from-amber-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
              <RefreshCw className={`w-4 h-4 relative ${isLoading ? 'animate-spin' : 'group-hover:rotate-180 transition-transform'}`} />
              <span className="hidden sm:inline relative">{isLoading ? 'Syncing…' : 'Resync'}</span>
            </motion.button>
          </div>
        </motion.div>

        {/* ── Member hero ───────────────────────────────────────────────────── */}
        {!isAdmin && (
          <div className="pt-8 sm:pt-12">
            <MemberHero user={user} metrics={weeklyMetrics} sessions={memberSessions} onViewHistory={setSelectedName} />
          </div>
        )}

        {/* ── Enhanced Analytics Section ────────────────────────────────────── */}
        {!isLoading && sessions.length > 0 && (
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
            className="space-y-6 pt-8 sm:pt-12"
          >
            {/* Quick Insights - Logo Inspired */}
            <div>
              <motion.div
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                className="flex items-center gap-3 mb-6"
              >
                <div className="w-10 h-10 bg-gradient-to-br from-green-100 to-amber-100 rounded-xl flex items-center justify-center shadow-sm">
                  <BarChart3 className="w-5 h-5 text-green-700" />
                </div>
                <h2 className="text-lg font-black uppercase tracking-tight">
                  <span className="bg-gradient-to-r from-green-700 to-amber-600 bg-clip-text text-transparent">Quick Insights</span>
                </h2>
              </motion.div>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {[
                  {
                    title: 'Total Sessions',
                    value: sessions.length,
                    icon: Calendar,
                    color: 'from-green-50 to-green-100',
                    iconBg: 'bg-green-100',
                    iconColor: 'text-green-600',
                    trend: { value: 12, direction: 'up' },
                    subtitle: 'All time'
                  },
                  {
                    title: 'Completed',
                    value: sessions.filter(s => s.status === 'COMPLETE').length,
                    icon: CheckCircle2,
                    color: 'from-emerald-50 to-emerald-100',
                    iconBg: 'bg-emerald-100',
                    iconColor: 'text-emerald-600',
                    trend: { value: 8, direction: 'up' },
                    subtitle: 'Successful'
                  },
                  {
                    title: 'Active Now',
                    value: sessions.filter(s => s.status === 'ONGOING').length,
                    icon: Activity,
                    color: 'from-blue-50 to-blue-100',
                    iconBg: 'bg-blue-100',
                    iconColor: 'text-blue-600',
                    subtitle: 'In progress'
                  },
                  {
                    title: 'Issues',
                    value: sessions.filter(s => s.status === 'MISSING_TIMEOUT' || s.status === 'INVALID_LOG').length,
                    icon: AlertCircle,
                    color: 'from-amber-50 to-amber-100',
                    iconBg: 'bg-amber-100',
                    iconColor: 'text-amber-600',
                    trend: { value: 3, direction: 'down' },
                    subtitle: 'Needs review'
                  },
                ].map((card, i) => (
                  <motion.div
                    key={i}
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: i * 0.1 }}
                    whileHover={{ y: -4, scale: 1.02 }}
                  >
                    <div className={`bg-gradient-to-br ${card.color} p-6 rounded-[3rem] border border-slate-200 shadow-lg hover:shadow-xl transition-all relative overflow-hidden group`}>
                      {/* Logo watermark */}
                      <div className="absolute -right-6 -bottom-6 opacity-[0.04] group-hover:opacity-[0.08] transition-opacity">
                        <img src="/nsrc_logo.png" alt="" className="w-24 h-24 object-contain" />
                      </div>
                      {/* Animated accent line */}
                      <motion.div
                        className="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-green-600 to-amber-500"
                        initial={{ scaleX: 0 }}
                        whileHover={{ scaleX: 1 }}
                        transition={{ duration: 0.3 }}
                        style={{ originX: 0 }}
                      />
                      <div className="flex items-start justify-between mb-4 relative z-10">
                        <div>
                          <p className="text-xs font-bold text-slate-600 uppercase tracking-widest mb-2">{card.title}</p>
                          <p className="text-2xl font-black text-slate-900">{card.value}</p>
                          <p className="text-[10px] text-slate-500 font-semibold mt-1">{card.subtitle}</p>
                        </div>
                        <div className={`w-12 h-12 ${card.iconBg} rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform`}>
                          <card.icon className={`w-6 h-6 ${card.iconColor}`} />
                        </div>
                      </div>
                      {card.trend && (
                        <div className="flex items-center gap-2 relative z-10">
                          <div className={`flex items-center gap-1 text-xs font-bold px-2 py-1 rounded ${card.trend.direction === 'up' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500'}`}>
                            {card.trend.direction === 'up' ? <ArrowUpRight className="w-3 h-3" /> : <TrendingDown className="w-3 h-3" />}
                            {card.trend.value}%
                          </div>
                          <span className="text-xs text-slate-600">vs last week</span>
                        </div>
                      )}
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>

            {/* Analytics Grid - Logo Inspired */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <div className="lg:col-span-2">
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.2 }}
                >
                  <ActivityTimeline sessions={sessions} limit={8} />
                </motion.div>
              </div>
              <div>
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.3 }}
                >
                  <AttendanceDistribution sessions={sessions} />
                </motion.div>
              </div>
            </div>

            {/* Enhanced Performance Metrics */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              {[
                {
                  title: 'Avg Session Duration',
                  value: sessions.length > 0 ? fmtHours(Math.round(sessions.reduce((sum, s) => sum + (s.durationMinutes || 0), 0) / sessions.length)) : '0h',
                  icon: Clock,
                  color: 'from-blue-50 to-blue-100',
                  iconBg: 'bg-blue-100',
                  iconColor: 'text-blue-600',
                  subtitle: 'Per session'
                },
                {
                  title: 'Completion Rate',
                  value: sessions.length > 0 ? `${Math.round((sessions.filter(s => s.status === 'COMPLETE').length / sessions.length) * 100)}%` : '0%',
                  icon: CheckCircle2,
                  color: 'from-emerald-50 to-emerald-100',
                  iconBg: 'bg-emerald-100',
                  iconColor: 'text-emerald-600',
                  subtitle: 'Success rate'
                },
                {
                  title: 'Total Hours',
                  value: fmtHours(sessions.reduce((sum, s) => sum + (s.durationMinutes || 0), 0)),
                  icon: Clock,
                  color: 'from-amber-50 to-amber-100',
                  iconBg: 'bg-amber-100',
                  iconColor: 'text-amber-600',
                  subtitle: 'Cumulative'
                },
                {
                  title: 'Avg Daily Hours',
                  value: sessions.length > 0 ? fmtHours(Math.round(sessions.reduce((sum, s) => sum + (s.durationMinutes || 0), 0) / [...new Set(sessions.map(s => s.date))].length)) : '0h',
                  icon: BarChart3,
                  color: 'from-purple-50 to-purple-100',
                  iconBg: 'bg-purple-100',
                  iconColor: 'text-purple-600',
                  subtitle: 'Per day average'
                },
              ].map((metric, i) => (
                <motion.div
                  key={i}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.35 + i * 0.08 }}
                  whileHover={{ y: -2 }}
                >
                  <Card variant="default" padding="lg" className={`bg-gradient-to-br ${metric.color} border border-slate-200 shadow-sm hover:shadow-md transition-all relative overflow-hidden group`}>
                    <div className="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity">
                      <metric.icon className="w-16 h-16" />
                    </div>
                    <div className="relative z-10">
                      <div className="flex items-start justify-between mb-3">
                        <p className="text-xs font-bold text-slate-600 uppercase tracking-widest">{metric.title}</p>
                        <div className={`w-10 h-10 ${metric.iconBg} rounded-lg flex items-center justify-center`}>
                          <metric.icon className={`w-5 h-5 ${metric.iconColor}`} />
                        </div>
                      </div>
                      <p className="text-2xl font-black text-slate-900">{metric.value}</p>
                      <p className="text-[10px] text-slate-500 font-semibold mt-2">{metric.subtitle}</p>
                    </div>
                  </Card>
                </motion.div>
              ))}
            </div>

            {/* Performance Section - Logo Inspired */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <motion.div
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ delay: 0.4 }}
              >
                <TopPerformers sessions={sessions} limit={10} />
              </motion.div>
              <motion.div
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ delay: 0.5 }}
              >
                <Card variant="default" padding="lg" className="relative overflow-hidden bg-white shadow-sm border border-slate-200">
                  <div className="absolute inset-0 nsr-pattern opacity-30" />
                  <div className="absolute top-4 right-4 opacity-[0.03]">
                    <img src="/nsrc_logo.png" alt="" className="w-20 h-20 object-contain" />
                  </div>
                  <div className="relative z-10">
                    <div className="flex items-center gap-3 mb-6">
                      <div className="w-10 h-10 bg-gradient-to-br from-green-100 to-amber-100 rounded-xl flex items-center justify-center">
                        <Target className="w-5 h-5 text-green-700" />
                      </div>
                      <h3 className="text-xs font-black uppercase tracking-[0.2em]">
                        <span className="bg-gradient-to-r from-green-700 to-amber-600 bg-clip-text text-transparent">Attendance Goals</span>
                      </h3>
                    </div>
                    <div className="space-y-4">
                      {[
                        { label: 'Weekly Target', current: 32, target: 40, unit: 'hours', color: 'from-green-500 to-green-600' },
                        { label: 'Monthly Target', current: 128, target: 160, unit: 'hours', color: 'from-amber-500 to-amber-600' },
                        { label: 'Completion Rate', current: 80, target: 100, unit: '%', color: 'from-emerald-500 to-emerald-600' },
                      ].map((goal, i) => (
                        <motion.div
                          key={goal.label}
                          initial={{ opacity: 0, y: 10 }}
                          animate={{ opacity: 1, y: 0 }}
                          transition={{ delay: 0.6 + i * 0.1 }}
                        >
                          <div className="flex items-center justify-between mb-2">
                            <span className="text-xs font-bold text-slate-600">{goal.label}</span>
                            <span className="text-xs font-black text-slate-900">{goal.current}/{goal.target} {goal.unit}</span>
                          </div>
                          <div className="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <motion.div
                              className={`h-full bg-gradient-to-r ${goal.color} rounded-full`}
                              initial={{ width: 0 }}
                              animate={{ width: `${Math.min((goal.current / goal.target) * 100, 100)}%` }}
                              transition={{ duration: 0.8, ease: 'easeOut' }}
                            />
                          </div>
                        </motion.div>
                      ))}
                    </div>
                  </div>
                </Card>
              </motion.div>
            </div>

            {/* Status Overview Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {[
                {
                  title: 'Session Status',
                  items: [
                    { label: 'Completed', value: sessions.filter(s => s.status === 'COMPLETE').length, color: 'text-emerald-600', bg: 'bg-emerald-50' },
                    { label: 'Ongoing', value: sessions.filter(s => s.status === 'ONGOING').length, color: 'text-blue-600', bg: 'bg-blue-50' },
                    { label: 'Pending', value: sessions.filter(s => s.status === 'MISSING_TIMEOUT').length, color: 'text-amber-600', bg: 'bg-amber-50' },
                  ]
                },
                {
                  title: 'Time Distribution',
                  items: [
                    { label: 'Regular Hours', value: fmtHours(sessions.filter(s => (s.durationMinutes || 0) <= 480).reduce((sum, s) => sum + (s.durationMinutes || 0), 0)), color: 'text-green-600', bg: 'bg-green-50' },
                    { label: 'Overtime', value: fmtHours(sessions.filter(s => (s.durationMinutes || 0) > 480).reduce((sum, s) => sum + ((s.durationMinutes || 0) - 480), 0)), color: 'text-purple-600', bg: 'bg-purple-50' },
                    { label: 'Undertime', value: fmtHours(sessions.filter(s => (s.durationMinutes || 0) < 60).reduce((sum, s) => sum + (s.durationMinutes || 0), 0)), color: 'text-red-600', bg: 'bg-red-50' },
                  ]
                },
                {
                  title: 'Data Quality',
                  items: [
                    { label: 'Valid Records', value: sessions.filter(s => s.status !== 'INVALID_LOG').length, color: 'text-green-600', bg: 'bg-green-50' },
                    { label: 'Issues Found', value: sessions.filter(s => s.status === 'INVALID_LOG' || s.status === 'MISSING_TIMEOUT').length, color: 'text-red-600', bg: 'bg-red-50' },
                    { label: 'Accuracy', value: `${Math.round((sessions.filter(s => s.status !== 'INVALID_LOG').length / Math.max(sessions.length, 1)) * 100)}%`, color: 'text-blue-600', bg: 'bg-blue-50' },
                  ]
                },
              ].map((section, sectionIdx) => (
                <motion.div
                  key={sectionIdx}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.65 + sectionIdx * 0.1 }}
                >
                  <Card variant="default" padding="lg" className="bg-white shadow-sm border border-slate-200 relative overflow-hidden">
                    <div className="absolute -right-4 -top-4 opacity-[0.02]">
                      <BarChart3 className="w-20 h-20" />
                    </div>
                    <h3 className="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-4">{section.title}</h3>
                    <div className="space-y-3 relative z-10">
                      {section.items.map((item, i) => (
                        <motion.div
                          key={i}
                          initial={{ opacity: 0, x: -10 }}
                          animate={{ opacity: 1, x: 0 }}
                          transition={{ delay: 0.7 + sectionIdx * 0.1 + i * 0.05 }}
                          className="flex items-center justify-between p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors"
                        >
                          <span className="text-xs font-bold text-slate-600">{item.label}</span>
                          <span className={`text-sm font-black ${item.color}`}>{item.value}</span>
                        </motion.div>
                      ))}
                    </div>
                  </Card>
                </motion.div>
              ))}
            </div>
          </motion.div>
        )}

        {/* ── Empty state for members with no sessions ────────────────────── */}
        {!isAdmin && !isLoading && sessions.length === 0 && (
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
            className="relative rounded-[4rem] p-12 shadow-xl overflow-hidden"
          >
            <div className="absolute inset-0 bg-gradient-to-br from-green-50 to-amber-50" />
            <div className="absolute inset-0 nsr-pattern opacity-30" />
            <div className="relative z-10 text-center">
              <motion.div
                initial={{ scale: 0.8 }}
                animate={{ scale: 1 }}
                className="w-20 h-20 bg-gradient-to-br from-green-100 to-amber-100 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg"
              >
                <Calendar className="w-10 h-10 text-green-700" />
              </motion.div>
              <h3 className="text-lg font-black mb-2">
                <span className="bg-gradient-to-r from-green-700 to-amber-600 bg-clip-text text-transparent">No Duty Sessions Yet</span>
              </h3>
              <p className="text-slate-600 font-medium mb-6 max-w-md mx-auto">
                Your duty sessions will appear here once you start recording attendance. Check in and out to begin tracking your volunteer hours.
              </p>
              <p className="text-xs text-slate-600 font-bold uppercase tracking-widest">
                Your metrics will update automatically as you complete duty sessions.
              </p>
            </div>
          </motion.div>
        )}

        {/* ── Missing timeout alert (admin only) ───────────────────────────── */}
        {isAdmin && stats.missingTimeouts > 0 && (
          <Alert
            type="warning"
            title={`${stats.missingTimeouts} Missing Time-Out${stats.missingTimeouts > 1 ? 's' : ''} Detected`}
            description="These sessions have no recorded time-out and may need manual review."
          />
        )}

        {/* ── Main Grid ─────────────────────────────────────────────────────── */}
        <div className="grid grid-cols-1 gap-4 sm:gap-6 lg:gap-8">
          {/* ── Duty Logs ─────────────────────────────────────────────────── */}
          <div>
            <Card variant="default" padding="lg" className="bg-white shadow-sm border border-slate-200">
              {/* Log header */}
              <div className="flex items-center justify-between gap-4 mb-6">
                <div className="flex items-center gap-2.5">
                  <div className="w-1 h-5 bg-blue-500 rounded-full shrink-0 shadow-lg shadow-blue-500/30" />
                  <h3 className="text-xs font-black text-slate-100 uppercase tracking-[0.2em]">Duty Logs</h3>
                  <span className="text-xs font-bold text-slate-600">({filteredSessions.length}{hasFilters ? ' filtered' : ''})</span>
                </div>
                <div className="flex items-center gap-1.5">
                  <span className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/30" />
                  <span className="text-xs font-bold text-slate-600 uppercase tracking-widest">Live</span>
                </div>
              </div>

              {/* Filter bar */}
              <FilterBar
                search={search}
                onSearchChange={handleSearchChange}
                startDate={startDate}
                onStartDateChange={handleStartDateChange}
                endDate={endDate}
                onEndDateChange={handleEndDateChange}
                statusFilter={statusFilter}
                onStatusChange={handleStatusChange}
                locationFilter={locationFilter}
                onLocationChange={handleLocationChange}
                sortBy={sortBy}
                onSortChange={handleSortChange}
                onClear={clearAllFilters}
              />

              {/* Active filter chips */}
              <AnimatePresence>
                {hasFilters && (
                  <motion.div
                    initial={{ opacity: 0, y: -6 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -6 }}
                    className="mt-4 flex flex-wrap gap-2"
                  >
                    {search         && <FilterChip label={`Name: ${search}`}            onClear={() => setSearch('')}               />}
                    {statusFilter !== 'All' && <FilterChip label={`Status: ${statusFilter}`} onClear={() => setStatusFilter('All')} />}
                    {locationFilter && <FilterChip label={`Sector: ${locationFilter}`}  onClear={() => setLocationFilter('')}       />}
                    {startDate      && <FilterChip label={`From: ${startDate}`}         onClear={() => setStartDate('')}            />}
                    {endDate        && <FilterChip label={`To: ${endDate}`}             onClear={() => setEndDate('')}              />}
                    <button
                      onClick={clearAllFilters}
                      className="text-xs font-black text-slate-600 hover:text-slate-100 uppercase tracking-wider underline underline-offset-2 transition-colors"
                    >
                      Clear all
                    </button>
                  </motion.div>
                )}
              </AnimatePresence>

              {/* Session list */}
              <div className="mt-6">
                {isLoading ? (
                  <LoadingState />
                ) : sessions.length === 0 ? (
                  <EmptyDataState onRefresh={refresh} />
                ) : filteredSessions.length === 0 ? (
                  <EmptyFilterState />
                ) : (
                  <>
                    <motion.div
                      variants={ANIMATION_STAGGER}
                      initial="hidden"
                      animate="show"
                      className="space-y-3"
                    >
                      {filteredSessions.slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE).map((session, idx) => (
                        <motion.div key={`${session.traceId}-${idx}`} variants={ANIMATION_FADE_UP}>
                          <DutySessionCard
                            session={session}
                            index={idx}
                            onUpdated={refresh}
                            onFilterSector={setLocationFilter}
                          />
                        </motion.div>
                      ))}
                    </motion.div>

                    {/* Pagination */}
                    {(() => {
                      const totalPages = Math.ceil(filteredSessions.length / ITEMS_PER_PAGE);
                      if (totalPages <= 1) return null;

                      const startIdx = (currentPage - 1) * ITEMS_PER_PAGE + 1;
                      const endIdx = Math.min(currentPage * ITEMS_PER_PAGE, filteredSessions.length);

                      const getPageNumbers = () => {
                        const pages: (number | string)[] = [];
                        const maxVisible = 7;

                        if (totalPages <= maxVisible) {
                          for (let i = 1; i <= totalPages; i++) pages.push(i);
                        } else {
                          pages.push(1);
                          if (currentPage > 3) pages.push('...');
                          const start = Math.max(2, currentPage - 1);
                          const end = Math.min(totalPages - 1, currentPage + 1);
                          for (let i = start; i <= end; i++) pages.push(i);
                          if (currentPage < totalPages - 2) pages.push('...');
                          pages.push(totalPages);
                        }
                        return pages;
                      };

                      return (
                        <div className="mt-8 flex items-center justify-between px-4 py-3 bg-white/[0.03] border border-slate-200 rounded-xl">
                          <div className="text-xs font-bold text-slate-600 uppercase tracking-widest">
                            Showing {startIdx}–{endIdx} of {filteredSessions.length}
                          </div>
                          <div className="flex items-center gap-1.5">
                            <button
                              onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                              disabled={currentPage === 1}
                              className="p-2 rounded-lg border border-slate-200 bg-white/[0.03] hover:bg-white/[0.06] disabled:opacity-30 disabled:cursor-not-allowed transition-all text-slate-600 hover:text-slate-200"
                            >
                              <ChevronLeft className="w-4 h-4" />
                            </button>
                            {getPageNumbers().map((page, i) =>
                              typeof page === 'string' ? (
                                <span key={`ellipsis-${i}`} className="px-2 text-slate-600 text-xs">
                                  {page}
                                </span>
                              ) : (
                                <button
                                  key={page}
                                  onClick={() => setCurrentPage(page)}
                                  className={`w-8 h-8 rounded-lg text-xs font-black transition-all ${
                                    currentPage === page
                                      ? 'bg-blue-500/20 text-amber-600 border border-blue-500/30'
                                      : 'bg-white/[0.03] border border-slate-200 text-slate-600 hover:bg-white/[0.06] hover:text-slate-300'
                                  }`}
                                >
                                  {page}
                                </button>
                              )
                            )}
                            <button
                              onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                              disabled={currentPage === totalPages}
                              className="p-2 rounded-lg border border-slate-200 bg-white/[0.03] hover:bg-white/[0.06] disabled:opacity-30 disabled:cursor-not-allowed transition-all text-slate-600 hover:text-slate-200"
                            >
                              <ChevronRight className="w-4 h-4" />
                            </button>
                          </div>
                        </div>
                      );
                    })()}
                  </>
                )}
              </div>
            </Card>
          </div>
        </div>

        {/* History Modal for member dashboard */}
        <AnimatePresence>
          {selectedName && (
            <HistoryModal
              name={selectedName}
              sessions={sessions}
              onClose={() => setSelectedName(null)}
            />
          )}
        </AnimatePresence>
      </div>
    </div>
  );
};

export default Dashboard;
