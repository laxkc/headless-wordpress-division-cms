import Link from "next/link";
import type { Advisor } from "@/lib/types";

export function AdvisorCard({ advisor }: { advisor: Advisor }) {
  const initials = advisor.name
    .split(" ")
    .map((part) => part[0])
    .filter(Boolean)
    .slice(0, 2)
    .join("");

  return (
    <Link
      href={`/advisors/${advisor.slug}`}
      className="group flex items-center gap-4 rounded-2xl border border-border bg-surface p-4 transition-shadow hover:shadow-lg"
    >
      <div
        aria-hidden
        className="flex h-12 w-12 flex-none items-center justify-center rounded-full bg-accent/15 text-sm font-semibold text-accent-strong"
      >
        {initials}
      </div>
      <div className="min-w-0 flex-1">
        <p className="truncate font-medium group-hover:text-accent-strong transition-colors">
          {advisor.name}
        </p>
        <p className="truncate text-xs text-muted">
          {advisor.role}
        </p>
      </div>
    </Link>
  );
}
