import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/Card';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/Table';
import Button from '@/Components/ui/Button';
import Input from '@/Components/ui/Input';
import Select from '@/Components/ui/Select';
import Badge, { getStatusVariant } from '@/Components/ui/Badge';
import { Search, Plus } from 'lucide-react';

interface Player {
    id: number;
    zifa_id: string | null;
    first_name: string;
    last_name: string;
    dob: string;
    gender: string;
    status: string;
    registration_category: string;
    current_club?: {
        id: number;
        name: string;
    };
    created_at: string;
}

interface PlayersIndexProps {
    players: {
        data: Player[];
        current_page: number;
        last_page: number;
        total: number;
    };
    filters?: {
        search?: string;
        status?: string;
        category?: string;
    };
}

export default function PlayersIndex({ players, filters = {} }: PlayersIndexProps) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/players', { search }, { preserveState: true });
    };

    const statusOptions = [
        { value: '', label: 'All Statuses' },
        { value: 'draft', label: 'Draft' },
        { value: 'submitted', label: 'Submitted' },
        { value: 'under_review', label: 'Under Review' },
        { value: 'approved', label: 'Approved' },
        { value: 'rejected', label: 'Rejected' },
    ];

    const categoryOptions = [
        { value: '', label: 'All Categories' },
        { value: 'senior', label: 'Senior' },
        { value: 'u20', label: 'U-20' },
        { value: 'u17', label: 'U-17' },
        { value: 'women', label: 'Women' },
        { value: 'futsal', label: 'Futsal' },
    ];

    return (
        <AuthenticatedLayout>
            <Head title="Players" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900">Players</h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Manage player registrations
                        </p>
                    </div>
                    <Button href="/players/create">
                        <Plus className="h-4 w-4 mr-2" />
                        Register Player
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
                                        placeholder="Search by name or ZIFA ID..."
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
                                    onChange={(e) => router.get('/players', { ...filters, status: e.target.value })}
                                    className="w-40"
                                />
                                <Select
                                    options={categoryOptions}
                                    value={filters.category || ''}
                                    onChange={(e) => router.get('/players', { ...filters, category: e.target.value })}
                                    className="w-40"
                                />
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ZIFA ID</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Club</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {players.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell className="text-center text-gray-500 py-8" colSpan={6}>
                                            No players found
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    players.data.map((player) => (
                                        <TableRow key={player.id}>
                                            <TableCell className="font-medium">
                                                {player.zifa_id || '-'}
                                            </TableCell>
                                            <TableCell>
                                                <div>
                                                    <div className="font-medium text-gray-900">
                                                        {player.first_name} {player.last_name}
                                                    </div>
                                                    <div className="text-xs text-gray-500">
                                                        {player.dob}
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell>{player.registration_category}</TableCell>
                                            <TableCell>
                                                {player.current_club?.name || 'Free Agent'}
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant={getStatusVariant(player.status)}>
                                                    {player.status.replace('_', ' ')}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                <Link
                                                    href={`/players/${player.id}`}
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

                {/* Pagination */}
                {players.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-sm text-gray-500">
                            Showing page {players.current_page} of {players.last_page} ({players.total} total)
                        </p>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={players.current_page === 1}
                                onClick={() => router.get('/players', { ...filters, page: players.current_page - 1 })}
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={players.current_page === players.last_page}
                                onClick={() => router.get('/players', { ...filters, page: players.current_page + 1 })}
                            >
                                Next
                            </Button>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
