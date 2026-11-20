import { Link, usePage } from "@inertiajs/react";
import { PageProps } from "@/types";

export default function Navbar() {
    const { auth, cartCount } = usePage<PageProps>().props;

    return (
        <div className="navbar bg-base-100 shadow-sm">
            <div className="navbar-start gap-2">
                <Link href="/" className="btn btn-ghost text-xl">
                    Larastore
                </Link>
                <Link href={route("products.index")} className="btn btn-ghost btn-sm">
                    Shop
                </Link>
            </div>
            <div className="navbar-end gap-2">
                {auth.user && (
                    <Link href={route("vendor.apply")} className="btn btn-ghost btn-sm">
                        Sell
                    </Link>
                )}
                {auth.user && (
                    <Link href={route("orders.index")} className="btn btn-ghost btn-sm">
                        Orders
                    </Link>
                )}

                <Link href={route("cart.index")} className="btn btn-ghost btn-sm">
                    Cart ({cartCount})
                </Link>

                {auth.roles.includes("vendor") && (
                    <a href="/vendor" className="btn btn-ghost btn-sm">
                        Seller
                    </a>
                )}

                {auth.roles.includes("admin") && (
                    <a href="/admin" className="btn btn-ghost btn-sm">
                        Admin
                    </a>
                )}

                {auth.user ? (
                    <>
                        <span className="text-sm">{auth.user.name}</span>
                        <Link
                            href={route("logout")}
                            method="post"
                            as="button"
                            className="btn btn-sm"
                        >
                            Logout
                        </Link>
                    </>
                ) : (
                    <>
                        <Link href={route("login")} className="btn btn-ghost btn-sm">
                            Login
                        </Link>
                        <Link href={route("register")} className="btn btn-primary btn-sm">
                            Register
                        </Link>
                    </>
                )}
            </div>
        </div>
    );
}
