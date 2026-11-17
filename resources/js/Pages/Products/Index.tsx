import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

type ProductCard = {
    id: number;
    title: string;
    slug: string;
    price: string;
    category: string;
    image: string | null;
};

type PaginatedProducts = {
    data: ProductCard[];
    links: { url: string | null; label: string; active: boolean }[];
};

export default function Index({ products }: { products: PaginatedProducts }) {
    return (
        <MainLayout>
            <Head title="Shop" />

            <h1 className="mb-6 text-3xl font-bold">Shop</h1>

            {products.data.length === 0 ? (
                <p>No products yet.</p>
            ) : (
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {products.data.map((product) => (
                        <Link
                            key={product.id}
                            href={route("products.show", product.slug)}
                            className="card bg-base-100 shadow-sm transition hover:shadow-md"
                        >
                            <figure className="aspect-square bg-base-200">
                                {product.image ? (
                                    <img
                                        src={product.image}
                                        alt={product.title}
                                        className="h-full w-full object-cover"
                                    />
                                ) : (
                                    <span className="text-sm text-base-content/50">No image</span>
                                )}
                            </figure>
                            <div className="card-body">
                                <p className="text-xs uppercase text-base-content/60">
                                    {product.category}
                                </p>
                                <h2 className="card-title">{product.title}</h2>
                                <p className="text-lg font-semibold">${product.price}</p>
                            </div>
                        </Link>
                    ))}
                </div>
            )}

            <div className="mt-8 flex flex-wrap justify-center gap-2">
                {products.links.map((link, index) =>
                    link.url ? (
                        <Link
                            key={index}
                            href={link.url}
                            preserveScroll
                            className={`btn btn-sm ${link.active ? "btn-primary" : "btn-ghost"}`}
                            // Laravel labels contain HTML entities such as &laquo;
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ) : (
                        <span
                            key={index}
                            className="btn btn-sm btn-disabled"
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    )
                )}
            </div>
        </MainLayout>
    );
}
