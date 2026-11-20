import { Head, useForm } from "@inertiajs/react";
import { FormEvent } from "react";
import MainLayout from "@/Layouts/MainLayout";

type VendorStatus = {
    store_name: string;
    status: string;
};

export default function Apply({ vendor }: { vendor: VendorStatus | null }) {
    const form = useForm({
        store_name: "",
        description: "",
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        form.post(route("vendor.apply.store"));
    };

    return (
        <MainLayout>
            <Head title="Sell on Larastore" />

            <div className="mx-auto max-w-2xl">
                <h1 className="mb-6 text-3xl font-bold">Sell on Larastore</h1>

                {vendor?.status === "pending" && (
                    <div className="alert alert-info">
                        <span>Your application for {vendor.store_name} is under review.</span>
                    </div>
                )}

                {vendor?.status === "approved" && (
                    <div className="alert alert-success">
                        <span>You're approved to sell as {vendor.store_name}. Seller tools are coming next.</span>
                    </div>
                )}

                {vendor?.status === "suspended" && (
                    <div className="alert alert-error">
                        <span>Your seller account is suspended. Contact support for help.</span>
                    </div>
                )}

                {!vendor && (
                    <form onSubmit={submit} className="card bg-base-100 shadow-sm">
                        <div className="card-body space-y-5">
                            <p className="text-base-content/70">
                                Tell us about your store. An admin reviews every application before you can list products.
                            </p>

                            <div className="space-y-2">
                                <label htmlFor="store_name" className="font-semibold">
                                    Store name
                                </label>
                                <input
                                    id="store_name"
                                    type="text"
                                    className="input w-full"
                                    value={form.data.store_name}
                                    onChange={(e) => form.setData("store_name", e.target.value)}
                                    required
                                />
                                {form.errors.store_name && (
                                    <p className="text-sm text-error">{form.errors.store_name}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <label htmlFor="description" className="font-semibold">
                                    What do you sell?
                                </label>
                                <textarea
                                    id="description"
                                    className="textarea w-full"
                                    rows={4}
                                    value={form.data.description}
                                    onChange={(e) => form.setData("description", e.target.value)}
                                />
                                {form.errors.description && (
                                    <p className="text-sm text-error">{form.errors.description}</p>
                                )}
                            </div>

                            {form.errors.vendor && (
                                <p className="text-sm text-error">{form.errors.vendor}</p>
                            )}

                            <button type="submit" className="btn btn-primary" disabled={form.processing}>
                                Submit application
                            </button>
                        </div>
                    </form>
                )}
            </div>
        </MainLayout>
    );
}
