import Link from "next/link";
import { Container } from "@/components/Container";

export default function NotFound() {
  return (
    <Container className="py-32">
      <p className="text-xs uppercase tracking-[0.2em] text-muted">
        404
      </p>
      <h1 className="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">
        We could not find that page.
      </h1>
      <p className="mt-5 max-w-2xl text-lg text-muted">
        The link may be outdated, or the content has moved. Try one of these
        instead:
      </p>
      <div className="mt-8 flex flex-wrap gap-3">
        <Link
          href="/"
          className="inline-flex items-center rounded-full bg-accent px-5 py-2.5 text-sm font-medium text-white hover:bg-accent-strong transition-colors"
        >
          Home
        </Link>
        <Link
          href="/divisions"
          className="inline-flex items-center rounded-full border border-border px-5 py-2.5 text-sm font-medium hover:border-foreground transition-colors"
        >
          Divisions
        </Link>
        <Link
          href="/insights"
          className="inline-flex items-center rounded-full border border-border px-5 py-2.5 text-sm font-medium hover:border-foreground transition-colors"
        >
          Insights
        </Link>
        <Link
          href="/search"
          className="inline-flex items-center rounded-full border border-border px-5 py-2.5 text-sm font-medium hover:border-foreground transition-colors"
        >
          Search
        </Link>
      </div>
    </Container>
  );
}
