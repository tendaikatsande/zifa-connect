import { PropsWithChildren, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Home,
    Users,
    Building2,
    ArrowLeftRight,
    Trophy,
    FileText,
    Settings,
    ChevronDown,
    Menu,
    X,
    Bell,
    CircleUser,
    Heart,
} from 'lucide-react';

interface NavItem {
    name: string;
    href: string;
    icon: React.ComponentType<{ className?: string }>;
    children?: { name: string; href: string }[];
}

const navigation: NavItem[] = [
    { name: 'Dashboard', href: '/dashboard', icon: Home },
    {
        name: 'Players',
        href: '/players',
        icon: Users,
        children: [
            { name: 'All Players', href: '/players' },
            { name: 'Register Player', href: '/players/create' },
            { name: 'Pending Approval', href: '/players?status=under_review' },
        ],
    },
    {
        name: 'Clubs',
        href: '/clubs',
        icon: Building2,
        children: [
            { name: 'All Clubs', href: '/clubs' },
            { name: 'Register Club', href: '/clubs/create' },
            { name: 'Affiliations', href: '/clubs?tab=affiliations' },
        ],
    },
    {
        name: 'Transfers',
        href: '/transfers',
        icon: ArrowLeftRight,
        children: [
            { name: 'All Transfers', href: '/transfers' },
            { name: 'New Transfer', href: '/transfers/create' },
            { name: 'Pending', href: '/transfers?status=pending' },
        ],
    },
    {
        name: 'Competitions',
        href: '/competitions',
        icon: Trophy,
        children: [
            { name: 'All Competitions', href: '/competitions' },
            { name: 'Fixtures', href: '/competitions?tab=fixtures' },
            { name: 'Results', href: '/competitions?tab=results' },
        ],
    },
    {
        name: 'Invoices',
        href: '/invoices',
        icon: FileText,
        children: [
            { name: 'All Invoices', href: '/invoices' },
            { name: 'Pending', href: '/invoices?status=pending' },
            { name: 'Overdue', href: '/invoices?status=overdue' },
        ],
    },
    {
        name: 'Fan Zone',
        href: '/fan',
        icon: Heart,
        children: [
            { name: 'News', href: '/fan/news' },
            { name: 'Polls', href: '/fan/polls' },
            { name: 'My Profile', href: '/fan/profile' },
            { name: 'Leaderboard', href: '/fan/leaderboard' },
        ],
    },
    { name: 'Settings', href: '/settings', icon: Settings },
];

export default function AuthenticatedLayout({ children }: PropsWithChildren) {
    const { auth } = usePage().props as any;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [expandedItems, setExpandedItems] = useState<string[]>([]);

    const toggleExpand = (name: string) => {
        setExpandedItems((prev) =>
            prev.includes(name)
                ? prev.filter((item) => item !== name)
                : [...prev, name]
        );
    };

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Mobile sidebar */}
            <div
                className={`fixed inset-0 z-50 lg:hidden ${sidebarOpen ? '' : 'hidden'}`}
            >
                <div
                    className="fixed inset-0 bg-gray-600 bg-opacity-75"
                    onClick={() => setSidebarOpen(false)}
                />
                <div className="fixed inset-y-0 left-0 flex w-64 flex-col bg-white">
                    <SidebarContent
                        expandedItems={expandedItems}
                        toggleExpand={toggleExpand}
                        onClose={() => setSidebarOpen(false)}
                    />
                </div>
            </div>

            {/* Desktop sidebar */}
            <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
                <SidebarContent
                    expandedItems={expandedItems}
                    toggleExpand={toggleExpand}
                />
            </div>

            {/* Main content */}
            <div className="lg:pl-64">
                {/* Top bar */}
                <div className="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        className="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <Menu className="h-6 w-6" />
                    </button>

                    <div className="flex flex-1 justify-end gap-x-4 lg:gap-x-6">
                        <button className="relative p-2 text-gray-400 hover:text-gray-500">
                            <Bell className="h-6 w-6" />
                            <span className="absolute right-1 top-1 h-2 w-2 rounded-full bg-red-500" />
                        </button>

                        <div className="flex items-center gap-x-4">
                            <span className="hidden lg:block text-sm font-medium text-gray-700">
                                {auth?.user?.name}
                            </span>
                            <CircleUser className="h-8 w-8 text-gray-400" />
                        </div>
                    </div>
                </div>

                <main className="py-6">
                    <div className="px-4 sm:px-6 lg:px-8">{children}</div>
                </main>
            </div>
        </div>
    );
}

function SidebarContent({
    expandedItems,
    toggleExpand,
    onClose,
}: {
    expandedItems: string[];
    toggleExpand: (name: string) => void;
    onClose?: () => void;
}) {
    return (
        <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-primary-700 px-6 pb-4">
            <div className="flex h-16 shrink-0 items-center justify-between">
                <Link href="/dashboard" className="flex items-center gap-2">
                    <div className="h-8 w-8 rounded-lg bg-white flex items-center justify-center">
                        <span className="text-primary-700 font-bold text-sm">Z</span>
                    </div>
                    <span className="text-xl font-bold text-white">ZIFA Connect</span>
                </Link>
                {onClose && (
                    <button onClick={onClose} className="lg:hidden text-white">
                        <X className="h-6 w-6" />
                    </button>
                )}
            </div>

            <nav className="flex flex-1 flex-col">
                <ul className="flex flex-1 flex-col gap-y-1">
                    {navigation.map((item) => (
                        <li key={item.name}>
                            {item.children ? (
                                <div>
                                    <button
                                        onClick={() => toggleExpand(item.name)}
                                        className="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-primary-100 hover:bg-primary-600 hover:text-white"
                                    >
                                        <item.icon className="h-5 w-5 shrink-0" />
                                        {item.name}
                                        <ChevronDown
                                            className={`ml-auto h-4 w-4 transition-transform ${
                                                expandedItems.includes(item.name)
                                                    ? 'rotate-180'
                                                    : ''
                                            }`}
                                        />
                                    </button>
                                    {expandedItems.includes(item.name) && (
                                        <ul className="mt-1 pl-9">
                                            {item.children.map((child) => (
                                                <li key={child.name}>
                                                    <Link
                                                        href={child.href}
                                                        className="block rounded-md py-2 pr-2 text-sm text-primary-200 hover:text-white"
                                                    >
                                                        {child.name}
                                                    </Link>
                                                </li>
                                            ))}
                                        </ul>
                                    )}
                                </div>
                            ) : (
                                <Link
                                    href={item.href}
                                    className="group flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-primary-100 hover:bg-primary-600 hover:text-white"
                                >
                                    <item.icon className="h-5 w-5 shrink-0" />
                                    {item.name}
                                </Link>
                            )}
                        </li>
                    ))}
                </ul>
            </nav>
        </div>
    );
}
