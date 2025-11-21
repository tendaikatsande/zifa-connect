import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader } from '@/Components/ui/Card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/Table';
import Button from '@/Components/ui/Button';
import Select from '@/Components/ui/Select';
import Badge, { getStatusVariant } from '@/Components/ui/Badge';

interface Invoice {
    id: number;
    invoice_number: string;
    description: string;
    category: string;
    amount_cents: number;
    currency: string;
    status: string;
    due_date: string;
    paid_date: string | null;
    club?: {
        id: number;
        name: string;
    };
}

interface InvoicesIndexProps {
    invoices: {
        data: Invoice[];
        current_page: number;
        last_page: number;
        total: number;
    };
    filters?: {
        status?: string;
        category?: string;
    };
}

export default function InvoicesIndex({ invoices, filters = {} }: InvoicesIndexProps) {
    const statusOptions = [
        { value: '', label: 'All Statuses' },
        { value: 'draft', label: 'Draft' },
        { value: 'sent', label: 'Sent' },
        { value: 'pending', label: 'Pending' },
        { value: 'paid', label: 'Paid' },
        { value: 'overdue', label: 'Overdue' },
    ];

    const categoryOptions = [
        { value: '', label: 'All Categories' },
        { value: 'registration', label: 'Registration' },
        { value: 'affiliation', label: 'Affiliation' },
        { value: 'transfer', label: 'Transfer' },
        { value: 'fine', label: 'Fine' },
    ];

    const formatAmount = (cents: number, currency: string) => {
        return `${currency} ${(cents / 100).toLocaleString()}`;
    };

    return (
        <AuthenticatedLayout>
            <Head title="Invoices" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Invoices</h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Manage payments and invoices
                        </p>
                    </div>
                </div>

                <Card>
                    <CardHeader>
                        <div className="flex gap-4">
                            <Select
                                options={statusOptions}
                                value={filters.status || ''}
                                onChange={(e) => router.get('/invoices', { ...filters, status: e.target.value })}
                                className="w-40"
                            />
                            <Select
                                options={categoryOptions}
                                value={filters.category || ''}
                                onChange={(e) => router.get('/invoices', { ...filters, category: e.target.value })}
                                className="w-40"
                            />
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Invoice #</TableHead>
                                    <TableHead>Description</TableHead>
                                    <TableHead>Club</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Amount</TableHead>
                                    <TableHead>Due Date</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {invoices.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell className="text-center text-gray-500 py-8" colSpan={8}>
                                            No invoices found
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    invoices.data.map((invoice) => (
                                        <TableRow key={invoice.id}>
                                            <TableCell className="font-medium">
                                                {invoice.invoice_number}
                                            </TableCell>
                                            <TableCell className="max-w-xs truncate">
                                                {invoice.description}
                                            </TableCell>
                                            <TableCell>
                                                {invoice.club?.name || '-'}
                                            </TableCell>
                                            <TableCell className="capitalize">
                                                {invoice.category}
                                            </TableCell>
                                            <TableCell className="font-medium">
                                                {formatAmount(invoice.amount_cents, invoice.currency)}
                                            </TableCell>
                                            <TableCell>
                                                {invoice.due_date}
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getStatusVariant(invoice.status)}>
                                                    {invoice.status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex gap-2">
                                                    <Link
                                                        href={`/invoices/${invoice.id}`}
                                                        className="text-primary-600 hover:text-primary-800 text-sm font-medium"
                                                    >
                                                        View
                                                    </Link>
                                                    {invoice.status !== 'paid' && (
                                                        <Link
                                                            href={`/invoices/${invoice.id}/pay`}
                                                            className="text-green-600 hover:text-green-800 text-sm font-medium"
                                                        >
                                                            Pay
                                                        </Link>
                                                    )}
                                                </div>
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
