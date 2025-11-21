import { Head, Link } from '@inertiajs/react';

export default function Welcome() {
    return (
        <>
            <Head title="Welcome" />

            <div className="min-h-screen bg-gradient-to-br from-primary-600 to-primary-800">
                <div className="container mx-auto px-4 py-16">
                    <div className="text-center">
                        <h1 className="text-5xl font-bold text-white mb-4">
                            ZIFA Connect
                        </h1>
                        <p className="text-xl text-primary-100 mb-8">
                            Zimbabwe Football Association Registration & Management Platform
                        </p>

                        <div className="flex justify-center gap-4">
                            <Link
                                href="/login"
                                className="btn-primary px-8 py-3 text-lg"
                            >
                                Login
                            </Link>
                            <Link
                                href="/register"
                                className="btn bg-white text-primary-600 hover:bg-primary-50 px-8 py-3 text-lg"
                            >
                                Register
                            </Link>
                        </div>
                    </div>

                    <div className="mt-16 grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                        <div className="card p-6 text-center">
                            <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg className="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 className="font-semibold text-gray-900 mb-2">Player Registration</h3>
                            <p className="text-sm text-gray-600">
                                Register players across all age categories with document verification
                            </p>
                        </div>

                        <div className="card p-6 text-center">
                            <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg className="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 className="font-semibold text-gray-900 mb-2">Club Management</h3>
                            <p className="text-sm text-gray-600">
                                Annual affiliations, roster management, and compliance tracking
                            </p>
                        </div>

                        <div className="card p-6 text-center">
                            <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg className="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                            <h3 className="font-semibold text-gray-900 mb-2">Transfer System</h3>
                            <p className="text-sm text-gray-600">
                                Local and international transfers with FIFA Connect integration
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
