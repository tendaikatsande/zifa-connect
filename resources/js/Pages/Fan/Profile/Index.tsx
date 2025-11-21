import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/Card';
import { Button } from '@/Components/ui/Button';
import { Badge } from '@/Components/ui/Badge';
import {
    User,
    Heart,
    Trophy,
    Star,
    MapPin,
    Calendar,
    Medal,
    Users,
    Ticket,
    Bell,
    Settings,
} from 'lucide-react';

interface FanProfile {
    id: number;
    nickname: string;
    city: string;
    member_since: string;
    loyalty_points: number;
    membership_tier: string;
    favorite_club?: { id: number; name: string };
    favorite_player?: { id: number; first_name: string; last_name: string };
}

interface ClubFollow {
    id: number;
    club: { id: number; name: string };
    notifications_enabled: boolean;
}

interface PlayerFollow {
    id: number;
    player: { id: number; first_name: string; last_name: string };
    notifications_enabled: boolean;
}

interface FanProfileIndexProps {
    profile?: FanProfile;
    followedClubs?: ClubFollow[];
    followedPlayers?: PlayerFollow[];
    attendances?: { id: number; match: { id: number }; status: string }[];
}

const tierColors: Record<string, string> = {
    bronze: 'bg-amber-100 text-amber-800',
    silver: 'bg-gray-200 text-gray-800',
    gold: 'bg-yellow-100 text-yellow-800',
    platinum: 'bg-purple-100 text-purple-800',
};

export default function FanProfileIndex({
    profile,
    followedClubs = [],
    followedPlayers = [],
    attendances = [],
}: FanProfileIndexProps) {
    if (!profile) {
        return (
            <AuthenticatedLayout>
                <Head title="Fan Profile" />
                <div className="space-y-6">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                            <User className="h-6 w-6" />
                            Fan Profile
                        </h1>
                    </div>
                    <Card>
                        <CardContent className="py-12 text-center">
                            <User className="h-12 w-12 mx-auto text-gray-400 mb-4" />
                            <h3 className="text-lg font-medium text-gray-900 mb-2">
                                Create Your Fan Profile
                            </h3>
                            <p className="text-gray-500 mb-4">
                                Join our fan community to earn loyalty points, follow your favorite clubs and players, and participate in polls.
                            </p>
                            <Button as={Link} href="/fan/profile/create">
                                Create Profile
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </AuthenticatedLayout>
        );
    }

    return (
        <AuthenticatedLayout>
            <Head title="Fan Profile" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                            <User className="h-6 w-6" />
                            Fan Profile
                        </h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Manage your fan profile and preferences
                        </p>
                    </div>
                    <Button as={Link} href="/fan/profile/edit" variant="outline">
                        <Settings className="h-4 w-4 mr-2" />
                        Edit Profile
                    </Button>
                </div>

                {/* Profile Overview */}
                <div className="grid gap-6 md:grid-cols-3">
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle>Profile Information</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-start gap-6">
                                <div className="h-20 w-20 rounded-full bg-primary-100 flex items-center justify-center">
                                    <User className="h-10 w-10 text-primary-600" />
                                </div>
                                <div className="flex-1">
                                    <h3 className="text-xl font-semibold text-gray-900">
                                        {profile.nickname || 'Anonymous Fan'}
                                    </h3>
                                    <div className="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                        {profile.city && (
                                            <span className="flex items-center gap-1">
                                                <MapPin className="h-4 w-4" />
                                                {profile.city}
                                            </span>
                                        )}
                                        <span className="flex items-center gap-1">
                                            <Calendar className="h-4 w-4" />
                                            Member since {new Date(profile.member_since).toLocaleDateString()}
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-4 mt-4">
                                        <Badge className={tierColors[profile.membership_tier]}>
                                            <Medal className="h-3 w-3 mr-1" />
                                            {profile.membership_tier.charAt(0).toUpperCase() + profile.membership_tier.slice(1)}
                                        </Badge>
                                        <span className="text-sm font-medium text-primary-600">
                                            {profile.loyalty_points.toLocaleString()} points
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {(profile.favorite_club || profile.favorite_player) && (
                                <div className="mt-6 pt-6 border-t grid gap-4 sm:grid-cols-2">
                                    {profile.favorite_club && (
                                        <div>
                                            <p className="text-sm font-medium text-gray-500 mb-1">Favorite Club</p>
                                            <p className="flex items-center gap-2 text-gray-900">
                                                <Heart className="h-4 w-4 text-red-500 fill-red-500" />
                                                {profile.favorite_club.name}
                                            </p>
                                        </div>
                                    )}
                                    {profile.favorite_player && (
                                        <div>
                                            <p className="text-sm font-medium text-gray-500 mb-1">Favorite Player</p>
                                            <p className="flex items-center gap-2 text-gray-900">
                                                <Star className="h-4 w-4 text-yellow-500 fill-yellow-500" />
                                                {profile.favorite_player.first_name} {profile.favorite_player.last_name}
                                            </p>
                                        </div>
                                    )}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Stats Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Your Stats</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <span className="flex items-center gap-2 text-sm text-gray-600">
                                        <Users className="h-4 w-4" />
                                        Following Clubs
                                    </span>
                                    <span className="font-semibold">{followedClubs.length}</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="flex items-center gap-2 text-sm text-gray-600">
                                        <Star className="h-4 w-4" />
                                        Following Players
                                    </span>
                                    <span className="font-semibold">{followedPlayers.length}</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="flex items-center gap-2 text-sm text-gray-600">
                                        <Ticket className="h-4 w-4" />
                                        Matches Attended
                                    </span>
                                    <span className="font-semibold">
                                        {attendances.filter(a => a.status === 'attended').length}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="flex items-center gap-2 text-sm text-gray-600">
                                        <Trophy className="h-4 w-4" />
                                        Loyalty Points
                                    </span>
                                    <span className="font-semibold text-primary-600">
                                        {profile.loyalty_points.toLocaleString()}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Following Section */}
                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Heart className="h-5 w-5" />
                                Following Clubs
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {followedClubs.length === 0 ? (
                                <p className="text-sm text-gray-500">You are not following any clubs yet.</p>
                            ) : (
                                <ul className="space-y-3">
                                    {followedClubs.map((follow) => (
                                        <li key={follow.id} className="flex items-center justify-between">
                                            <span className="text-sm font-medium">{follow.club.name}</span>
                                            {follow.notifications_enabled && (
                                                <Bell className="h-4 w-4 text-primary-600" />
                                            )}
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Star className="h-5 w-5" />
                                Following Players
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {followedPlayers.length === 0 ? (
                                <p className="text-sm text-gray-500">You are not following any players yet.</p>
                            ) : (
                                <ul className="space-y-3">
                                    {followedPlayers.map((follow) => (
                                        <li key={follow.id} className="flex items-center justify-between">
                                            <span className="text-sm font-medium">
                                                {follow.player.first_name} {follow.player.last_name}
                                            </span>
                                            {follow.notifications_enabled && (
                                                <Bell className="h-4 w-4 text-primary-600" />
                                            )}
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
