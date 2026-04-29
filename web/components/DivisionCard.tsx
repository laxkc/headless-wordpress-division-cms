import Link from "next/link";
import type { Division } from "@/lib/types";

export function DivisionCard({ division }: { division: Division }) {
  const accent = division.accent_color ?? "var(--color-accent)";
  return (
    <Link
      href={`/divisions/${division.slug}`}
      className="group block rounded-2xl border border-border bg-surface p-6 transition-shadow hover:shadow-lg"
    >
      <div
        aria-hidden
        className="h-1 w-12 rounded-full"
        style={{ background: accent }}
      />
      <h3 className="mt-4 text-xl font-semibold tracking-tight">
        {division.title}
      </h3>
      <p className="mt-2 text-sm text-muted line-clamp-3">
        {division.short_description}
      </p>
      <p className="mt-6 text-xs font-medium uppercase tracking-wider text-muted transition-colors group-hover:text-text">
        Explore →
      </p>
    </Link>
  );
}
