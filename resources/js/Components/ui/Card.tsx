import { ReactNode } from 'react';

interface CardProps {
    children: ReactNode;
    className?: string;
    padding?: 'none' | 'sm' | 'md' | 'lg';
}

export function Card({ children, className = '', padding = 'md' }: CardProps) {
    const paddingStyles = {
        none: '',
        sm: 'p-4',
        md: 'p-6',
        lg: 'p-8',
    };

    return (
        <div
            className={`bg-white rounded-lg shadow-sm border border-gray-200 ${paddingStyles[padding]} ${className}`}
        >
            {children}
        </div>
    );
}

export function CardHeader({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <div className={`border-b border-gray-200 pb-4 mb-4 ${className}`}>
            {children}
        </div>
    );
}

export function CardTitle({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <h3 className={`text-lg font-semibold text-gray-900 ${className}`}>
            {children}
        </h3>
    );
}

export function CardDescription({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <p className={`text-sm text-gray-500 mt-1 ${className}`}>{children}</p>
    );
}

export function CardContent({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return <div className={className}>{children}</div>;
}

export function CardFooter({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <div className={`border-t border-gray-200 pt-4 mt-4 ${className}`}>
            {children}
        </div>
    );
}
