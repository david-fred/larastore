import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

type CartItem = {
    id: number;
    title: string;
    slug: string;
    price: number;
    quantity: number;
    stock: number;
    image: string | null;
    line_total: number;
};

export default function Index({ items, subtotal }: { items: CartItem[]; subtotal: number }) {
    const changeQuantity = (item: CartItem, quantity: number) => {
        router.patch(route("cart.update", item.id), { quantity }, { preserveScroll: true });
    };

    const remove = (item: CartItem) => {
        router.delete(route("cart.destroy", item.id), { preserveScroll: true });
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
                            <div key={item.id} className="card card-side bg-base-100 shadow-sm">
                                <figure className="w-28 shrink-0 bg-base-200">
                                    {item.image && (
                                        <img src={item.image} alt={item.title} className="h-full w-full object-cover" />
                                    )}
                                </figure>

                                <div className="card-body flex-row flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <Link
                                            href={route("products.show", item.slug)}
                                            className="font-semibold hover:text-primary"
                                        >
                                            {item.title}
                                        </Link>
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
                            <button type="button" className="btn btn-primary mt-4" disabled>
                                Checkout (next chapter)
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </MainLayout>
    );
}
