import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

export default function Success({ sessionId }: { sessionId: string | null }) {
    return (
        <MainLayout>
            <Head title="Thanks for your order" />

            <div className="card bg-base-100 shadow-sm">
                <div className="card-body">
                    <h1 className="card-title text-2xl">Thanks for your order</h1>
                    <p>Your payment is being confirmed. Order status updates come in the next step.</p>
                    {sessionId && (
                        <p className="text-xs text-base-content/50">Session: {sessionId}</p>
                    )}
                    <Link href={route("products.index")} className="btn btn-primary btn-sm w-fit">
                        Keep shopping
                    </Link>
                </div>
            </div>
        </MainLayout>
    );
}
