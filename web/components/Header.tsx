import Link from "next/link";
import { Container } from "./Container";

const NAV = [
  { href: "/divisions", label: "Practices" },
  { href: "/advisors", label: "Advisors" },
  { href: "/insights", label: "Insights" },
];

export function Header() {
  return (
    <header className="border-b border-border bg-surface/80 backdrop-blur-sm sticky top-0 z-10">
      <Container className="flex items-center justify-between py-4">
        <Link
          href="/"
          className="font-display text-lg font-semibold tracking-tight"
        >
          Northium
        </Link>
        <nav className="flex items-center gap-6 text-sm text-muted">
          {NAV.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className="hover:text-foreground transition-colors"
            >
              {item.label}
            </Link>
          ))}
          <Link
            href="/search"
            className="rounded-full border border-border px-3 py-1 text-xs hover:border-foreground transition-colors"
          >
            Search
          </Link>
        </nav>
      </Container>
    </header>
  );
}
