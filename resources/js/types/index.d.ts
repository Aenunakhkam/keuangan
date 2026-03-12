export interface User {
    id: number;
    name: string;
    email?: string;
    email_verified_at?: string;
    roles: string[];
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    settings: Record<string, string>;
    flash: {
        success: string | null;
        error: string | null;
    };
};
