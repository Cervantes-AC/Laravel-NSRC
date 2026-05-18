import React from 'react';
import { AlertTriangle, Info, CheckCircle2, XCircle, X } from 'lucide-react';

interface CardProps {
  variant?: 'default' | 'elevated' | 'outline';
  padding?: 'none' | 'sm' | 'md' | 'lg';
  className?: string;
  children: React.ReactNode;
}

const variantClasses = {
  default: 'bg-white shadow-sm border border-slate-200',
  elevated: 'bg-white shadow-lg border border-slate-200',
  outline: 'bg-transparent border border-slate-200',
};

const paddingClasses = {
  none: 'p-0',
  sm: 'p-3',
  md: 'p-4',
  lg: 'p-5 sm:p-6',
};

export const Card: React.FC<CardProps> = ({
  variant = 'default',
  padding = 'md',
  className = '',
  children,
}) => {
  return (
    <div className={`rounded-xl ${variantClasses[variant]} ${paddingClasses[padding]} ${className}`}>
      {children}
    </div>
  );
};

interface AlertProps {
  type: 'info' | 'warning' | 'success' | 'error';
  title: string;
  description?: string;
  onClose?: () => void;
}

const alertConfig = {
  info: { bg: 'bg-blue-50 border-blue-200', icon: Info, iconColor: 'text-blue-600', titleColor: 'text-blue-800', descColor: 'text-blue-700' },
  warning: { bg: 'bg-amber-50 border-amber-200', icon: AlertTriangle, iconColor: 'text-amber-600', titleColor: 'text-amber-800', descColor: 'text-amber-700' },
  success: { bg: 'bg-emerald-50 border-emerald-200', icon: CheckCircle2, iconColor: 'text-emerald-600', titleColor: 'text-emerald-800', descColor: 'text-emerald-700' },
  error: { bg: 'bg-red-50 border-red-200', icon: XCircle, iconColor: 'text-red-600', titleColor: 'text-red-800', descColor: 'text-red-700' },
};

export const Alert: React.FC<AlertProps> = ({ type, title, description, onClose }) => {
  const config = alertConfig[type];
  const Icon = config.icon;

  return (
    <div className={`relative rounded-xl border p-4 ${config.bg}`}>
      <div className="flex gap-3">
        <Icon className={`w-5 h-5 mt-0.5 shrink-0 ${config.iconColor}`} />
        <div className="flex-1">
          <p className={`text-sm font-bold ${config.titleColor}`}>{title}</p>
          {description && (
            <p className={`text-xs mt-1 ${config.descColor}`}>{description}</p>
          )}
        </div>
        {onClose && (
          <button onClick={onClose} className={`shrink-0 ${config.iconColor} hover:opacity-70 transition-opacity`}>
            <X className="w-4 h-4" />
          </button>
        )}
      </div>
    </div>
  );
};
