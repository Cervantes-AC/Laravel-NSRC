import React from 'react';
import { X, Clock, Calendar, AlertTriangle } from 'lucide-react';
import { motion } from 'motion/react';
import { DutySession } from '../types';
import { format } from 'date-fns';

interface HistoryModalProps {
  name: string;
  sessions: DutySession[];
  onClose: () => void;
}

const statusBadge = (status: string) => {
  switch (status) {
    case 'COMPLETE':
      return <span className="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 uppercase tracking-wider">Complete</span>;
    case 'ONGOING':
      return <span className="text-[10px] font-black px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 uppercase tracking-wider">Ongoing</span>;
    case 'MISSING_TIMEOUT':
      return <span className="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 uppercase tracking-wider">Missing Timeout</span>;
    default:
      return <span className="text-[10px] font-black px-2 py-0.5 rounded-full bg-red-100 text-red-700 uppercase tracking-wider">Invalid</span>;
  }
};

export const HistoryModal: React.FC<HistoryModalProps> = ({ name, sessions, onClose }) => {
  const personSessions = sessions.filter(s => s.fullName === name);
  const totalMinutes = personSessions.reduce((sum, s) => sum + (s.durationMinutes || 0), 0);
  const totalHours = (totalMinutes / 60).toFixed(1);

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
      onClick={onClose}
    >
      <motion.div
        initial={{ opacity: 0, scale: 0.95, y: 20 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.95, y: 20 }}
        onClick={e => e.stopPropagation()}
        className="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col border border-slate-200"
      >
        <div className="flex items-center justify-between p-5 border-b border-slate-200">
          <div>
            <h3 className="text-base font-black text-slate-900">{name}</h3>
            <p className="text-xs text-slate-600 font-medium mt-0.5">
              {personSessions.length} sessions · {totalHours}h total
            </p>
          </div>
          <button
            onClick={onClose}
            className="p-2 rounded-lg hover:bg-slate-100 transition-colors text-slate-500 hover:text-slate-700"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        <div className="flex-1 overflow-y-auto p-5 space-y-3">
          {personSessions.length === 0 ? (
            <div className="flex flex-col items-center py-12 text-slate-500">
              <Calendar className="w-10 h-10 mb-3 opacity-40" />
              <p className="text-sm font-medium">No sessions found for this person.</p>
            </div>
          ) : (
            personSessions
              .sort((a, b) => new Date(b.timeIn).getTime() - new Date(a.timeIn).getTime())
              .map((session, idx) => (
                <div
                  key={`${session.traceId}-${idx}`}
                  className="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-colors"
                >
                  <div className="flex items-center gap-3 min-w-0">
                    <div className="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center shrink-0">
                      <Clock className="w-4 h-4 text-slate-600" />
                    </div>
                    <div className="min-w-0">
                      <p className="text-sm font-bold text-slate-900">{session.date}</p>
                      <p className="text-xs text-slate-600 font-mono mt-0.5">
                        {session.timeIn && session.timeIn !== 'N/A'
                          ? format(new Date(session.timeIn), 'HH:mm')
                          : 'N/A'}
                        {session.timeOut && ` — ${format(new Date(session.timeOut), 'HH:mm')}`}
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-3 shrink-0">
                    {session.durationMinutes !== null && (
                      <span className="text-xs font-bold text-slate-700">
                        {Math.floor(session.durationMinutes / 60)}h {session.durationMinutes % 60}m
                      </span>
                    )}
                    {statusBadge(session.status)}
                  </div>
                </div>
              ))
          )}
        </div>
      </motion.div>
    </motion.div>
  );
};

export default HistoryModal;
