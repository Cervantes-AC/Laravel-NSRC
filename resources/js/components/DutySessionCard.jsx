import React from 'react';
import { Clock, MapPin, AlertTriangle, CheckCircle2, XCircle } from 'lucide-react';
import { motion } from 'motion/react';
import { DutySession } from '../types';

interface DutySessionCardProps {
  session: DutySession;
  index: number;
  onUpdated: () => Promise<void>;
  onFilterSector: (sector: string) => void;
}

const statusConfig = {
  COMPLETE: { bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-700', label: 'Complete', dot: 'bg-emerald-500', icon: CheckCircle2 },
  ONGOING: { bg: 'bg-blue-50', border: 'border-blue-200', text: 'text-blue-700', label: 'Ongoing', dot: 'bg-blue-500', icon: Clock },
  MISSING_TIMEOUT: { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-700', label: 'Missing Timeout', dot: 'bg-amber-500', icon: AlertTriangle },
  INVALID_LOG: { bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-700', label: 'Invalid', dot: 'bg-red-500', icon: XCircle },
};

export const DutySessionCard: React.FC<DutySessionCardProps> = ({ session, index, onUpdated, onFilterSector }) => {
  const status = statusConfig[session.status] || statusConfig.INVALID_LOG;
  const StatusIcon = status.icon;

  return (
    <motion.div
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: index * 0.04 }}
      className={`group relative overflow-hidden rounded-xl border ${status.border} ${status.bg} p-4 hover:shadow-md transition-all`}
    >
      <div className="flex items-start justify-between gap-4">
        <div className="flex-1 min-w-0">
          <div className="flex items-center gap-2 mb-1">
            <span className={`w-2 h-2 rounded-full ${status.dot} shadow-lg`} />
            <span className={`text-xs font-bold uppercase tracking-wider ${status.text}`}>{status.label}</span>
          </div>
          <h4 className="text-sm font-bold text-slate-900 truncate">{session.fullName}</h4>
          <div className="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
            <span className="text-xs text-slate-600 font-medium">{session.date}</span>
            <span className="text-xs text-slate-500 font-mono">{session.timeIn || 'N/A'}</span>
            {session.durationMinutes !== null && (
              <>
                <span className="w-1 h-1 bg-slate-300 rounded-full" />
                <span className="text-xs font-semibold text-slate-700">
                  {Math.floor(session.durationMinutes / 60)}h {session.durationMinutes % 60}m
                </span>
              </>
            )}
          </div>
          {session.location && (
            <button
              onClick={() => onFilterSector(session.location!)}
              className="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-slate-500 hover:text-orange-600 transition-colors"
            >
              <MapPin className="w-3 h-3" />
              {session.location}
            </button>
          )}
        </div>
        <div className="flex items-center gap-2 shrink-0">
          {session.integrityScore !== undefined && (
            <div className={`text-xs font-black px-2 py-1 rounded ${session.integrityScore >= 80 ? 'text-emerald-600 bg-emerald-100' : session.integrityScore >= 50 ? 'text-amber-600 bg-amber-100' : 'text-red-600 bg-red-100'}`}>
              {session.integrityScore}%
            </div>
          )}
          <StatusIcon className={`w-5 h-5 ${status.text}`} />
        </div>
      </div>
    </motion.div>
  );
};

export default DutySessionCard;
