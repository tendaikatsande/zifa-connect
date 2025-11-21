import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/Card';
import Button from '@/Components/ui/Button';
import Input from '@/Components/ui/Input';
import Select from '@/Components/ui/Select';

interface Club {
    id: number;
    name: string;
}

interface CreatePlayerProps {
    clubs: Club[];
}

export default function CreatePlayer({ clubs = [] }: CreatePlayerProps) {
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        other_names: '',
        dob: '',
        gender: '',
        nationality: 'Zimbabwean',
        place_of_birth: '',
        phone: '',
        email: '',
        address: '',
        national_id: '',
        passport_number: '',
        current_club_id: '',
        registration_category: '',
        primary_position: '',
        secondary_position: '',
        height_cm: '',
        weight_kg: '',
        dominant_foot: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/api/v1/players');
    };

    const genderOptions = [
        { value: 'M', label: 'Male' },
        { value: 'F', label: 'Female' },
        { value: 'Other', label: 'Other' },
    ];

    const categoryOptions = [
        { value: 'senior', label: 'Senior' },
        { value: 'u20', label: 'U-20' },
        { value: 'u17', label: 'U-17' },
        { value: 'u15', label: 'U-15' },
        { value: 'women', label: 'Women' },
        { value: 'futsal', label: 'Futsal' },
    ];

    const footOptions = [
        { value: 'right', label: 'Right' },
        { value: 'left', label: 'Left' },
        { value: 'both', label: 'Both' },
    ];

    const positionOptions = [
        { value: 'GK', label: 'Goalkeeper' },
        { value: 'CB', label: 'Centre Back' },
        { value: 'LB', label: 'Left Back' },
        { value: 'RB', label: 'Right Back' },
        { value: 'CDM', label: 'Defensive Midfielder' },
        { value: 'CM', label: 'Central Midfielder' },
        { value: 'CAM', label: 'Attacking Midfielder' },
        { value: 'LW', label: 'Left Winger' },
        { value: 'RW', label: 'Right Winger' },
        { value: 'ST', label: 'Striker' },
    ];

    const clubOptions = clubs.map((club) => ({
        value: String(club.id),
        label: club.name,
    }));

    return (
        <AuthenticatedLayout>
            <Head title="Register Player" />

            <div className="max-w-4xl mx-auto space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Register New Player</h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Fill in the player details to begin registration
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Personal Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Personal Information</CardTitle>
                            <CardDescription>Basic player details</CardDescription>
                        </CardHeader>
                        <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Input
                                label="First Name *"
                                value={data.first_name}
                                onChange={(e) => setData('first_name', e.target.value)}
                                error={errors.first_name}
                                required
                            />
                            <Input
                                label="Last Name *"
                                value={data.last_name}
                                onChange={(e) => setData('last_name', e.target.value)}
                                error={errors.last_name}
                                required
                            />
                            <Input
                                label="Other Names"
                                value={data.other_names}
                                onChange={(e) => setData('other_names', e.target.value)}
                            />
                            <Input
                                label="Date of Birth *"
                                type="date"
                                value={data.dob}
                                onChange={(e) => setData('dob', e.target.value)}
                                error={errors.dob}
                                required
                            />
                            <Select
                                label="Gender *"
                                options={genderOptions}
                                value={data.gender}
                                onChange={(e) => setData('gender', e.target.value)}
                                error={errors.gender}
                                placeholder="Select gender"
                            />
                            <Input
                                label="Nationality *"
                                value={data.nationality}
                                onChange={(e) => setData('nationality', e.target.value)}
                                error={errors.nationality}
                                required
                            />
                            <Input
                                label="Place of Birth"
                                value={data.place_of_birth}
                                onChange={(e) => setData('place_of_birth', e.target.value)}
                            />
                            <Input
                                label="National ID"
                                value={data.national_id}
                                onChange={(e) => setData('national_id', e.target.value)}
                            />
                        </CardContent>
                    </Card>

                    {/* Contact Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Contact Information</CardTitle>
                        </CardHeader>
                        <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Input
                                label="Phone"
                                type="tel"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                            />
                            <Input
                                label="Email"
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                            />
                            <div className="md:col-span-2">
                                <Input
                                    label="Address"
                                    value={data.address}
                                    onChange={(e) => setData('address', e.target.value)}
                                />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Football Information */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Football Information</CardTitle>
                        </CardHeader>
                        <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Select
                                label="Registration Category *"
                                options={categoryOptions}
                                value={data.registration_category}
                                onChange={(e) => setData('registration_category', e.target.value)}
                                error={errors.registration_category}
                                placeholder="Select category"
                            />
                            <Select
                                label="Club"
                                options={clubOptions}
                                value={data.current_club_id}
                                onChange={(e) => setData('current_club_id', e.target.value)}
                                placeholder="Select club (optional)"
                            />
                            <Select
                                label="Primary Position"
                                options={positionOptions}
                                value={data.primary_position}
                                onChange={(e) => setData('primary_position', e.target.value)}
                                placeholder="Select position"
                            />
                            <Select
                                label="Secondary Position"
                                options={positionOptions}
                                value={data.secondary_position}
                                onChange={(e) => setData('secondary_position', e.target.value)}
                                placeholder="Select position"
                            />
                            <Select
                                label="Dominant Foot"
                                options={footOptions}
                                value={data.dominant_foot}
                                onChange={(e) => setData('dominant_foot', e.target.value)}
                                placeholder="Select foot"
                            />
                            <Input
                                label="Height (cm)"
                                type="number"
                                value={data.height_cm}
                                onChange={(e) => setData('height_cm', e.target.value)}
                            />
                            <Input
                                label="Weight (kg)"
                                type="number"
                                value={data.weight_kg}
                                onChange={(e) => setData('weight_kg', e.target.value)}
                            />
                        </CardContent>
                    </Card>

                    {/* Actions */}
                    <div className="flex justify-end gap-4">
                        <Button type="button" variant="outline" href="/players">
                            Cancel
                        </Button>
                        <Button type="submit" loading={processing}>
                            Create Player
                        </Button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
