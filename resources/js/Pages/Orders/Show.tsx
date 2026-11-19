import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

type OrderItem = {
    title: string;
    label: string;
    price: string;
    quantity: number;
    line_total: number;
};

type OrderDetail = {
    reference: string;
    status: string;
    total: string;
    placed_at: string;
    paid_at: string | null;
    items: OrderItem[];
};

export default function Show({ order }: { order: OrderDetail }) {
    const paid = order.status === "paid";

    return (
        <MainLayout>
            <Head title={`Order ${order.reference}`} />

            <nav className="mb-4 flex flex-wrap items-center gap-2 text-sm text-base-content/70">
                <Link href={route("orders.index")} className="hover:text-primary">
                    My orders
                </Link>
                <span aria-hidden="true">›</span>
                <span className="text-base-content">{order.reference}</span>
            </nav>

            <div className="mb-8 flex flex-wrap items-center gap-4">
                <h1 className="text-3xl font-bold font-mono">{order.reference}</h1>
                <span className={`badge ${paid ? "badge-success" : "badge-warning"} capitalize`}>
                    {order.status}
                </span>
            </div>

            <ul className="steps steps-horizontal mb-10 w-full">
                <li className="step step-primary">
                    <span className="block">Order placed</span>
                    <span className="block text-xs text-base-content/60">{order.placed_at}</span>
                </li>
                <li className={`step ${paid ? "step-primary" : ""}`}>
                    <span className="block">Payment confirmed</span>
                    {order.paid_at && (
                        <span className="block text-xs text-base-content/60">{order.paid_at}</span>
                    )}
                </li>
            </ul>

            <div className="grid gap-8 lg:grid-cols-3">
                <div className="card bg-base-100 shadow-sm lg:col-span-2">
                    <div className="card-body">
                        <h2 className="card-title">Items</h2>

                        <div className="divide-y divide-base-200">
                            {order.items.map((item, index) => (
                                <div key={index} className="flex items-center justify-between gap-4 py-4">
                                    <div>
                                        <p className="font-semibold">{item.title}</p>
                                        {item.label !== "Default" && (
                                            <p className="text-sm text-base-content/70">{item.label}</p>
                                        )}
                                        <p className="text-sm text-base-content/70">
                                            {item.quantity} × ${Number(item.price).toFixed(2)}
                                        </p>
                                    </div>
                                    <p className="font-semibold">${item.line_total.toFixed(2)}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                <div className="card h-fit bg-base-100 shadow-sm">
                    <div className="card-body">
                        <h2 className="card-title">Summary</h2>
                        <div className="flex justify-between">
                            <span>Total paid</span>
                            <span className="font-semibold">${order.total}</span>
                        </div>
                        <Link href={route("orders.index")} className="btn btn-outline btn-sm mt-4">
                            Back to my orders
                        </Link>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
