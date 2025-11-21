import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/Card';
import {
    UsersIcon,
    BuildingOfficeIcon,
    ArrowsRightLeftIcon,
    CurrencyDollarIcon,
    DocumentCheckIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';

interface DashboardProps {
    stats?: {
        total_players: number;
        active_clubs: number;
        pending_registrations: number;
        pending_transfers: number;
        total_revenue_usd: number;
        outstanding_invoices: number;
    };
}

export default function Dashboard({ stats }: DashboardProps) {
    const defaultStats = stats || {
        total_players: 0,
        active_clubs: 0,
        pending_registrations: 0,
        pending_transfers: 0,
        total_revenue_usd: 0,
        outstanding_invoices: 0,
    };

    const statCards = [
        {
            name: 'Total Players',
            value: defaultStats.total_players.toLocaleString(),
            icon: UsersIcon,
            color: 'bg-blue-100 text-blue-600',
        },
        {
            name: 'Active Clubs',
            value: defaultStats.active_clubs.toLocaleString(),
            icon: BuildingOfficeIcon,
            color: 'bg-green-100 text-green-600',
        },
        {
            name: 'Pending Registrations',
            value: defaultStats.pending_registrations.toLocaleString(),
            icon: DocumentCheckIcon,
            color: 'bg-yellow-100 text-yellow-600',
        },
        {
            name: 'Pending Transfers',
            value: defaultStats.pending_transfers.toLocaleString(),
            icon: ArrowsRightLeftIcon,
            color: 'bg-purple-100 text-purple-600',
        },
        {
            name: 'Revenue (USD)',
            value: `$${defaultStats.total_revenue_usd.toLocaleString()}`,
            icon: CurrencyDollarIcon,
            color: 'bg-primary-100 text-primary-600',
        },
        {
            name: 'Outstanding',
            value: `$${defaultStats.outstanding_invoices.toLocaleString()}`,
            icon: ExclamationTriangleIcon,
            color: 'bg-red-100 text-red-600',
        },
    ];

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Welcome to ZIFA Connect Management System
                    </p>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    {statCards.map((stat) => (
                        <Card key={stat.name} padding="none">
                            <CardContent className="p-5">
                                <div className="flex items-center">
                                    <div className={`p-3 rounded-lg ${stat.color}`}>
                                        <stat.icon className="h-6 w-6" />
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-500">
                                            {stat.name}
                                        </p>
                                        <p className="text-2xl font-semibold text-gray-900">
                                            {stat.value}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* Quick Actions */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Registrations</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm text-gray-500">
                                No recent registrations to display.
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Pending Transfers</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm text-gray-500">
                                No pending transfers to display.
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
