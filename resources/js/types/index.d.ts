import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface PaginatedData<T> {
    data: T[];
    links: {
        first: string;
        last: string;
        next?: string;
        prev?: string;
    };
    meta: {
        current_page: number;
        from?: number;
        last_page: number;
        links: {
            active: boolean;
            label: string;
            url?: string;
        }[];
        path: string;
        per_page: number;
        to?: number;
        total: number;
    };
}

export interface Subscriber {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    phone: string;
    unsubscribed_at: string | null;
    is_subscribed: boolean;
    created_at: string;
    updated_at: string;
}
