import { Head } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

export default function Welcome() {
    return (
        <MainLayout>
            <Head title="Welcome" />
            <div className="hero min-h-[60vh] rounded-box bg-base-100">
                <div className="hero-content text-center">
                    <div className="max-w-md">
                        <h1 className="text-5xl font-bold">Welcome to Larastore</h1>
                        <p className="py-6">
                            A multi-vendor marketplace built with Laravel and React.
                        </p>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
