import { Head, Link } from '@inertiajs/react';
import {
    UsersIcon,
    BuildingOfficeIcon,
    ArrowsRightLeftIcon,
    TrophyIcon,
    ShieldCheckIcon,
    CurrencyDollarIcon,
    GlobeAltIcon,
    DocumentCheckIcon,
    ChartBarIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';

export default function Welcome() {
    const features = [
        {
            name: 'Player Registration',
            description: 'Digital registration for players across all age categories with document verification and medical tracking.',
            icon: UsersIcon,
        },
        {
            name: 'Club Management',
            description: 'Complete club lifecycle management including annual affiliations, roster control, and compliance monitoring.',
            icon: BuildingOfficeIcon,
        },
        {
            name: 'Transfer System',
            description: 'Streamlined local and international transfers with automated workflows and FIFA TMS integration.',
            icon: ArrowsRightLeftIcon,
        },
        {
            name: 'Competition Management',
            description: 'Organize leagues, cups, and tournaments with fixture generation, results tracking, and standings.',
            icon: TrophyIcon,
        },
        {
            name: 'Officials & Referees',
            description: 'License management, training courses, and match assignments for coaches and referees.',
            icon: ShieldCheckIcon,
        },
        {
            name: 'Payment Processing',
            description: 'Integrated PesePay payments for registrations, affiliations, transfers, and fines.',
            icon: CurrencyDollarIcon,
        },
    ];

    const stats = [
        { label: 'Registered Players', value: '50,000+' },
        { label: 'Active Clubs', value: '600+' },
        { label: 'Annual Transfers', value: '5,000+' },
        { label: 'Competitions', value: '50+' },
    ];

    return (
        <>
            <Head title="Welcome" />

            <div className="min-h-screen bg-white">
                {/* Navigation */}
                <nav className="bg-white border-b border-gray-100">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between h-16">
                            <div className="flex items-center">
                                <div className="flex items-center gap-3">
                                    <div className="h-10 w-10 bg-primary-600 rounded-lg flex items-center justify-center">
                                        <span className="text-white font-bold text-lg">Z</span>
                                    </div>
                                    <div>
                                        <span className="text-xl font-bold text-gray-900">ZIFA</span>
                                        <span className="text-xl font-light text-primary-600"> Connect</span>
                                    </div>
                                </div>
                            </div>
                            <div className="flex items-center gap-4">
                                <Link
                                    href="/login"
                                    className="text-gray-600 hover:text-gray-900 font-medium"
                                >
                                    Sign In
                                </Link>
                                <Link
                                    href="/register"
                                    className="bg-primary-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-primary-700 transition-colors"
                                >
                                    Get Started
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <div className="relative overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900" />
                    <div className="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-50" />

                    <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
                        <div className="text-center">
                            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-white tracking-tight">
                                Zimbabwe Football
                                <span className="block text-secondary-400">Registration Platform</span>
                            </h1>
                            <p className="mt-6 text-lg sm:text-xl text-primary-100 max-w-3xl mx-auto">
                                The official digital platform for ZIFA to register and manage players, clubs,
                                officials, transfers, and competitions. Integrated with FIFA Connect for
                                international compliance.
                            </p>
                            <div className="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                                <Link
                                    href="/register"
                                    className="inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-primary-700 bg-white rounded-lg hover:bg-gray-50 transition-colors shadow-lg"
                                >
                                    Register Your Club
                                </Link>
                                <Link
                                    href="/login"
                                    className="inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-white border-2 border-white/30 rounded-lg hover:bg-white/10 transition-colors"
                                >
                                    Sign In to Dashboard
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Wave divider */}
                    <div className="absolute bottom-0 left-0 right-0">
                        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
                        </svg>
                    </div>
                </div>

                {/* Stats Section */}
                <div className="bg-white py-12 -mt-1">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
                            {stats.map((stat) => (
                                <div key={stat.label} className="text-center">
                                    <div className="text-3xl sm:text-4xl font-bold text-primary-600">
                                        {stat.value}
                                    </div>
                                    <div className="mt-1 text-sm text-gray-500 font-medium">
                                        {stat.label}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Features Section */}
                <div className="py-20 bg-gray-50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl sm:text-4xl font-bold text-gray-900">
                                Everything You Need to Manage Football
                            </h2>
                            <p className="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                                A comprehensive platform designed for modern football administration
                            </p>
                        </div>

                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {features.map((feature) => (
                                <div
                                    key={feature.name}
                                    className="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
                                >
                                    <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                                        <feature.icon className="h-6 w-6 text-primary-600" />
                                    </div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                        {feature.name}
                                    </h3>
                                    <p className="text-gray-600 text-sm leading-relaxed">
                                        {feature.description}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Integration Section */}
                <div className="py-20 bg-white">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                            <div>
                                <h2 className="text-3xl sm:text-4xl font-bold text-gray-900">
                                    FIFA Connect Integration
                                </h2>
                                <p className="mt-4 text-lg text-gray-600">
                                    Seamlessly sync player and club data with FIFA's global football
                                    management system. Ensure compliance with international transfer
                                    regulations and maintain accurate records.
                                </p>
                                <ul className="mt-8 space-y-4">
                                    {[
                                        'Automatic player ID synchronization',
                                        'International Transfer Matching System (ITMS)',
                                        'Real-time data validation',
                                        'Compliance with FIFA regulations',
                                    ].map((item) => (
                                        <li key={item} className="flex items-start gap-3">
                                            <div className="flex-shrink-0 w-5 h-5 bg-primary-100 rounded-full flex items-center justify-center mt-0.5">
                                                <svg className="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                                </svg>
                                            </div>
                                            <span className="text-gray-600">{item}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            <div className="mt-12 lg:mt-0">
                                <div className="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8 border border-gray-200">
                                    <div className="grid grid-cols-2 gap-6">
                                        <div className="bg-white rounded-lg p-4 shadow-sm">
                                            <GlobeAltIcon className="h-8 w-8 text-primary-600 mb-2" />
                                            <div className="text-sm font-medium text-gray-900">Global Sync</div>
                                        </div>
                                        <div className="bg-white rounded-lg p-4 shadow-sm">
                                            <DocumentCheckIcon className="h-8 w-8 text-primary-600 mb-2" />
                                            <div className="text-sm font-medium text-gray-900">Compliance</div>
                                        </div>
                                        <div className="bg-white rounded-lg p-4 shadow-sm">
                                            <ChartBarIcon className="h-8 w-8 text-primary-600 mb-2" />
                                            <div className="text-sm font-medium text-gray-900">Analytics</div>
                                        </div>
                                        <div className="bg-white rounded-lg p-4 shadow-sm">
                                            <UserGroupIcon className="h-8 w-8 text-primary-600 mb-2" />
                                            <div className="text-sm font-medium text-gray-900">Identity</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* CTA Section */}
                <div className="bg-primary-700">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                        <div className="text-center">
                            <h2 className="text-3xl font-bold text-white">
                                Ready to Get Started?
                            </h2>
                            <p className="mt-4 text-lg text-primary-100 max-w-2xl mx-auto">
                                Join hundreds of clubs already using ZIFA Connect to manage their
                                football operations efficiently.
                            </p>
                            <div className="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                                <Link
                                    href="/register"
                                    className="inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-primary-700 bg-white rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    Create Account
                                </Link>
                                <Link
                                    href="mailto:support@zifa.org.zw"
                                    className="inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-white border-2 border-white/30 rounded-lg hover:bg-white/10 transition-colors"
                                >
                                    Contact Support
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <footer className="bg-gray-900">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
                            <div className="col-span-2 md:col-span-1">
                                <div className="flex items-center gap-2">
                                    <div className="h-8 w-8 bg-primary-600 rounded-lg flex items-center justify-center">
                                        <span className="text-white font-bold">Z</span>
                                    </div>
                                    <span className="text-white font-semibold">ZIFA Connect</span>
                                </div>
                                <p className="mt-4 text-sm text-gray-400">
                                    Official registration platform of the Zimbabwe Football Association.
                                </p>
                            </div>
                            <div>
                                <h3 className="text-sm font-semibold text-white mb-4">Platform</h3>
                                <ul className="space-y-2">
                                    <li><Link href="/players" className="text-sm text-gray-400 hover:text-white">Players</Link></li>
                                    <li><Link href="/clubs" className="text-sm text-gray-400 hover:text-white">Clubs</Link></li>
                                    <li><Link href="/transfers" className="text-sm text-gray-400 hover:text-white">Transfers</Link></li>
                                    <li><Link href="/competitions" className="text-sm text-gray-400 hover:text-white">Competitions</Link></li>
                                </ul>
                            </div>
                            <div>
                                <h3 className="text-sm font-semibold text-white mb-4">Support</h3>
                                <ul className="space-y-2">
                                    <li><a href="#" className="text-sm text-gray-400 hover:text-white">Help Center</a></li>
                                    <li><a href="#" className="text-sm text-gray-400 hover:text-white">Documentation</a></li>
                                    <li><a href="#" className="text-sm text-gray-400 hover:text-white">Contact Us</a></li>
                                </ul>
                            </div>
                            <div>
                                <h3 className="text-sm font-semibold text-white mb-4">Legal</h3>
                                <ul className="space-y-2">
                                    <li><a href="#" className="text-sm text-gray-400 hover:text-white">Privacy Policy</a></li>
                                    <li><a href="#" className="text-sm text-gray-400 hover:text-white">Terms of Service</a></li>
                                </ul>
                            </div>
                        </div>
                        <div className="mt-12 pt-8 border-t border-gray-800">
                            <p className="text-sm text-gray-400 text-center">
                                &copy; {new Date().getFullYear()} Zimbabwe Football Association. All rights reserved.
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
