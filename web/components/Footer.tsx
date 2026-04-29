import Link from "next/link";
import { Container } from "./Container";

const DIVISIONS = [
  { slug: "wealth-planning", label: "Wealth Planning" },
  { slug: "tax-and-estate", label: "Tax & Estate" },
  { slug: "family-cfo", label: "Family CFO" },
  { slug: "founder-and-equity", label: "Founder & Equity" },
  { slug: "studio", label: "Studio" },
];

const COPYRIGHT_YEAR = 2026;

export function Footer() {
  return (
    <footer className="border-t border-border mt-24 py-12 text-sm text-muted">
      <Container className="grid gap-8 sm:grid-cols-3">
        <div>
          <p className="font-display text-base font-semibold text-foreground">
            Northium
          </p>
          <p className="mt-2 max-w-xs">
            Independent, fee-only financial advice. Built by ex-bank advisors
            who got tired of opaque fees and product pushing.
          </p>
        </div>
        <div>
          <p className="text-xs uppercase tracking-wider text-foreground">
            Practices
          </p>
          <ul className="mt-3 space-y-1.5">
            {DIVISIONS.map((d) => (
              <li key={d.slug}>
                <Link
                  href={`/divisions/${d.slug}`}
                  className="hover:text-foreground transition-colors"
                >
                  {d.label}
                </Link>
              </li>
            ))}
          </ul>
        </div>
        <div>
          <p className="text-xs uppercase tracking-wider text-foreground">
            Firm
          </p>
          <ul className="mt-3 space-y-1.5">
            <li>
              <Link href="/insights" className="hover:text-foreground">
                Insights
              </Link>
            </li>
            <li>
              <Link href="/advisors" className="hover:text-foreground">
                Advisors
              </Link>
            </li>
            <li>
              <Link href="/search" className="hover:text-foreground">
                Search
              </Link>
            </li>
          </ul>
        </div>
      </Container>
      <Container className="mt-12 text-xs">
        © {COPYRIGHT_YEAR} Northium Financial · Fee-only · Independent
      </Container>
    </footer>
  );
}
