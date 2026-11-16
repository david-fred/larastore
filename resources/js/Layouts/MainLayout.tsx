import { PropsWithChildren } from "react";
import Navbar from "@/Components/Navbar";

export default function MainLayout({ children }: PropsWithChildren) {
    return (
        <div className="min-h-screen bg-base-200">
            <Navbar />
            <main className="container mx-auto p-6">{children}</main>
        </div>
    );
}
