import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    const urlToCheckPath = toUrl(urlToCheck);

    // Extract pathname from current URL (remove query parameters)
    const currentPath = currentUrl.split('?')[0];
    const checkPath = urlToCheckPath.split('?')[0];

    return currentPath === checkPath || currentUrl.startsWith(urlToCheckPath);
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function ucFirst(str: string) {
    return str[0].toUpperCase() + str.slice(1);
}
