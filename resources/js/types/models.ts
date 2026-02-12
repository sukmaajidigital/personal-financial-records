export type Category = {
    id: number;
    name: string;
    color: string;
    transactions_count?: number;
    created_at: string;
    updated_at: string;
};

export type Transaction = {
    id: number;
    user_id: number;
    category_id: number;
    description: string;
    amount: string;
    type: 'income' | 'expense';
    date: string;
    category?: Pick<Category, 'id' | 'name' | 'color'>;
    created_at: string;
    updated_at: string;
};

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
};

export type DashboardSummary = {
    totalIncome: number;
    totalExpense: number;
    balance: number;
    totalTransactions: number;
};

export type MonthlyTrend = {
    month: string;
    income: number;
    expense: number;
};

export type ExpenseByCategory = {
    name: string;
    color: string;
    total: number;
};

export type TransactionFilters = {
    search?: string;
    type?: string;
    category_id?: string;
    date_from?: string;
    date_to?: string;
};
