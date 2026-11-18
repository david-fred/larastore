import { Head, Link, router, usePage } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import { PageProps } from "@/types";

type CartItem = {
    variant_id: number;
    product_slug: string;
    title: string;
    label: string;
    price: number;
    quantity: number;
    stock: number;
    image: string | null;
    line_total: number;
};

export default function Index({ items, subtotal }: { items: CartItem[]; subtotal: number }) {
    const { errors } = usePage<PageProps & { errors: Record<string, string> }>().props;

    const changeQuantity = (item: CartItem, quantity: number) => {
        router.patch(route("cart.update", item.variant_id), { quantity }, { preserveScroll: true });
    };

    const remove = (item: CartItem) => {
        router.delete(route("cart.destroy", item.variant_id), { preserveScroll: true });
    };

    return (
        <MainLayout>
            <Head title="Cart" />

            <h1 className="mb-6 text-3xl font-bold">Your cart</h1>

            {items.length === 0 ? (
                <div className="card bg-base-100 shadow-sm">
                    <div className="card-body">
                        <p>Your cart is empty.</p>
                        <Link href={route("products.index")} className="btn btn-primary btn-sm w-fit">
                            Browse products
                        </Link>
                    </div>
                </div>
            ) : (
                <div className="grid gap-8 lg:grid-cols-3">
                    <div className="space-y-4 lg:col-span-2">
                        {items.map((item) => (
                            <div key={item.variant_id} className="card card-side bg-base-100 shadow-sm">
                                <figure className="w-28 shrink-0 bg-base-200">
                                    {item.image && (
                                        <img src={item.image} alt={item.title} className="h-full w-full object-cover" />
                                    )}
                                </figure>

                                <div className="card-body flex-row flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <Link
                                            href={route("products.show", item.product_slug)}
                                            className="font-semibold hover:text-primary"
                                        >
                                            {item.title}
                                        </Link>
                                        {item.label !== "Default" && (
                                            <p className="text-sm text-base-content/70">{item.label}</p>
                                        )}
                                        <p className="text-sm text-base-content/70">
                                            ${item.price.toFixed(2)} each
                                        </p>
                                    </div>

                                    <div className="flex items-center gap-3">
                                        <div className="join">
                                            <button
                                                type="button"
                                                className="btn btn-sm join-item"
                                                onClick={() => changeQuantity(item, item.quantity - 1)}
                                            >
                                                −
                                            </button>
                                            <span className="btn btn-sm join-item pointer-events-none">
                                                {item.quantity}
                                            </span>
                                            <button
                                                type="button"
                                                className="btn btn-sm join-item"
                                                disabled={item.quantity >= item.stock}
                                                onClick={() => changeQuantity(item, item.quantity + 1)}
                                            >
                                                +
                                            </button>
                                        </div>

                                        <p className="w-24 text-right font-semibold">
                                            ${item.line_total.toFixed(2)}
                                        </p>

                                        <button
                                            type="button"
                                            className="btn btn-ghost btn-sm text-error"
                                            onClick={() => remove(item)}
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="card h-fit bg-base-100 shadow-sm">
                        <div className="card-body">
                            <h2 className="card-title">Summary</h2>
                            <div className="flex justify-between">
                                <span>Subtotal</span>
                                <span className="font-semibold">${subtotal.toFixed(2)}</span>
                            </div>

                            {errors.quantity && <p className="text-sm text-error">{errors.quantity}</p>}
                            {errors.cart && <p className="text-sm text-error">{errors.cart}</p>}

                            <button
                                type="button"
                                className="btn btn-primary mt-4"
                                onClick={() => router.post(route("checkout.store"))}
                            >
                                Checkout
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
