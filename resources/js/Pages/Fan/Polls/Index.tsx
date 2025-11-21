import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/Card';
import { Button } from '@/Components/ui/Button';
import { Badge } from '@/Components/ui/Badge';
import {
    Vote,
    Clock,
    CheckCircle,
    Users,
    Trophy,
    Goal,
    Star,
    Plus,
} from 'lucide-react';

interface PollOption {
    id: number;
    display_name: string;
    votes_count: number;
}

interface Poll {
    id: number;
    title: string;
    description: string;
    type: string;
    status: string;
    is_featured: boolean;
    starts_at: string;
    ends_at: string;
    options: PollOption[];
}

interface FanPollsIndexProps {
    polls?: {
        data: Poll[];
        current_page: number;
        last_page: number;
    };
}

const typeIcons: Record<string, React.ComponentType<{ className?: string }>> = {
    player_of_match: Trophy,
    goal_of_week: Goal,
    best_player: Star,
    custom: Vote,
};

const statusColors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-green-100 text-green-800',
    closed: 'bg-red-100 text-red-800',
    archived: 'bg-yellow-100 text-yellow-800',
};

export default function FanPollsIndex({ polls }: FanPollsIndexProps) {
    const pollList = polls?.data || [];

    const getTotalVotes = (poll: Poll) => {
        return poll.options.reduce((sum, opt) => sum + opt.votes_count, 0);
    };

    return (
        <AuthenticatedLayout>
            <Head title="Fan Polls" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                            <Vote className="h-6 w-6" />
                            Fan Polls
                        </h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Vote for your favorite players and moments
                        </p>
                    </div>
                    <Button as={Link} href="/fan/polls/create">
                        <Plus className="h-4 w-4 mr-2" />
                        Create Poll
                    </Button>
                </div>

                {pollList.length === 0 ? (
                    <Card>
                        <CardContent className="py-12 text-center">
                            <Vote className="h-12 w-12 mx-auto text-gray-400 mb-4" />
                            <p className="text-gray-500">No polls available yet.</p>
                            <Button as={Link} href="/fan/polls/create" className="mt-4">
                                Create First Poll
                            </Button>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-6 md:grid-cols-2">
                        {pollList.map((poll) => {
                            const TypeIcon = typeIcons[poll.type] || Vote;
                            const totalVotes = getTotalVotes(poll);

                            return (
                                <Card key={poll.id} className="hover:shadow-md transition-shadow">
                                    <CardHeader>
                                        <div className="flex items-start justify-between">
                                            <div className="flex items-center gap-2">
                                                <TypeIcon className="h-5 w-5 text-primary-600" />
                                                <CardTitle className="text-lg">
                                                    {poll.title}
                                                </CardTitle>
                                            </div>
                                            <Badge className={statusColors[poll.status]}>
                                                {poll.status}
                                            </Badge>
                                        </div>
                                    </CardHeader>
                                    <CardContent>
                                        {poll.description && (
                                            <p className="text-sm text-gray-600 mb-4">
                                                {poll.description}
                                            </p>
                                        )}

                                        <div className="space-y-2 mb-4">
                                            {poll.options.slice(0, 3).map((option) => (
                                                <div
                                                    key={option.id}
                                                    className="flex items-center justify-between text-sm"
                                                >
                                                    <span className="text-gray-700">
                                                        {option.display_name}
                                                    </span>
                                                    <span className="text-gray-500">
                                                        {option.votes_count} votes
                                                    </span>
                                                </div>
                                            ))}
                                            {poll.options.length > 3 && (
                                                <p className="text-xs text-gray-400">
                                                    +{poll.options.length - 3} more options
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex items-center justify-between pt-4 border-t">
                                            <div className="flex items-center gap-4 text-sm text-gray-500">
                                                <span className="flex items-center gap-1">
                                                    <Users className="h-4 w-4" />
                                                    {totalVotes} votes
                                                </span>
                                                {poll.ends_at && (
                                                    <span className="flex items-center gap-1">
                                                        <Clock className="h-4 w-4" />
                                                        {new Date(poll.ends_at).toLocaleDateString()}
                                                    </span>
                                                )}
                                            </div>
                                            <Link
                                                href={`/fan/polls/${poll.id}`}
                                                className="text-sm font-medium text-primary-600 hover:text-primary-700"
                                            >
                                                {poll.status === 'active' ? 'Vote Now' : 'View Results'}
                                            </Link>
                                        </div>
                                    </CardContent>
                                </Card>
                            );
                        })}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
