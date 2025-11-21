import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader } from '@/Components/ui/Card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/Table';
import Button from '@/Components/ui/Button';
import Input from '@/Components/ui/Input';
import Select from '@/Components/ui/Select';
import Badge, { getStatusVariant } from '@/Components/ui/Badge';
import { MagnifyingGlassIcon, PlusIcon, ArrowRightIcon } from '@heroicons/react/24/outline';

interface Transfer {
    id: number;
    transfer_reference: string;
    type: string;
    status: string;
    transfer_fee_usd: number;
    created_at: string;
    player: {
        id: number;
        first_name: string;
        last_name: string;
    };
    from_club?: {
        id: number;
        name: string;
    };
    to_club: {
        id: number;
        name: string;
    };
}

interface TransfersIndexProps {
    transfers: {
        data: Transfer[];
        current_page: number;
        last_page: number;
        total: number;
    };
    filters?: {
        search?: string;
        status?: string;
        type?: string;
    };
}

export default function TransfersIndex({ transfers, filters = {} }: TransfersIndexProps) {
    const [search, setSearch] = useState(filters.search || '');

    const statusOptions = [
        { value: '', label: 'All Statuses' },
        { value: 'requested', label: 'Requested' },
        { value: 'pending_from_club', label: 'Pending Club' },
        { value: 'pending_payment', label: 'Pending Payment' },
        { value: 'pending_zifa_review', label: 'Pending ZIFA' },
        { value: 'completed', label: 'Completed' },
        { value: 'rejected', label: 'Rejected' },
    ];

    const typeOptions = [
        { value: '', label: 'All Types' },
        { value: 'local', label: 'Local' },
        { value: 'international', label: 'International' },
        { value: 'loan', label: 'Loan' },
        { value: 'free', label: 'Free' },
    ];

    return (
        <AuthenticatedLayout>
            <Head title="Transfers" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Transfers</h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Manage player transfers
                        </p>
                    </div>
                    <Button href="/transfers/create">
                        <PlusIcon className="h-4 w-4 mr-2" />
                        New Transfer
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <div className="flex flex-col sm:flex-row gap-4">
                            <form onSubmit={(e) => { e.preventDefault(); router.get('/transfers', { search }); }} className="flex-1">
                                <div className="relative">
                                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                    <Input
                                        type="search"
                                        placeholder="Search by reference..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        className="pl-10"
                                    />
                                </div>
                            </form>
                            <div className="flex gap-2">
                                <Select
                                    options={statusOptions}
                                    value={filters.status || ''}
                                    onChange={(e) => router.get('/transfers', { ...filters, status: e.target.value })}
                                    className="w-40"
                                />
                                <Select
                                    options={typeOptions}
                                    value={filters.type || ''}
                                    onChange={(e) => router.get('/transfers', { ...filters, type: e.target.value })}
                                    className="w-36"
                                />
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Reference</TableHead>
                                    <TableHead>Player</TableHead>
                                    <TableHead>Transfer</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Fee</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {transfers.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell className="text-center text-gray-500 py-8" colSpan={7}>
                                            No transfers found
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    transfers.data.map((transfer) => (
                                        <TableRow key={transfer.id}>
                                            <TableCell className="font-medium">
                                                {transfer.transfer_reference}
                                            </TableCell>
                                            <TableCell>
                                                {transfer.player.first_name} {transfer.player.last_name}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-1 text-sm">
                                                    <span className="truncate max-w-20">
                                                        {transfer.from_club?.name || 'Free'}
                                                    </span>
                                                    <ArrowRightIcon className="h-3 w-3 text-gray-400 flex-shrink-0" />
                                                    <span className="truncate max-w-20">
                                                        {transfer.to_club.name}
                                                    </span>
                                                </div>
                                            </TableCell>
                                            <TableCell className="capitalize">
                                                {transfer.type}
                                            </TableCell>
                                            <TableCell>
                                                ${transfer.transfer_fee_usd?.toLocaleString() || 0}
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getStatusVariant(transfer.status)}>
                                                    {transfer.status.replace(/_/g, ' ')}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <Link
                                                    href={`/transfers/${transfer.id}`}
                                                    className="text-primary-600 hover:text-primary-800 text-sm font-medium"
                                                >
                                                    View
                                                </Link>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
