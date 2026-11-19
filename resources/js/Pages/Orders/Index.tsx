import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Pagination from "@/Components/Pagination";

type OrderRow = {
    reference: string;
    status: string;
    total: string;
    placed_at: string;
    items_count: number;
};

type PageLink = { url: string | null; label: string; active: boolean };
type Paginated = { data: OrderRow[]; links: PageLink[] };

export default function Index({ orders }: { orders: Paginated }) {
    return (
        <MainLayout>
            <Head title="My orders" />

            <h1 className="mb-6 text-3xl font-bold">My orders</h1>

            {orders.data.length === 0 ? (
                <div className="card bg-base-100 shadow-sm">
                    <div className="card-body">
                        <p>You haven't placed any orders yet.</p>
                        <Link href={route("products.index")} className="btn btn-primary btn-sm w-fit">
                            Start shopping
                        </Link>
                    </div>
                </div>
            ) : (
                <div className="overflow-x-auto rounded-box bg-base-100 shadow-sm">
                    <table className="table">
                        <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        {orders.data.map((order) => (
                            <tr key={order.reference}>
                                <td className="font-mono">{order.reference}</td>
                                <td>{order.placed_at}</td>
                                <td>{order.items_count}</td>
                                <td>${order.total}</td>
                                <td>
                                    <span className="badge badge-success capitalize">{order.status}</span>
                                </td>
                                <td className="text-right">
                                    <Link
                                        href={route("orders.show", order.reference)}
                                        className="btn btn-outline btn-sm"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
            )}

            <Pagination links={orders.links} />
        </MainLayout>
    );
}
