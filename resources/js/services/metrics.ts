import { DutySession, VolunteerMetrics } from '../types';

const STANDARD_WORK_MINUTES = 480; // 8 hours
const MIN_SESSION_MINUTES = 60; // 1 hour

export const calculateVolunteerMetrics = (sessions: DutySession[]): VolunteerMetrics[] => {
  const grouped: Record<string, DutySession[]> = {};

  // Group sessions by volunteer
  sessions.forEach(session => {
    if (!grouped[session.fullName]) {
      grouped[session.fullName] = [];
    }
    grouped[session.fullName].push(session);
  });

  // Calculate metrics for each volunteer
  return Object.entries(grouped).map(([fullName, volunteerSessions]) => {
    let totalRegularMinutes = 0;
    let totalOvertimeMinutes = 0;
    let totalUndertimeMinutes = 0;
    let completedSessions = 0;

    volunteerSessions.forEach(session => {
      if (session.status === 'COMPLETE' && session.durationMinutes) {
        completedSessions++;
        const duration = session.durationMinutes;

        if (duration < MIN_SESSION_MINUTES) {
          totalUndertimeMinutes += duration;
        } else if (duration <= STANDARD_WORK_MINUTES) {
          totalRegularMinutes += duration;
        } else {
          totalRegularMinutes += STANDARD_WORK_MINUTES;
          totalOvertimeMinutes += duration - STANDARD_WORK_MINUTES;
        }
      }
    });

    const completionRate = volunteerSessions.length > 0
      ? Math.round((completedSessions / volunteerSessions.length) * 100)
      : 0;

    return {
      fullName,
      totalRegularMinutes,
      totalOvertimeMinutes,
      totalUndertimeMinutes,
      sessionCount: volunteerSessions.length,
      completionRate,
    };
  });
};

export const calculateDailyMetrics = (sessions: DutySession[], date: string) => {
  const daySessions = sessions.filter(s => s.date === date);
  
  let totalMinutes = 0;
  let completedCount = 0;

  daySessions.forEach(session => {
    if (session.status === 'COMPLETE' && session.durationMinutes) {
      totalMinutes += session.durationMinutes;
      completedCount++;
    }
  });

  return {
    date,
    totalMinutes,
    completedCount,
    totalSessions: daySessions.length,
    avgDuration: completedCount > 0 ? Math.round(totalMinutes / completedCount) : 0,
  };
};

export const calculateWeeklyMetrics = (sessions: DutySession[], startDate: Date, endDate: Date) => {
  const weekSessions = sessions.filter(s => {
    const sessionDate = new Date(s.date);
    return sessionDate >= startDate && sessionDate <= endDate;
  });

  let totalMinutes = 0;
  let completedCount = 0;

  weekSessions.forEach(session => {
    if (session.status === 'COMPLETE' && session.durationMinutes) {
      totalMinutes += session.durationMinutes;
      completedCount++;
    }
  });

  return {
    totalMinutes,
    completedCount,
    totalSessions: weekSessions.length,
    avgDuration: completedCount > 0 ? Math.round(totalMinutes / completedCount) : 0,
  };
};
