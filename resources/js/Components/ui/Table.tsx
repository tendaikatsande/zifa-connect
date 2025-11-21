import { ReactNode } from 'react';

interface TableProps {
    children: ReactNode;
    className?: string;
}

export function Table({ children, className = '' }: TableProps) {
    return (
        <div className="overflow-x-auto">
            <table className={`min-w-full divide-y divide-gray-200 ${className}`}>
                {children}
            </table>
        </div>
    );
}

export function TableHeader({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return <thead className={`bg-gray-50 ${className}`}>{children}</thead>;
}

export function TableBody({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <tbody className={`bg-white divide-y divide-gray-200 ${className}`}>
            {children}
        </tbody>
    );
}

export function TableRow({
    children,
    className = '',
    onClick,
}: {
    children: ReactNode;
    className?: string;
    onClick?: () => void;
}) {
    return (
        <tr
            className={`${onClick ? 'cursor-pointer hover:bg-gray-50' : ''} ${className}`}
            onClick={onClick}
        >
            {children}
        </tr>
    );
}

export function TableHead({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <th
            scope="col"
            className={`px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ${className}`}
        >
            {children}
        </th>
    );
}

export function TableCell({
    children,
    className = '',
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <td className={`px-6 py-4 whitespace-nowrap text-sm ${className}`}>
            {children}
        </td>
    );
}
