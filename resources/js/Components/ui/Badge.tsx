import { ReactNode } from 'react';

interface BadgeProps {
    children: ReactNode;
    variant?: 'default' | 'success' | 'warning' | 'danger' | 'info';
    size?: 'sm' | 'md';
    className?: string;
}

export default function Badge({
    children,
    variant = 'default',
    size = 'sm',
    className = '',
}: BadgeProps) {
    const variants = {
        default: 'bg-gray-100 text-gray-800',
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        danger: 'bg-red-100 text-red-800',
        info: 'bg-blue-100 text-blue-800',
    };

    const sizes = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-1 text-sm',
    };

    return (
        <span
            className={`inline-flex items-center rounded-full font-medium ${variants[variant]} ${sizes[size]} ${className}`}
        >
            {children}
        </span>
    );
}

// Helper to map status to badge variant
export function getStatusVariant(status: string): BadgeProps['variant'] {
    const statusMap: Record<string, BadgeProps['variant']> = {
        // Player/Club statuses
        draft: 'default',
        submitted: 'info',
        under_review: 'warning',
        approved: 'success',
        rejected: 'danger',
        suspended: 'danger',
        active: 'success',
        inactive: 'default',
        pending: 'warning',

        // Payment statuses
        paid: 'success',
        partial: 'warning',
        overdue: 'danger',
        failed: 'danger',

        // Transfer statuses
        requested: 'info',
        pending_from_club: 'warning',
        pending_payment: 'warning',
        pending_zifa_review: 'warning',
        completed: 'success',
        cancelled: 'default',
    };

    return statusMap[status] || 'default';
}
