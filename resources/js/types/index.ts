export interface User {
  id: string;
  fullName: string;
  email: string;
  role: 'admin' | 'member' | 'supervisor';
  sector?: string;
  serialNumber?: string;
  nsrcSerialNumber?: string;
}

export type DutyStatus = 'COMPLETE' | 'ONGOING' | 'MISSING_TIMEOUT' | 'INVALID_LOG';

export interface DutySession {
  fullName: string;
  volunteerId: string;
  date: string;
  timeIn: string;
  timeOut: string | null;
  durationMinutes: number | null;
  status: DutyStatus;
  traceId: string;
  location?: string;
  sector?: string;
  integrityScore?: number;
}

export interface Attendance {
  dateTime: string;
  fullName: string;
  attendance: 'Time in' | 'Time out';
  location?: string;
}

export interface AttendanceStats {
  totalRecords: number;
  todayCount: number;
  activeNow: number;
  missingTimeouts: number;
  avgDuration: string;
}

export interface VolunteerMetrics {
  fullName: string;
  totalRegularMinutes: number;
  totalOvertimeMinutes: number;
  totalUndertimeMinutes: number;
  sessionCount: number;
  completionRate: number;
}
