import { Head, Link, router } from "@inertiajs/react";
import { useEffect } from "react";
import MainLayout from "@/Layouts/MainLayout";

type OrderSummary = {
    reference: string;
    status: string;
    total: string;
};

export default function Success({ order }: { order: OrderSummary | null }) {
    const paid = order?.status === "paid";

    useEffect(() => {
        if (!order || paid) {
            return;
        }

        // The webhook marks the order paid a few seconds after checkout, so check for an update
        let attempts = 0;
        const timer = window.setInterval(() => {
            attempts++;

            if (attempts > 15) {
                window.clearInterval(timer);
                return;
            }

            router.reload({ only: ["order"] });
        }, 2000);

        return () => window.clearInterval(timer);
    }, [order?.status]);

    return (
        <MainLayout>
            <Head title="Thanks for your order" />

            <div className="card bg-base-100 shadow-sm">
                <div className="card-body">
                    <h1 className="card-title text-2xl">Thanks for your order!</h1>

                    {order ? (
                        <>
                            <p>Order {order.reference}</p>
                            <p>Total: ${order.total}</p>
                            <div className={`badge ${paid ? "badge-success" : "badge-warning"} w-fit`}>
                                {paid ? "Paid" : "Confirming payment..."}
                            </div>
                            {!paid && (
                                <p className="text-sm text-base-content/70">
                                    This page updates automatically. It can take up to a minute.
                                </p>
                            )}
                        </>
                    ) : (
                        <p>We couldn't find that order. If you were charged, contact support.</p>
                    )}

                    {paid && (
                        <Link href={route("orders.show", order.reference)} className="btn btn-outline btn-sm w-fit">
                            View order
                        </Link>
                    )}

                    <Link href={route("products.index")} className="btn btn-primary btn-sm w-fit">
                        Keep shopping
                    </Link>
                </div>
            </div>
        </MainLayout>
    );
}
