import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

type OrderSummary = {
    id: number;
    status: string;
    total: string;
};

export default function Success({ order }: { order: OrderSummary | null }) {
    const paid = order?.status === "paid";

    return (
        <MainLayout>
            <Head title="Thanks for your order" />

            <div className="card bg-base-100 shadow-sm">
                <div className="card-body">
                    <h1 className="card-title text-2xl">Thanks for your order!</h1>

                    {order ? (
                        <>
                            <p>Order #{order.id}</p>
                            <p>Total: ${order.total}</p>
                            <div className={`badge ${paid ? "badge-success" : "badge-warning"} w-fit`}>
                                {paid ? "Paid" : "Confirming payment..."}
                            </div>
                            {!paid && (
                                <p className="text-sm text-base-content/70">
                                    Payment confirmation can take a few seconds. Refresh this page to check again.
                                </p>
                            )}
                        </>
                    ) : (
                        <p>We couldn't find that order. If you were charged, contact support.</p>
                    )}

                    <Link href={route("products.index")} className="btn btn-primary btn-sm w-fit">
                        Keep shopping
                    </Link>
                </div>
            </div>
        </MainLayout>
    );
}
