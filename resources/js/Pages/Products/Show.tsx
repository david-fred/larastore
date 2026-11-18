import { Head, Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";
import { PageProps } from "@/types";

type Option = { id: number; name: string };
type VariationType = { id: number; name: string; options: Option[] };
type Variant = { id: number; label: string; price: string; quantity: number; option_ids: number[] };
type ProductImage = { url: string; option_id: number | null };

type ProductDetail = {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    price: string;
    category: string;
    categorySlug: string;
    images: ProductImage[];
    variationTypes: VariationType[];
    variants: Variant[];
};

// Maps each variation type to the option a variant uses, e.g. { colorTypeId: silverId }
function selectionFor(variant: Variant | undefined, types: VariationType[]): Record<number, number> {
    const selection: Record<number, number> = {};

    types.forEach((type) => {
        const match = type.options.find((option) => variant?.option_ids.includes(option.id));

        if (match) {
            selection[type.id] = match.id;
        }
    });

    return selection;
}

export default function Show({ product }: { product: ProductDetail }) {
    const { errors } = usePage<PageProps & { errors: Record<string, string> }>().props;

    const [selectedImage, setSelectedImage] = useState(0);

    const firstAvailable = product.variants.find((v) => v.quantity > 0) ?? product.variants[0];

    const [selection, setSelection] = useState<Record<number, number>>(() =>
        selectionFor(firstAvailable, product.variationTypes),
    );

    // The variant that uses every selected option
    const selectedIds = Object.values(selection);
    const variant = product.variants.find(
        (v) =>
            v.option_ids.length === selectedIds.length &&
            selectedIds.every((id) => v.option_ids.includes(id)),
    );

    const inStock = (variant?.quantity ?? 0) > 0;

    // Images tagged with a selected option win, then general images, then everything
    const tagged = product.images.filter((img) => img.option_id !== null && selectedIds.includes(img.option_id));
    const general = product.images.filter((img) => img.option_id === null);
    const gallery = tagged.length > 0 ? tagged : general.length > 0 ? general : product.images;
    const mainImage = gallery[selectedImage]?.url ?? null;

    const choose = (typeId: number, optionId: number) => {
        setSelection((prev) => ({ ...prev, [typeId]: optionId }));
        setSelectedImage(0);
    };

    const addToCart = () => {
        if (!variant) return;

        router.post(route("cart.store"), { variant_id: variant.id, quantity: 1 }, { preserveScroll: true });
    };

    return (
        <MainLayout>
            <Head title={product.title} />

            <nav className="mb-4 flex flex-wrap items-center gap-2 text-sm text-base-content/70">
                <Link href={route("products.index")} className="hover:text-primary">
                    Shop
                </Link>
                <span aria-hidden="true">›</span>
                <Link href={route("categories.show", product.categorySlug)} className="hover:text-primary">
                    {product.category}
                </Link>
                <span aria-hidden="true">›</span>
                <span className="text-base-content">{product.title}</span>
            </nav>

            <div className="grid gap-8 lg:grid-cols-2">
                <div>
                    <div className="card bg-base-100 shadow-sm">
                        <figure className="aspect-square bg-base-200">
                            {mainImage ? (
                                <img src={mainImage} alt={product.title} className="h-full w-full object-contain" />
                            ) : (
                                <span className="text-sm text-base-content/50">No image</span>
                            )}
                        </figure>
                    </div>

                    {gallery.length > 1 && (
                        <div className="mt-4 flex gap-2 overflow-x-auto">
                            {gallery.map((img, index) => (
                                <button
                                    key={img.url}
                                    type="button"
                                    onClick={() => setSelectedImage(index)}
                                    className={`h-20 w-20 shrink-0 overflow-hidden rounded-box border-2 ${
                                        index === selectedImage ? "border-primary" : "border-transparent"
                                    }`}
                                >
                                    <img src={img.url} alt={`${product.title} ${index + 1}`} className="h-full w-full object-cover" />
                                </button>
                            ))}
                        </div>
                    )}
                </div>

                <div className="space-y-5">
                    <p className="text-xs uppercase text-base-content/60">{product.category}</p>
                    <h1 className="text-3xl font-bold">{product.title}</h1>

                    {variant ? (
                        <p className="text-2xl font-semibold">${Number(variant.price).toFixed(2)}</p>
                    ) : (
                        <p className="text-error">That combination isn't available.</p>
                    )}

                    {product.variationTypes.map((type) => (
                        <div key={type.id} className="space-y-2">
                            <p className="text-sm font-semibold">{type.name}</p>
                            <div className="flex flex-wrap gap-2">
                                {type.options.map((option) => (
                                    <button
                                        key={option.id}
                                        type="button"
                                        onClick={() => choose(type.id, option.id)}
                                        className={`btn btn-sm ${
                                            selection[type.id] === option.id ? "btn-primary" : "btn-outline"
                                        }`}
                                    >
                                        {option.name}
                                    </button>
                                ))}
                            </div>
                        </div>
                    ))}

                    <div className={`badge ${inStock ? "badge-success" : "badge-error"}`}>
                        {variant ? (inStock ? `${variant.quantity} in stock` : "Out of stock") : "Unavailable"}
                    </div>

                    <p className="whitespace-pre-line text-base-content/80">
                        {product.description ?? "No description yet."}
                    </p>

                    <button type="button" className="btn btn-primary" disabled={!inStock} onClick={addToCart}>
                        Add to cart
                    </button>

                    {errors.quantity && <p className="text-sm text-error">{errors.quantity}</p>}
                </div>
            </div>
        </MainLayout>
    );
}
