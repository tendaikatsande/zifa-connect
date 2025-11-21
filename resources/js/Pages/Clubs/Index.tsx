import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader } from '@/Components/ui/Card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/Table';
import Button from '@/Components/ui/Button';
import Input from '@/Components/ui/Input';
import Select from '@/Components/ui/Select';
import Badge, { getStatusVariant } from '@/Components/ui/Badge';
import { Search, Plus } from 'lucide-react';

interface Club {
    id: number;
    name: string;
    short_name: string | null;
    registration_number: string | null;
    status: string;
    category: string | null;
    region?: {
        id: number;
        name: string;
    };
    affiliation_expiry: string | null;
    active_players_count?: number;
}

interface ClubsIndexProps {
    clubs: {
        data: Club[];
        current_page: number;
        last_page: number;
        total: number;
    };
    filters?: {
        search?: string;
        status?: string;
        region_id?: string;
    };
}

export default function ClubsIndex({ clubs, filters = {} }: ClubsIndexProps) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/clubs', { search }, { preserveState: true });
    };

    const statusOptions = [
        { value: '', label: 'All Statuses' },
        { value: 'pending', label: 'Pending' },
        { value: 'active', label: 'Active' },
        { value: 'inactive', label: 'Inactive' },
        { value: 'suspended', label: 'Suspended' },
    ];

    return (
        <AuthenticatedLayout>
            <Head title="Clubs" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Clubs</h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Manage club registrations and affiliations
                        </p>
                    </div>
                    <Button href="/clubs/create">
                        <Plus className="h-4 w-4 mr-2" />
                        Register Club
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <div className="flex flex-col sm:flex-row gap-4">
                            <form onSubmit={handleSearch} className="flex-1">
                                <div className="relative">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                    <Input
                                        type="search"
                                        placeholder="Search by name..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        className="pl-10"
                                    />
                                </div>
                            </form>
                            <Select
                                options={statusOptions}
                                value={filters.status || ''}
                                onChange={(e) => router.get('/clubs', { ...filters, status: e.target.value })}
                                className="w-40"
                            />
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Club</TableHead>
                                    <TableHead>Region</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Players</TableHead>
                                    <TableHead>Affiliation</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {clubs.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell className="text-center text-gray-500 py-8" colSpan={7}>
                                            No clubs found
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    clubs.data.map((club) => (
                                        <TableRow key={club.id}>
                                            <TableCell>
                                                <div>
                                                    <div className="font-medium text-gray-900">
                                                        {club.name}
                                                    </div>
                                                    {club.short_name && (
                                                        <div className="text-xs text-gray-500">
                                                            {club.short_name}
                                                        </div>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell>{club.region?.name || '-'}</TableCell>
                                            <TableCell className="capitalize">
                                                {club.category?.replace('_', ' ') || '-'}
                                            </TableCell>
                                            <TableCell>{club.active_players_count || 0}</TableCell>
                                            <TableCell>
                                                {club.affiliation_expiry ? (
                                                    <span className={new Date(club.affiliation_expiry) < new Date() ? 'text-red-600' : 'text-green-600'}>
                                                        {club.affiliation_expiry}
                                                    </span>
                                                ) : (
                                                    '-'
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getStatusVariant(club.status)}>
                                                    {club.status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <Link
                                                    href={`/clubs/${club.id}`}
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
