import { Head, Link } from "@inertiajs/react";
import { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";

type ProductDetail = {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    price: string;
    quantity: number;
    category: string;
    images: string[];
};

export default function Show({ product }: { product: ProductDetail }) {
    const [selected, setSelected] = useState(0);
    const mainImage = product.images[selected] ?? null;
    const inStock = product.quantity > 0;

    return (
        <MainLayout>
            <Head title={product.title} />

            <nav className="mb-4 flex flex-wrap items-center gap-2 text-sm text-base-content/70">
                <Link href={route("products.index")} className="hover:text-primary">
                    Shop
                </Link>
                <span aria-hidden="true">›</span>
                <span>{product.category}</span>
                <span aria-hidden="true">›</span>
                <span className="text-base-content">{product.title}</span>
            </nav>

            <div className="grid gap-8 lg:grid-cols-2">
                <div>
                    <div className="card bg-base-100 shadow-sm">
                        <figure className="aspect-square bg-base-200">
                            {mainImage ? (
                                <img
                                    src={mainImage}
                                    alt={product.title}
                                    className="h-full w-full object-contain"
                                />
                            ) : (
                                <span className="text-sm text-base-content/50">No image</span>
                            )}
                        </figure>
                    </div>

                    {product.images.length > 1 && (
                        <div className="mt-4 flex gap-2 overflow-x-auto">
                            {product.images.map((src, index) => (
                                <button
                                    key={src}
                                    type="button"
                                    onClick={() => setSelected(index)}
                                    className={`h-20 w-20 shrink-0 overflow-hidden rounded-box border-2 ${
                                        index === selected ? "border-primary" : "border-transparent"
                                    }`}
                                >
                                    <img
                                        src={src}
                                        alt={`${product.title} ${index + 1}`}
                                        className="h-full w-full object-cover"
                                    />
                                </button>
                            ))}
                        </div>
                    )}
                </div>

                <div className="space-y-4">
                    <p className="text-xs uppercase text-base-content/60">{product.category}</p>
                    <h1 className="text-3xl font-bold">{product.title}</h1>
                    <p className="text-2xl font-semibold">${product.price}</p>

                    <div className={`badge ${inStock ? "badge-success" : "badge-error"}`}>
                        {inStock ? `${product.quantity} in stock` : "Out of stock"}
                    </div>

                    <p className="whitespace-pre-line text-base-content/80">
                        {product.description ?? "No description yet."}
                    </p>

                    <button type="button" className="btn btn-primary" disabled={!inStock}>
                        Add to cart
                    </button>
                    <p className="text-xs text-base-content/50">
                        The cart is the next chapter, so this button doesn't do anything yet.
                    </p>
                </div>
            </div>
        </MainLayout>
    );
}
