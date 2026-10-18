export type User = {
    id: number;
    name: string;
    email: string;
    role?: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Tenant = {
    id: string;
    name: string;
    short_name?: string;
    domain?: string;
    staff_theme_color?: string;
    user_theme_color?: string;
};

export type Auth = {
    user: User;
    staff?: User | null;
    web?: User | null;
    tenant?: Tenant | null;
};
