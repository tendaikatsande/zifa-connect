import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import Button from '@/Components/ui/Button';
import Input from '@/Components/ui/Input';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/Card';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <>
            <Head title="Login" />

            <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                <div className="max-w-md w-full space-y-8">
                    <div className="text-center">
                        <div className="mx-auto h-12 w-12 bg-primary-600 rounded-lg flex items-center justify-center">
                            <span className="text-white font-bold text-xl">Z</span>
                        </div>
                        <h2 className="mt-6 text-3xl font-bold text-gray-900">
                            ZIFA Connect
                        </h2>
                        <p className="mt-2 text-sm text-gray-600">
                            Sign in to your account
                        </p>
                    </div>

                    <Card>
                        <CardContent className="p-6">
                            <form onSubmit={handleSubmit} className="space-y-4">
                                <Input
                                    label="Email address"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    error={errors.email}
                                    required
                                    autoComplete="email"
                                />

                                <Input
                                    label="Password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    error={errors.password}
                                    required
                                    autoComplete="current-password"
                                />

                                <div className="flex items-center justify-between">
                                    <div className="flex items-center">
                                        <input
                                            id="remember"
                                            type="checkbox"
                                            checked={data.remember}
                                            onChange={(e) => setData('remember', e.target.checked)}
                                            className="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                        />
                                        <label htmlFor="remember" className="ml-2 block text-sm text-gray-900">
                                            Remember me
                                        </label>
                                    </div>

                                    <Link
                                        href="/forgot-password"
                                        className="text-sm font-medium text-primary-600 hover:text-primary-500"
                                    >
                                        Forgot password?
                                    </Link>
                                </div>

                                <Button type="submit" className="w-full" loading={processing}>
                                    Sign in
                                </Button>
                            </form>
                        </CardContent>
                    </Card>

                    <p className="text-center text-sm text-gray-600">
                        Don't have an account?{' '}
                        <Link
                            href="/register"
                            className="font-medium text-primary-600 hover:text-primary-500"
                        >
                            Register
                        </Link>
                    </p>
                </div>
            </div>
        </>
    );
}
