import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/Card';
import { Button } from '@/Components/ui/Button';
import { Badge } from '@/Components/ui/Badge';
import {
    Newspaper,
    Eye,
    MessageCircle,
    Calendar,
    Star,
    Pin,
    Plus,
} from 'lucide-react';

interface NewsArticle {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    category: string;
    status: string;
    is_featured: boolean;
    is_pinned: boolean;
    views_count: number;
    published_at: string;
    club?: { name: string };
}

interface FanNewsIndexProps {
    news?: {
        data: NewsArticle[];
        current_page: number;
        last_page: number;
    };
}

const categoryColors: Record<string, string> = {
    announcement: 'bg-blue-100 text-blue-800',
    match_preview: 'bg-purple-100 text-purple-800',
    match_report: 'bg-green-100 text-green-800',
    transfer: 'bg-orange-100 text-orange-800',
    interview: 'bg-pink-100 text-pink-800',
    general: 'bg-gray-100 text-gray-800',
};

export default function FanNewsIndex({ news }: FanNewsIndexProps) {
    const articles = news?.data || [];

    return (
        <AuthenticatedLayout>
            <Head title="Fan News" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-900 flex items-center gap-2">
                            <Newspaper className="h-6 w-6" />
                            Fan News
                        </h1>
                        <p className="text-sm text-gray-500 mt-1">
                            Latest news and updates for fans
                        </p>
                    </div>
                    <Button as={Link} href="/fan/news/create">
                        <Plus className="h-4 w-4 mr-2" />
                        Create Article
                    </Button>
                </div>

                {articles.length === 0 ? (
                    <Card>
                        <CardContent className="py-12 text-center">
                            <Newspaper className="h-12 w-12 mx-auto text-gray-400 mb-4" />
                            <p className="text-gray-500">No news articles yet.</p>
                            <Button as={Link} href="/fan/news/create" className="mt-4">
                                Create First Article
                            </Button>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-6">
                        {articles.map((article) => (
                            <Card key={article.id} className="hover:shadow-md transition-shadow">
                                <CardContent className="p-6">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <div className="flex items-center gap-2 mb-2">
                                                {article.is_pinned && (
                                                    <Pin className="h-4 w-4 text-red-500" />
                                                )}
                                                {article.is_featured && (
                                                    <Star className="h-4 w-4 text-yellow-500 fill-yellow-500" />
                                                )}
                                                <Badge className={categoryColors[article.category] || categoryColors.general}>
                                                    {article.category.replace('_', ' ')}
                                                </Badge>
                                            </div>
                                            <Link
                                                href={`/fan/news/${article.id}`}
                                                className="text-lg font-semibold text-gray-900 hover:text-primary-600"
                                            >
                                                {article.title}
                                            </Link>
                                            <p className="text-sm text-gray-600 mt-2 line-clamp-2">
                                                {article.excerpt}
                                            </p>
                                            <div className="flex items-center gap-4 mt-4 text-sm text-gray-500">
                                                <span className="flex items-center gap-1">
                                                    <Eye className="h-4 w-4" />
                                                    {article.views_count}
                                                </span>
                                                <span className="flex items-center gap-1">
                                                    <Calendar className="h-4 w-4" />
                                                    {new Date(article.published_at).toLocaleDateString()}
                                                </span>
                                                {article.club && (
                                                    <span>{article.club.name}</span>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
