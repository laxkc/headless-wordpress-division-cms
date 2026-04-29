import type { Metadata } from "next";
import { Inter, Manrope } from "next/font/google";
import "./globals.css";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { env } from "@/lib/env";

// Body workhorse — designed for UI / screens, neutral and trustworthy.
const inter = Inter({
  variable: "--font-sans",
  subsets: ["latin"],
  display: "swap",
});

// Display face for headings — geometric humanist, premium feel,
// distinct silhouette so the Northium brand reads as intentional.
const manrope = Manrope({
  variable: "--font-display",
  subsets: ["latin"],
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL(env.SITE_URL),
  title: {
    default: "Northium Financial",
    template: "%s — Northium",
  },
  description:
    "Independent, fee-only financial advice for professionals, families, and founders. Wealth, tax, family CFO, founder equity, and our Studio research desk.",
  openGraph: {
    type: "website",
    siteName: "Northium Financial",
  },
  twitter: {
    card: "summary_large_image",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="en"
      suppressHydrationWarning
      className={`${inter.variable} ${manrope.variable} h-full antialiased`}
    >
      <body suppressHydrationWarning className="min-h-full flex flex-col">
        <Header />
        <main className="flex-1">{children}</main>
        <Footer />
      </body>
    </html>
  );
}
